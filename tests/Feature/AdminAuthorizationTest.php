<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_visit_admin_modules(): void
    {
        $this->get('/admin/customers')
            ->assertRedirect('/login');
    }

    public function test_users_only_access_modules_allowed_by_their_role(): void
    {
        $user = User::factory()->create(['role' => 'waiter']);

        $this->actingAs($user)
            ->get('/admin/customers')
            ->assertOk()
            ->assertSee('Mozo')
            ->assertSee('Salón y atención')
            ->assertDontSee('Producción');

        $this->actingAs($user)
            ->get(route('admin.proveedor.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('admin.mesas.destroy', 1))
            ->assertForbidden();

        $unassignedUser = User::factory()->create();
        $this->actingAs($unassignedUser)
            ->get('/dashboard')
            ->assertForbidden();

        $administrator = User::factory()->create(['role' => 'administrator']);
        $this->actingAs($administrator)
            ->get(route('admin.proveedor.index'))
            ->assertOk();
    }

    public function test_waiters_can_only_change_table_availability(): void
    {
        $waiter = User::factory()->create(['role' => 'waiter']);
        $table = Table::create([
            'nro_mesas' => 1,
            'capacidad' => 4,
            'disponible' => true,
        ]);

        $this->actingAs($waiter)
            ->put(route('admin.mesas.update', $table), [
                'disponible' => '0',
            ])
            ->assertRedirect(route('admin.mesas.index'));

        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'nro_mesas' => 1,
            'capacidad' => 4,
            'disponible' => false,
        ]);

        $this->actingAs($waiter)
            ->put(route('admin.mesas.update', $table), [
                'nro_mesas' => 5,
                'capacidad' => 8,
                'disponible' => '1',
            ])
            ->assertSessionHasErrors(['nro_mesas', 'capacidad']);

        $this->assertDatabaseHas('tables', [
            'id' => $table->id,
            'nro_mesas' => 1,
            'capacidad' => 4,
            'disponible' => false,
        ]);
    }

    public function test_kitchen_roles_do_not_see_financial_dashboard_or_legacy_order_management(): void
    {
        $cook = User::factory()->create(['role' => 'cook']);

        $this->actingAs($cook)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Cocinero')
            ->assertSee('Producción')
            ->assertSee('Cocina / Barra')
            ->assertDontSee('Salón y atención')
            ->assertDontSee('Ventas del mes')
            ->assertDontSee('Ventas y compras');

        $this->actingAs($cook)
            ->get(route('admin.orden.index'))
            ->assertForbidden();
    }
}
