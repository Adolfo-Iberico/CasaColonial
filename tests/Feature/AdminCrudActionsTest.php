<?php

namespace Tests\Feature;

use App\Models\Buy;
use App\Models\BuyDetails;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCrudActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_records_can_be_updated_from_their_module_routes(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $supplier = Supplier::create([
            'ruc' => '12345678901',
            'razonsocial' => 'Proveedor Original',
            'address' => 'Dirección original',
            'phone' => '987654321',
            'email' => 'supplier@example.test',
        ]);
        $customer = Customer::create([
            'name' => 'Nombre original',
            'last_name' => 'Apellido original',
            'phone' => '987654321',
            'email' => 'customer@example.test',
            'address' => 'Dirección original',
            'date_registered' => '2025-01-01',
        ]);
        $table = Table::create([
            'nro_mesas' => 1,
            'capacidad' => 2,
            'disponible' => true,
        ]);
        $buy = Buy::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
            'total' => 20,
            'date' => '2025-01-01',
        ]);
        $detail = BuyDetails::create([
            'buy_id' => $buy->id,
            'name' => 'Producto original',
            'cantidad' => 1,
            'precio_unitario' => 20,
            'subtotal' => 20,
        ]);
        $order = Order::create([
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'nombre_plato' => 'Plato original',
            'total' => 20,
            'metodo_pago' => 'Efectivo',
        ]);

        $this->actingAs($user)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('sweetalert2@11');
        $this->actingAs($user)
            ->get(route('admin.detalle.index'))
            ->assertOk()
            ->assertSee('lg:grid-cols-2')
            ->assertSee('Registrar Detalle de Compra')
            ->assertSee('Lista de Detalles de Compra');
        foreach ([
            'admin.proveedor.index',
            'admin.compra.index',
            'admin.mesas.index',
            'admin.orden.index',
        ] as $routeName) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('lg:grid-cols-2');
        }

        Livewire::test(\App\Livewire\Admin\ClienteTable::class)
            ->assertSee($customer->email)
            ->assertSee(route('admin.customers.index').'/');
        Livewire::test(\App\Livewire\Admin\ProveedorTable::class)
            ->assertSee($supplier->razonsocial)
            ->assertSee(route('admin.proveedor.index').'/');
        Livewire::test(\App\Livewire\Admin\CompraTable::class)
            ->assertSee(route('admin.compra.index').'/');
        Livewire::test(\App\Livewire\Admin\DetalleCompraTable::class)
            ->assertSee($detail->name)
            ->assertSee(route('admin.detalle.index').'/');
        Livewire::test(\App\Livewire\Admin\MesaTable::class)
            ->assertSee(route('admin.mesas.index').'/');
        Livewire::test(\App\Livewire\Admin\OrdenTable::class)
            ->assertSee($order->nombre_plato)
            ->assertSee(route('admin.orden.index').'/');

        $this->actingAs($user)
            ->put(route('admin.customers.update', $customer), [
                'name' => 'Nombre actualizado',
                'last_name' => 'Apellido actualizado',
                'phone' => null,
                'email' => $customer->email,
                'address' => null,
                'date_registered' => null,
            ])
            ->assertRedirect(route('admin.customers.index'));

        $this->actingAs($user)
            ->put(route('admin.proveedor.update', $supplier), [
                'ruc' => $supplier->ruc,
                'razonsocial' => 'Proveedor actualizado',
                'address' => 'Dirección nueva',
                'phone' => '912345678',
                'email' => 'updated-supplier@example.test',
            ])
            ->assertRedirect(route('admin.proveedor.index'));

        $this->actingAs($user)
            ->put(route('admin.compra.update', $buy), [
                'user_id' => $user->id,
                'supplier_id' => $supplier->id,
                'total' => 35,
                'date' => '2025-02-01',
            ])
            ->assertRedirect(route('admin.compra.index'));

        $this->actingAs($user)
            ->put(route('admin.detalle.update', $detail), [
                'buy_id' => $buy->id,
                'name' => 'Producto actualizado',
                'cantidad' => 2,
                'precio_unitario' => 17.5,
                'subtotal' => 35,
            ])
            ->assertRedirect(route('admin.detalle.index'));

        $this->actingAs($user)
            ->put(route('admin.mesas.update', $table), [
                'nro_mesas' => 2,
                'capacidad' => 4,
                'disponible' => '0',
            ])
            ->assertRedirect(route('admin.mesas.index'));

        $this->actingAs($user)
            ->put(route('admin.orden.update', $order), [
                'customer_id' => $customer->id,
                'table_id' => $table->id,
                'nombre_plato' => 'Plato actualizado',
                'total' => 40,
                'metodo_pago' => 'Tarjeta',
            ])
            ->assertRedirect(route('admin.orden.index'));

        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Nombre actualizado']);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'razonsocial' => 'Proveedor actualizado']);
        $this->assertDatabaseHas('buys', ['id' => $buy->id, 'total' => 35]);
        $this->assertDatabaseHas('buy_details', ['id' => $detail->id, 'name' => 'Producto actualizado']);
        $this->assertDatabaseHas('tables', ['id' => $table->id, 'disponible' => false]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'nombre_plato' => 'Plato actualizado']);
    }

    public function test_admin_records_can_be_deleted_from_their_module_routes(): void
    {
        $users = User::factory()->count(2)->create(['is_admin' => true]);
        $customers = collect([
            ['name' => 'Cliente uno', 'email' => 'one@example.test'],
            ['name' => 'Cliente dos', 'email' => 'two@example.test'],
        ])->map(fn (array $customer) => Customer::create([
            ...$customer,
            'last_name' => 'Prueba',
        ]));
        $suppliers = collect([
            ['ruc' => '11111111111', 'razonsocial' => 'Proveedor uno'],
            ['ruc' => '22222222222', 'razonsocial' => 'Proveedor dos'],
        ])->map(fn (array $supplier) => Supplier::create($supplier));
        $tables = collect([1, 2])->map(fn (int $number) => Table::create([
            'nro_mesas' => $number,
            'capacidad' => 4,
            'disponible' => true,
        ]));
        $buys = collect([0, 1])->map(fn (int $index) => Buy::create([
            'user_id' => $users[$index]->id,
            'supplier_id' => $suppliers[$index]->id,
            'total' => 20,
            'date' => '2025-01-01',
        ]));
        $details = $buys->map(fn (Buy $buy) => BuyDetails::create([
            'buy_id' => $buy->id,
            'name' => 'Producto',
            'cantidad' => 1,
            'precio_unitario' => 20,
            'subtotal' => 20,
        ]));
        $orders = collect([0, 1])->map(fn (int $index) => Order::create([
            'customer_id' => $customers[$index]->id,
            'table_id' => $tables[$index]->id,
            'nombre_plato' => 'Plato',
            'total' => 20,
            'metodo_pago' => 'Efectivo',
        ]));

        $this->actingAs($users[0])
            ->delete(route('admin.detalle.destroy', $details[0]))
            ->assertRedirect(route('admin.detalle.index'));
        $this->actingAs($users[0])
            ->delete(route('admin.compra.destroy', $buys[0]))
            ->assertRedirect(route('admin.compra.index'));
        $this->actingAs($users[0])
            ->delete(route('admin.proveedor.destroy', $suppliers[1]))
            ->assertRedirect(route('admin.proveedor.index'));
        $this->actingAs($users[0])
            ->delete(route('admin.orden.destroy', $orders[0]))
            ->assertRedirect(route('admin.orden.index'));
        $this->actingAs($users[0])
            ->delete(route('admin.customers.destroy', $customers[0]))
            ->assertRedirect(route('admin.customers.index'));
        $this->actingAs($users[0])
            ->delete(route('admin.orden.destroy', $orders[1]))
            ->assertRedirect(route('admin.orden.index'));
        $this->actingAs($users[0])
            ->delete(route('admin.mesas.destroy', $tables[1]))
            ->assertRedirect(route('admin.mesas.index'));

        $this->assertDatabaseMissing('buy_details', ['id' => $details[0]->id]);
        $this->assertDatabaseMissing('buys', ['id' => $buys[0]->id]);
        $this->assertDatabaseMissing('buy_details', ['id' => $details[1]->id]);
        $this->assertDatabaseMissing('suppliers', ['id' => $suppliers[1]->id]);
        $this->assertDatabaseMissing('buys', ['id' => $buys[1]->id]);
        $this->assertDatabaseMissing('buy_details', ['id' => $details[1]->id]);
        $this->assertDatabaseMissing('orders', ['id' => $orders[0]->id]);
        $this->assertDatabaseMissing('orders', ['id' => $orders[1]->id]);
        $this->assertDatabaseMissing('customers', ['id' => $customers[0]->id]);
        $this->assertDatabaseMissing('tables', ['id' => $tables[1]->id]);
    }
}
