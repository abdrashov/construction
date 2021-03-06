<?php

namespace Database\Factories;

use App\Models\ItemCategory;
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
            'name' => $this->faker->text(20),
            'measurement_id' => Measurement::inRandomOrder()->first()->id,
            'item_category_id' => ItemCategory::inRandomOrder()->first()->id
        ];
    }
}
