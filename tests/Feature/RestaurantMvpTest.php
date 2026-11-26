<?php

namespace Tests\Feature;

use App\Livewire\Restaurant\KitchenDisplay;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RestaurantMvpTest extends TestCase
{
    use RefreshDatabase;

    public function test_waiter_creates_a_table_order_with_product_station_snapshots(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter']);
        $table = $this->createTable();
        $coffee = $this->createProduct('Café', 'bar', '7.50');
        $sandwich = $this->createProduct('Sándwich', 'kitchen', '18.00');

        $this->actingAs($waiter)
            ->post(route('comandas.orders.store'), [
                'service_type' => 'dine_in',
                'table_id' => $table->id,
                'items' => [
                    $coffee->id => 2,
                    $sandwich->id => 1,
                ],
            ])
            ->assertRedirect(route('comandas.index'));

        $order = Order::with('items')->firstOrFail();
        $this->assertSame('33.00', $order->total);
        $this->assertSame('pending', $order->status);
        $this->assertSame('Café x2, Sándwich x1', $order->nombre_plato);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $coffee->id,
            'station' => 'bar',
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $sandwich->id,
            'station' => 'kitchen',
            'quantity' => 1,
        ]);

        $this->actingAs($waiter)
            ->get(route('comandas.index'))
            ->assertOk()
            ->assertSee('Mapa de mesas')
            ->assertSee('Ocupada');

        $administrator = User::factory()->create(['role' => 'administrator']);
        $this->actingAs($administrator)
            ->delete(route('admin.mesas.destroy', $table))
            ->assertSessionHasErrors('table');

        $this->actingAs($waiter)
            ->post(route('comandas.orders.store'), [
                'service_type' => 'dine_in',
                'table_id' => $table->id,
                'items' => [$coffee->id => 1],
            ])
            ->assertSessionHasErrors('table_id');
    }

    public function test_kds_filters_by_station_and_advances_items_until_order_is_ready(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter']);
        $table = $this->createTable();
        $coffee = $this->createProduct('Latte', 'bar', '9.00');
        $meal = $this->createProduct('Waffle', 'kitchen', '22.00');

        $this->actingAs($waiter)->post(route('comandas.orders.store'), [
            'service_type' => 'dine_in',
            'table_id' => $table->id,
            'items' => [$coffee->id => 1, $meal->id => 1],
        ])->assertRedirect();
        $order = Order::with('items')->firstOrFail();
        $barItem = $order->items->firstWhere('station', 'bar');
        $kitchenItem = $order->items->firstWhere('station', 'kitchen');

        $barista = User::factory()->create(['role' => 'barista']);
        $this->actingAs($barista)
            ->get(route('kds.index'))
            ->assertOk()
            ->assertSee('Latte')
            ->assertDontSee('Waffle');

        Livewire::test(KitchenDisplay::class)
            ->call('advance', $barItem->id)
            ->assertSee('En preparación');
        Livewire::test(KitchenDisplay::class)
            ->call('advance', $barItem->id);

        $cook = User::factory()->create(['role' => 'cook']);
        $this->actingAs($cook)
            ->get(route('kds.index'))
            ->assertOk()
            ->assertSee('Waffle')
            ->assertDontSee('Latte');

        Livewire::test(KitchenDisplay::class)
            ->call('advance', $kitchenItem->id)
            ->call('advance', $kitchenItem->id);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'ready']);

        $this->actingAs($waiter)
            ->post(route('comandas.orders.served', $order))
            ->assertRedirect(route('comandas.index'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'served']);
    }

    public function test_cashier_records_split_tender_and_closes_cash_with_reconciliation(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier_bar']);
        $product = $this->createProduct('Torta', 'kitchen', '20.00');

        $this->actingAs($cashier)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertSee('Caja cerrada');

        $this->actingAs($cashier)
            ->post(route('pos.cash.open'), ['opening_amount' => '50.00'])
            ->assertRedirect(route('pos.index'));

        $this->actingAs($cashier)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertSee('Nueva venta')
            ->assertSee('Torta');

        $this->actingAs($cashier)
            ->post(route('pos.orders.store'), [
                'service_type' => 'takeaway',
                'document_type' => 'factura',
                'customer_document' => '20123456789',
                'items' => [$product->id => 1],
            ])
            ->assertRedirect(route('pos.index'));

        $order = Order::firstOrFail();
        $this->assertSame('factura', $order->document_type);
        $this->assertSame('20123456789', $order->customer_document);
        $this->assertNull($order->table_id);

        $this->actingAs($cashier)
            ->post(route('pos.orders.payments.store', $order), [
                'method' => 'cash',
                'amount' => '8.00',
            ])
            ->assertRedirect(route('pos.index'));

        $this->actingAs($cashier)
            ->post(route('pos.orders.payments.store', $order), [
                'method' => 'yape',
                'amount' => '13.00',
            ])
            ->assertSessionHasErrors('amount');

        $this->actingAs($cashier)
            ->post(route('pos.orders.payments.store', $order), [
                'method' => 'yape',
                'amount' => '12.00',
            ])
            ->assertRedirect(route('pos.index'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'metodo_pago' => 'Mixto']);
        $this->assertDatabaseCount('order_payments', 2);

        $administrator = User::factory()->create(['role' => 'administrator']);
        $this->actingAs($administrator)
            ->delete(route('admin.orden.destroy', $order))
            ->assertSessionHasErrors('order');

        $this->actingAs($cashier)
            ->post(route('pos.cash.close'), ['counted_cash' => '58.00'])
            ->assertRedirect(route('pos.index'));

        $session = CashSession::firstOrFail();
        $this->assertSame('closed', $session->status);
        $this->assertSame('58.00', $session->expected_cash);
        $this->assertSame('0.00', $session->difference);

        $this->actingAs($cashier)
            ->get(route('pos.index'))
            ->assertOk()
            ->assertSee('Último arqueo')
            ->assertSee('Efectivo contado');
    }

    public function test_only_assigned_staff_can_open_pos_kds_and_menu(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter']);
        $barista = User::factory()->create(['role' => 'barista']);
        $cashier = User::factory()->create(['role' => 'cashier_bar']);

        $this->actingAs($waiter)->get(route('pos.index'))->assertForbidden();
        $this->actingAs($barista)->get(route('pos.index'))->assertForbidden();
        $this->actingAs($waiter)->get(route('kds.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('kds.index'))->assertForbidden();
        $this->actingAs($waiter)->get(route('admin.menu.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('admin.menu.index'))->assertForbidden();
        $this->actingAs($cashier)->post(route('admin.orden.store'), [])->assertForbidden();
    }

    public function test_customer_with_order_history_cannot_be_deleted(): void
    {
        $administrator = User::factory()->create(['role' => 'administrator']);
        $customer = Customer::create([
            'name' => 'Cliente',
            'last_name' => 'Con historial',
            'email' => 'history-customer@example.test',
        ]);
        $table = $this->createTable();
        Order::create([
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'nombre_plato' => 'Venta histórica',
            'total' => '12.00',
            'metodo_pago' => 'Efectivo',
        ]);

        $this->actingAs($administrator)
            ->delete(route('admin.customers.destroy', $customer))
            ->assertSessionHasErrors('customer');

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_administrator_can_manage_the_empty_product_catalog(): void
    {
        $administrator = User::factory()->create(['role' => 'administrator']);

        $this->actingAs($administrator)
            ->get(route('admin.menu.index'))
            ->assertOk()
            ->assertSee('Aún no hay productos');

        $this->actingAs($administrator)
            ->post(route('admin.menu.store'), [
                'name' => 'Café filtrado',
                'category' => 'Bebidas',
                'price' => '8.50',
                'station' => 'bar',
                'active' => '1',
            ])
            ->assertRedirect(route('admin.menu.index'));

        $product = Product::firstOrFail();
        $this->actingAs($administrator)
            ->get(route('admin.menu.index'))
            ->assertOk()
            ->assertSee('Café filtrado');

        $this->actingAs($administrator)
            ->put(route('admin.menu.update', $product), [
                'name' => 'Café de especialidad',
                'category' => 'Cafetería',
                'price' => '10.00',
                'station' => 'bar',
            ])
            ->assertRedirect(route('admin.menu.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Café de especialidad',
            'active' => false,
        ]);

        $this->actingAs($administrator)
            ->delete(route('admin.menu.destroy', $product))
            ->assertRedirect(route('admin.menu.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    private function createProduct(string $name, string $station, string $price): Product
    {
        return Product::create([
            'name' => $name,
            'price' => $price,
            'station' => $station,
            'active' => true,
        ]);
    }

    private function createTable(): Table
    {
        return Table::create([
            'nro_mesas' => 1,
            'capacidad' => 4,
            'disponible' => true,
        ]);
    }
}
