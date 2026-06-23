<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Issue>
 */
class IssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->randomElement([
                'Fix login redirect after session timeout',
                'Improve project filtering on dashboard',
                'Add validation message for required fields',
                'Resolve duplicate records in issue list',
                'Update responsive layout for mobile screens',
                'Optimize slow query on reports page',
                'Add missing empty state to table',
                'Fix incorrect priority badge color',
                'Create export button for filtered issues',
                'Review permissions for project members',
            ]),
            'description' => fake()->paragraphs(fake()->numberBetween(1, 3), true),
            'status' => fake()->randomElement(['open', 'in_progress', 'closed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'due_date' => fake()->optional(0.75)->dateTimeBetween('now', '+3 months'),
        ];
    }
}
