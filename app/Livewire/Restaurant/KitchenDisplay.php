<?php

namespace App\Livewire\Restaurant;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class KitchenDisplay extends Component
{
    public function advance(int $itemId): void
    {
        $user = auth()->user();
        abort_unless($user->hasPermission('kds.update'), 403);

        DB::transaction(function () use ($itemId, $user): void {
            $item = OrderItem::query()->whereKey($itemId)->lockForUpdate()->firstOrFail();
            $this->authorizeStation($user, $item->station);
            $order = Order::query()->whereKey($item->order_id)->lockForUpdate()->firstOrFail();

            if (! in_array($item->status, ['pending', 'preparing'], true)) {
                throw ValidationException::withMessages([
                    'item' => 'Este producto ya no tiene acciones pendientes en cocina.',
                ]);
            }

            if ($item->status === 'pending') {
                $item->update([
                    'status' => 'preparing',
                    'started_at' => now(),
                ]);
            } else {
                $item->update([
                    'status' => 'ready',
                    'ready_at' => now(),
                ]);
            }

            $statuses = $order->items()->pluck('status');

            if ($statuses->every(fn (string $status): bool => $status === 'ready')) {
                $order->status = 'ready';
            } elseif ($statuses->contains('preparing')) {
                $order->status = 'preparing';
            } else {
                $order->status = 'pending';
            }

            $order->save();
        });
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user->hasPermission('kds.view'), 403);
        $stations = $this->stationsFor($user);

        $items = OrderItem::query()
            ->with(['order.table'])
            ->whereIn('station', $stations)
            ->whereIn('status', ['pending', 'preparing'])
            ->whereHas('order', fn ($query) => $query->whereNotIn('status', ['completed', 'cancelled']))
            ->oldest()
            ->get();

        return view('livewire.restaurant.kitchen-display', [
            'itemsByStation' => $items->groupBy('station'),
        ]);
    }

    private function authorizeStation(User $user, string $station): void
    {
        abort_unless(in_array($station, $this->stationsFor($user), true), 403);
    }

    private function stationsFor(User $user): array
    {
        if ($user->isAdmin()) {
            return ['bar', 'kitchen'];
        }

        return match ($user->role) {
            'barista' => ['bar'],
            'cook', 'kitchen_assistant' => ['kitchen'],
            default => [],
        };
    }
}
