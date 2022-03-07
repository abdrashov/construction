<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $oranization = Organization::inRandomOrder()->first();
        $user = $oranization->users[mt_rand(0, count($oranization->users) - 1)];
        $supplier = Supplier::inRandomOrder()->first();
        
        $pay = $this->faker->numberBetween(0, 1);

        return [
            'organization_id' => $oranization->id,
            'user_id' => User::inRandomOrder()->value('id'),
            'name' => $this->faker->numberBetween(1147483647),
            'pay' => $pay,
            'status' => $pay == 1 ? 1 : $this->faker->numberBetween(0, 1),
            'date' => $this->faker->date(),
            'supplier' => $supplier->name,
            'accepted' => $user['lastname'] . ' ' . $user['firstname'],
            'supplier_id' => $supplier->id
        ];
    }
}
