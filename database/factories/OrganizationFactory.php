<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'users' => [
                [
                    'firstname' => $this->faker->firstName,
                    'lastname' => $this->faker->lastName,
                    'default' => true,
                ],
                [
                    'firstname' => $this->faker->firstName,
                    'lastname' => $this->faker->lastName,
                    'default' => true,
                ],
                [
                    'firstname' => $this->faker->firstName,
                    'lastname' => $this->faker->lastName,
                    'default' => true,
                ],
            ],
        ];
    }
}
