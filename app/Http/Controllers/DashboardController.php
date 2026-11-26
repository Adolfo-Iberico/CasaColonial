<?php

namespace App\Http\Controllers;

use App\Models\Buy;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now();
        $monthStart = $today->copy()->startOfMonth();
        $sixMonthsStart = $monthStart->copy()->subMonths(5);

        $ordersThisMonth = Order::whereBetween('created_at', [$monthStart, $today]);
        $salesThisMonth = (float) (clone $ordersThisMonth)->sum('total');
        $ordersToday = (clone $ordersThisMonth)
            ->whereDate('created_at', $today->toDateString())
            ->count();

        $salesByMonth = Order::whereBetween('created_at', [$sixMonthsStart, $today])
            ->get(['created_at', 'total'])
            ->groupBy(fn (Order $order) => $order->created_at->format('Y-m'))
            ->map(fn ($orders) => (float) $orders->sum('total'));

        $purchasesByMonth = Buy::whereBetween('date', [
            $sixMonthsStart->toDateString(),
            $today->toDateString(),
        ])
            ->get(['date', 'total'])
            ->groupBy(fn (Buy $buy) => $buy->date)
            ->map(fn ($buys) => (float) $buys->sum('total'));

        $monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $monthlyData = [];

        for ($offset = 0; $offset < 6; $offset++) {
            $month = $sixMonthsStart->copy()->addMonths($offset);
            $key = $month->format('Y-m');
            $purchaseTotal = (float) $purchasesByMonth
                ->filter(fn ($total, $date) => str_starts_with($date, $key))
                ->sum();

            $monthlyData[] = [
                'label' => $monthNames[(int) $month->format('n') - 1],
                'sales' => $salesByMonth->get($key, 0),
                'purchases' => $purchaseTotal,
            ];
        }

        $maxMonthlyTotal = max(
            1,
            collect($monthlyData)->max(fn (array $month) => max($month['sales'], $month['purchases']))
        );

        foreach ($monthlyData as &$month) {
            $month['sales_percent'] = $month['sales'] > 0
                ? max(8, (int) round($month['sales'] / $maxMonthlyTotal * 100))
                : 2;
            $month['purchases_percent'] = $month['purchases'] > 0
                ? max(8, (int) round($month['purchases'] / $maxMonthlyTotal * 100))
                : 2;
        }
        unset($month);

        $paymentMethods = Order::query()
            ->selectRaw('metodo_pago, COUNT(*) as total')
            ->groupBy('metodo_pago')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($method) => [
                'name' => $method->metodo_pago ?: 'Sin especificar',
                'total' => (int) $method->total,
            ]);
        $maxPaymentTotal = max(1, (int) $paymentMethods->max('total'));
        $paymentMethods = $paymentMethods->map(function (array $method) use ($maxPaymentTotal): array {
            $method['percent'] = (int) round($method['total'] / $maxPaymentTotal * 100);

            return $method;
        });

        $popularDishes = Order::query()
            ->selectRaw('nombre_plato, COUNT(*) as total')
            ->groupBy('nombre_plato')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($dish) => [
                'name' => $dish->nombre_plato,
                'total' => (int) $dish->total,
            ]);
        $maxDishTotal = max(1, (int) $popularDishes->max('total'));
        $popularDishes = $popularDishes->map(function (array $dish) use ($maxDishTotal): array {
            $dish['percent'] = (int) round($dish['total'] / $maxDishTotal * 100);

            return $dish;
        });

        $recentOrders = Order::with(['customer', 'table'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'salesThisMonth' => $salesThisMonth,
            'ordersToday' => $ordersToday,
            'customerCount' => Customer::count(),
            'availableTables' => Table::where('disponible', true)->count(),
            'tableCount' => Table::count(),
            'monthlyData' => $monthlyData,
            'paymentMethods' => $paymentMethods,
            'popularDishes' => $popularDishes,
            'recentOrders' => $recentOrders,
        ]);
    }
}
