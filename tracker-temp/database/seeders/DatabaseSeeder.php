<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Tag;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            IssueSeeder::class,
            TagSeeder::class,
            CommentSeeder::class,
        ]);

        $tagIds = Tag::query()->pluck('id');

        Issue::query()->each(function (Issue $issue) use ($tagIds): void {
            $issue->tags()->sync(
                $tagIds->random(fake()->numberBetween(1, 4))->all()
            );
        });
    }
}
