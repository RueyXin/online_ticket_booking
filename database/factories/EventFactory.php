<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'start_time' => $this->faker->dateTimeBetween('now', '+1 year'),
            'total_tickets' => $this->faker->numberBetween(50, 200),
            'available_tickets' => $this->faker->numberBetween(10, 200),
            'price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
