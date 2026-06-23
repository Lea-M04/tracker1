<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'bug',
                'feature',
                'backend',
                'frontend',
                'ui',
                'ux',
                'api',
                'database',
                'security',
                'performance',
                'testing',
                'documentation',
                'urgent',
                'blocked',
                'enhancement',
                'refactor',
                'deployment',
                'support',
                'research',
                'review',
            ]),
            'color' => fake()->optional(0.9)->hexColor(),
        ];
    }
}
