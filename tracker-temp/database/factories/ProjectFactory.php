<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
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
        $startDate = fake()->dateTimeBetween('-3 months', '+1 month');

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Customer Portal',
                'Internal CRM',
                'Billing Dashboard',
                'Mobile Support App',
                'Inventory Tracker',
                'Marketing Website',
                'Analytics Console',
                'Helpdesk Platform',
                'Admin Panel',
                'API Integration',
            ]).' '.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->optional(0.85)->paragraph(),
            'start_date' => $startDate,
            'deadline' => fake()->optional(0.8)->dateTimeBetween($startDate, '+6 months'),
        ];
    }
}
