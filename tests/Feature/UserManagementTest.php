<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_open_user_management(): void
    {
        $regularUser = User::factory()->create();

        $this->actingAs($regularUser)
            ->get(route('admin.users.index'))
            ->assertForbidden();
        $this->actingAs($regularUser)
            ->get(route('dashboard'))
            ->assertForbidden()
            ->assertDontSee('Usuarios');

        $administrator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($administrator)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Gestión de usuarios')
            ->assertSee('Usuarios')
            ->assertSee('lg:grid-cols-2');
    }

    public function test_administrator_can_create_admin_and_regular_accounts(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        $password = 'SecureTestPass123!';

        $this->actingAs($administrator)
            ->post(route('admin.users.store'), [
                'name' => 'Nuevo Administrador',
                'email' => 'new-admin@example.test',
                'password' => $password,
                'password_confirmation' => $password,
                'role' => 'administrator',
            ])
            ->assertRedirect(route('admin.users.index'));

        $newAdmin = User::where('email', 'new-admin@example.test')->firstOrFail();
        $this->assertTrue($newAdmin->is_admin);
        $this->assertSame('administrator', $newAdmin->role);
        $this->assertNotNull($newAdmin->email_verified_at);
        $this->assertTrue(Hash::check($password, $newAdmin->password));

        $this->actingAs($administrator)
            ->post(route('admin.users.store'), [
                'name' => 'Nuevo Usuario',
                'email' => 'new-user@example.test',
                'password' => $password,
                'password_confirmation' => $password,
                'role' => 'waiter',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-user@example.test',
            'is_admin' => false,
            'role' => 'waiter',
        ]);
    }

    public function test_administrator_can_search_users_by_name_or_email(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        User::factory()->create([
            'name' => 'Carlos Barista',
            'email' => 'carlos@example.test',
        ]);
        User::factory()->create([
            'name' => 'Ana Cocina',
            'email' => 'ana-special@example.test',
        ]);

        $this->actingAs($administrator)
            ->get(route('admin.users.index', ['search' => 'Carlos']))
            ->assertOk()
            ->assertSee('Carlos Barista')
            ->assertDontSee('Ana Cocina')
            ->assertSee('1 resultado de 3 cuentas');

        $this->actingAs($administrator)
            ->get(route('admin.users.index', ['search' => 'ana-special']))
            ->assertOk()
            ->assertSee('Ana Cocina')
            ->assertDontSee('Carlos Barista');
    }

    public function test_administrator_cannot_delete_their_own_account(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);

        $this->actingAs($administrator)
            ->delete(route('admin.users.destroy', $administrator))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $administrator->id]);
    }

    public function test_administrator_can_change_an_existing_users_role(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['role' => 'cook']);

        $this->actingAs($administrator)
            ->put(route('admin.users.update', $user), ['role' => 'cashier_bar'])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'cashier_bar',
            'is_admin' => false,
        ]);
    }

    public function test_administrator_cannot_demote_themselves_or_the_last_administrator(): void
    {
        $administrator = User::factory()->create(['role' => 'administrator', 'is_admin' => true]);
        $otherUser = User::factory()->create(['role' => 'waiter']);

        $this->actingAs($administrator)
            ->put(route('admin.users.update', $administrator), ['role' => 'waiter'])
            ->assertSessionHasErrors('role');

        $this->actingAs($administrator)
            ->put(route('admin.users.update', $otherUser), ['role' => 'administrator'])
            ->assertRedirect(route('admin.users.index'));

        $this->actingAs($administrator)
            ->put(route('admin.users.update', $administrator), ['role' => 'waiter'])
            ->assertSessionHasErrors('role');
    }

    public function test_administrator_can_delete_another_account(): void
    {
        $administrator = User::factory()->create(['is_admin' => true]);
        $regularUser = User::factory()->create();

        $this->actingAs($administrator)
            ->delete(route('admin.users.destroy', $regularUser))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $regularUser->id]);
    }
}
