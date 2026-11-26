<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantOrderRequest;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use App\Services\OrderCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PointOfSaleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $cashSession = $this->openSessionFor($user->id);
        $cashSession?->load('payments');
        $lastClosedSession = CashSession::query()
            ->where('opened_by', $user->id)
            ->where('status', 'closed')
            ->with('payments')
            ->latest('closed_at')
            ->first();

        return view('restaurant.pos.index', [
            'cashSession' => $cashSession,
            'lastClosedSession' => $lastClosedSession,
            'products' => Product::query()->where('active', true)->orderBy('category')->orderBy('name')->get(),
            'customers' => Customer::query()->orderBy('name')->get(),
            'tables' => Table::query()
                ->where('disponible', true)
                ->whereDoesntHave('orders', fn ($query) => $query->whereNotIn('status', ['completed', 'cancelled']))
                ->orderBy('nro_mesas')
                ->get(),
            'orders' => Order::query()
                ->with(['customer', 'table', 'items', 'payments'])
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->latest()
                ->limit(30)
                ->get(),
        ]);
    }

    public function openCashSession(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);
        $user = $request->user();

        DB::transaction(function () use ($user, $validated): void {
            User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($this->openSessionFor($user->id)) {
                throw ValidationException::withMessages([
                    'cash_session' => 'Ya tienes una caja abierta.',
                ]);
            }

            CashSession::create([
                'opened_by' => $user->id,
                'opening_amount' => $validated['opening_amount'],
                'status' => 'open',
                'opened_at' => now(),
            ]);
        });

        return redirect()->route('pos.index')->with('success', 'Caja abierta.');
    }

    public function closeCashSession(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'counted_cash' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $session = CashSession::query()
                ->where('opened_by', $request->user()->id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->firstOrFail();
            $cashPaymentsCents = (int) round((float) $session->payments()->where('method', 'cash')->sum('amount') * 100);
            $openingCents = (int) round((float) $session->opening_amount * 100);
            $countedCents = (int) round((float) $validated['counted_cash'] * 100);
            $expectedCents = $openingCents + $cashPaymentsCents;

            $session->update([
                'closed_by' => $request->user()->id,
                'expected_cash' => number_format($expectedCents / 100, 2, '.', ''),
                'counted_cash' => number_format($countedCents / 100, 2, '.', ''),
                'difference' => number_format(($countedCents - $expectedCents) / 100, 2, '.', ''),
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        });

        return redirect()->route('pos.index')->with('success', 'Caja cerrada y arqueada.');
    }

    public function storeOrder(
        StoreRestaurantOrderRequest $request,
        OrderCreationService $orderCreation
    ): RedirectResponse {
        if (! $this->openSessionFor($request->user()->id)) {
            return back()->withErrors([
                'cash_session' => 'Abre una caja antes de registrar una venta.',
            ])->withInput();
        }

        $data = $request->validated();
        $data['document_type'] ??= 'boleta';
        $order = $orderCreation->create($data, $request->user());

        return redirect()->route('pos.index')->with('success', "Venta #{$order->id} registrada. Puedes cobrarla en esta pantalla.");
    }

    public function storePayment(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', 'in:cash,yape,plin,card,transfer'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
        ]);
        $user = $request->user();

        DB::transaction(function () use ($order, $user, $validated): void {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $session = CashSession::query()
                ->where('opened_by', $user->id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if (! $session) {
                throw ValidationException::withMessages([
                    'cash_session' => 'Abre una caja para registrar cobros.',
                ]);
            }

            if (in_array($lockedOrder->status, ['completed', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'amount' => 'Esta orden ya está cerrada.',
                ]);
            }

            $paidCents = (int) round((float) $lockedOrder->payments()->sum('amount') * 100);
            $totalCents = (int) round((float) $lockedOrder->total * 100);
            $amountCents = (int) round((float) $validated['amount'] * 100);

            if ($amountCents > $totalCents - $paidCents) {
                throw ValidationException::withMessages([
                    'amount' => 'El cobro supera el saldo pendiente.',
                ]);
            }

            $lockedOrder->payments()->create([
                'cash_session_id' => $session->id,
                'recorded_by' => $user->id,
                'method' => $validated['method'],
                'amount' => number_format($amountCents / 100, 2, '.', ''),
                'paid_at' => now(),
            ]);

            $methods = $lockedOrder->payments()->distinct()->pluck('method')->all();
            $lockedOrder->metodo_pago = count($methods) > 1
                ? 'Mixto'
                : $this->paymentMethodLabel($methods[0]);

            if (
                $lockedOrder->status === 'served'
                && $paidCents + $amountCents >= $totalCents
            ) {
                $lockedOrder->status = 'completed';
            }

            $lockedOrder->save();
        });

        return redirect()->route('pos.index')->with('success', 'Cobro registrado.');
    }

    private function openSessionFor(int $userId): ?CashSession
    {
        return CashSession::query()
            ->where('opened_by', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();
    }

    private function paymentMethodLabel(string $method): string
    {
        return [
            'cash' => 'Efectivo',
            'yape' => 'Yape',
            'plin' => 'Plin',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
        ][$method] ?? 'Otro';
    }
}
