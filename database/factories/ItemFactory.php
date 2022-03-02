<?php

namespace Database\Factories;

use App\Models\Measurement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->colorName,
            'measurement_id' => Measurement::inRandomOrder()->first()->id
        ];
    }
}
