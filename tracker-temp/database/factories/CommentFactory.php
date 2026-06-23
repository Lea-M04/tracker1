<?php

namespace Database\Factories;

use App\Models\Issue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'issue_id' => Issue::factory(),
            'author_name' => fake()->name(),
            'body' => fake()->randomElement([
                'I reproduced this locally and the issue still happens after clearing the cache.',
                'The latest change improves the flow, but we should add one more validation case.',
                'This looks ready from my side after the UI spacing adjustment.',
                'I added more details to the acceptance criteria so the next step is clearer.',
                'The backend response is correct; the remaining work is in the Blade partial.',
                'This should be tested with a project that has multiple tags attached.',
                'I noticed the same behavior on the staging database.',
                'The fix should also cover empty descriptions and overdue dates.',
            ]),
        ];
    }
}
