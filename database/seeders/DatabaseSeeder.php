<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        User::factory(5)->create();

        $organization = Organization::create([
            'name' => 'ЖК что-то',
            'address' => 'Рыскулов 5/8',
        ]);

        Organization::factory(8)->create();

        $organization->invoices()->create([
            'name' => 'Накладной 1',
            'status' => false,
            'date' => now(),
            'supplier' => 'Поставшик ',
            'accepted' => 'Принял',
        ])->invoiceItems()->createMany([
            [
                'name' => 'Темір',
                'count' => 12,
                'price' => 50000,
                'measurement' => 'кг'
            ],
            [
                'name' => 'Ағаш',
                'count' => 50,
                'price' => 12000,
                'measurement' => 'кг'
            ],
        ]);
    }
}
