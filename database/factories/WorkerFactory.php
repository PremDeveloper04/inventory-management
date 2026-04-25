<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Worker>
 */
class WorkerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'experience' => fake()->numberBetween(1, 15),
            'salary' => fake()->numberBetween(10000, 100000),
            'joined_at' => fake()->dateTimeBetween('-5 years', 'now'),
        ];
    }
}
