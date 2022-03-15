<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseHistory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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
            'first_name' => 'Тони',
            'last_name' => 'Старк',
            'role' => 'Супер Администратор',
            'login' => 'example@example.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        User::factory(8)->create();

        Measurement::create([
            'name' => 'кг',
        ]);
        Measurement::create([
            'name' => 'т',
        ]);
        Measurement::create([
            'name' => 'м',
        ]);

        Item::factory(100)->create();
        Supplier::factory(5)->create();
        Organization::factory(2)->create();
        Invoice::factory(20)->create();
        InvoiceItem::factory(100)->create();
        ExpenseCategory::create([
            'name' => 'Услуга',
        ]);
        ExpenseCategory::create([
            'name' => 'Зарплата',
        ]);
        ExpenseCategory::create([
            'name' => 'Прочие',
            'sort' => '1',
        ]);
        Expense::factory(10)->create();
        ExpenseHistory::factory(30)->create();
    }
}
