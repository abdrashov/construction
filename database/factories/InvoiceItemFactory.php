<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Measurement;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $item = Item::inRandomOrder()->first();
        $measurement = Measurement::inRandomOrder()->first();

        return [
            'invoice_id' => Invoice::inRandomOrder()->value('id'), 
            'name' => $item->name, 
            'item_id' => $item->id, 
            'count' => $this->faker->numberBetween(10000),
            'price' => $this->faker->numberBetween(5000000000),
            'measurement' => $measurement->name,
            'measurement_id' => $measurement->id,
        ];
    }
}
