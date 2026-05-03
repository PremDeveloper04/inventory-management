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
    public function definition(): array
    {
        $states = [
            'Bihar' => ['Patna', 'Gaya', 'Muzaffarpur'],
            'Uttar Pradesh' => ['Lucknow', 'Kanpur', 'Varanasi'],
            'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur'],
            'Delhi' => ['New Delhi', 'Dwarka', 'Rohini'],
            'Karnataka' => ['Bangalore', 'Mysore', 'Mangalore'],
            'West Bengal' => ['Kolkata', 'Howrah', 'Durgapur'],
            'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai'],
            'Rajasthan' => ['Jaipur', 'Udaipur', 'Jodhpur'],
        ];

        // 🔥 Pick random state
        $state = fake()->randomElement(array_keys($states));

        // 🔥 Pick city from that state
        $city = fake()->randomElement($states[$state]);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),

            'state' => $state,
            'city' => $city,
            'country' => 'India', // ✅ fixed

            'status' => fake()->randomElement(['active', 'inactive']),
            'experience' => rand(0, 15),
            'salary' => rand(10000, 100000),

            'joined_at' => now(),
        ];
    }
}
