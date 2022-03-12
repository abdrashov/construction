<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'expense_category_id' => ExpenseCategory::inRandomOrder()->value('id'),
            'organization_id' =>  Organization::inRandomOrder()->value('id'),
            'user_id' =>  User::inRandomOrder()->value('id'),
            'name'=> $this->faker->text(22),
            'price'=>$this->faker->numberBetween(1000000),
            'paid'=>$this->faker->numberBetween(1000000),
            'date'=> $this->faker->date(),
        ];
    }
}
