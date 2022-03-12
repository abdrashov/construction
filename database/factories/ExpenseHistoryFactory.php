<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'expense_id' => Expense::inRandomOrder()->value('id'),
            'user_id' => User::inRandomOrder()->value('id'),
            'name'=> $this->faker->text(22),
            'price'=>$this->faker->numberBetween(1000000),
            'date'=> $this->faker->date(),
        ];
    }
}
