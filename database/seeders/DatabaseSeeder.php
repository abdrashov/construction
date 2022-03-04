<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Measurement;
use App\Models\Organization;
use App\Models\Supplier;
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
            'first_name' => 'Тест',
            'last_name' => 'Адам',
            'login' => 'example@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        User::factory(5)->create();

        Measurement::create([
            'name' => 'кг'
        ]);
        Measurement::create([
            'name' => 'т'
        ]);
        Measurement::create([
            'name' => 'м'
        ]);

        Item::factory(20)->create();
        Supplier::factory(30)->create();
        Organization::factory(3)->create();
    }
}
