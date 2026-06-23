<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'deadline' => fake()->dateTimeBetween('+1 week', '+3 months'),
        ];
    }
}
