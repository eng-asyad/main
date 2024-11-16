<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'scientific_name' => $this->faker->name(),
            'company' => $this->faker->company(),
            'classification' => 'heart',
            'quantity' => 10,
            'expiration_date' => $this->faker->date(),
            'price' => 10,
        ];
    }
}