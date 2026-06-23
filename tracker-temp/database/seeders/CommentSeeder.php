<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::factory()
            ->count(100)
            ->create([
                'issue_id' => fn () => Issue::query()->inRandomOrder()->value('id'),
            ]);
    }
}
