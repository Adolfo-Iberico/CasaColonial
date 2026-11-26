<?php

namespace Tests\Feature;

use App\Models\Buy;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'administrator']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_dashboard_displays_live_restaurant_metrics(): void
    {
        $user = User::factory()->create(['role' => 'administrator']);
        $customer = Customer::create([
            'name' => 'María',
            'last_name' => 'García',
            'email' => 'maria@example.test',
        ]);
        $supplier = Supplier::create([
            'ruc' => '12345678901',
            'razonsocial' => 'Proveedor de prueba',
        ]);
        $table = Table::create([
            'nro_mesas' => 1,
            'capacidad' => 4,
            'disponible' => true,
        ]);

        Buy::create([
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
            'total' => 20,
            'date' => now()->toDateString(),
        ]);
        Order::create([
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'nombre_plato' => 'Lomo saltado',
            'total' => 45.5,
            'metodo_pago' => 'Efectivo',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ventas del mes')
            ->assertSee('$45.50')
            ->assertSee('Órdenes de hoy')
            ->assertSee('Mesas disponibles')
            ->assertSee('Lomo saltado')
            ->assertSee('Efectivo')
            ->assertSee('Ventas y compras');
    }
}
