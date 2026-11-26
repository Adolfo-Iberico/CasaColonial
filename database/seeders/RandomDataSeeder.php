<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RandomDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('es_ES');

        DB::transaction(function () use ($faker): void {
            $userIds = [];

            for ($i = 0; $i < 15; $i++) {
                $userIds[] = DB::table('users')->insertGetId([
                    'name' => $faker->name(),
                    'email' => 'demo.'.Str::uuid().'@example.test',
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(40)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $customerIds = [];

            for ($i = 0; $i < 20; $i++) {
                $customerIds[] = DB::table('customers')->insertGetId([
                    'name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'phone' => $faker->phoneNumber(),
                    'email' => 'customer.'.Str::uuid().'@example.test',
                    'address' => $faker->address(),
                    'date_registered' => $faker->dateTimeBetween('-2 years')->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $supplierIds = [];

            for ($i = 0; $i < 10; $i++) {
                do {
                    $ruc = $faker->numerify('##########') . $faker->randomDigit();
                } while (DB::table('suppliers')->where('ruc', $ruc)->exists());

                $supplierIds[] = DB::table('suppliers')->insertGetId([
                    'ruc' => $ruc,
                    'razonsocial' => $faker->company(),
                    'phone' => $faker->phoneNumber(),
                    'email' => 'supplier.'.Str::uuid().'@example.test',
                    'address' => $faker->address(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $tableIds = [];
            $nextTableNumber = ((int) DB::table('tables')->max('nro_mesas')) + 1;

            for ($i = 0; $i < 10; $i++) {
                $tableIds[] = DB::table('tables')->insertGetId([
                    'nro_mesas' => $nextTableNumber + $i,
                    'capacidad' => $faker->randomElement([2, 4, 6, 8]),
                    'disponible' => $faker->boolean(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            for ($i = 0; $i < 10; $i++) {
                $buyId = ((int) DB::table('buys')->max('id')) + 1;
                $details = [];
                $totalCents = 0;

                for ($detail = 0; $detail < $faker->numberBetween(2, 4); $detail++) {
                    $quantity = $faker->numberBetween(1, 20);
                    $unitPriceCents = $faker->numberBetween(100, 10000);
                    $subtotalCents = $quantity * $unitPriceCents;
                    $totalCents += $subtotalCents;

                    $details[] = [
                        'buy_id' => $buyId,
                        'name' => $faker->randomElement([
                            'Arroz',
                            'Aceite',
                            'Pollo',
                            'Verduras',
                            'Harina',
                            'Azúcar',
                            'Café',
                        ]),
                        'cantidad' => $quantity,
                        'precio_unitario' => number_format($unitPriceCents / 100, 2, '.', ''),
                        'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('buys')->insert([
                    'user_id' => $faker->randomElement($userIds),
                    'supplier_id' => $faker->randomElement($supplierIds),
                    'total' => number_format($totalCents / 100, 2, '.', ''),
                    'date' => $faker->dateTimeBetween('-1 year')->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('buy_details')->insert($details);
            }

            for ($i = 0; $i < 20; $i++) {
                DB::table('orders')->insert([
                    'customer_id' => $faker->randomElement($customerIds),
                    'table_id' => $faker->randomElement($tableIds),
                    'nombre_plato' => $faker->randomElement([
                        'Arroz con pollo',
                        'Lomo saltado',
                        'Ceviche',
                        'Ají de gallina',
                        'Tallarines verdes',
                        'Seco de res',
                    ]),
                    'total' => number_format($faker->randomFloat(2, 10, 150), 2, '.', ''),
                    'metodo_pago' => $faker->randomElement(['Efectivo', 'Tarjeta', 'Transferencia']),
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
