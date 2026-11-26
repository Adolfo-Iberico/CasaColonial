<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class DetalleCompraTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_a_placeholder_when_a_detail_has_no_creation_date(): void
    {
        $user = User::factory()->create();

        DB::table('buy_details')->insert([
            'buy_id' => $user->id,
            'name' => 'Producto de prueba',
            'cantidad' => 1,
            'precio_unitario' => 10,
            'subtotal' => 10,
            'created_at' => null,
            'updated_at' => null,
        ]);

        Livewire::test(\App\Livewire\Admin\DetalleCompraTable::class)
            ->assertSee('—');
    }
}
