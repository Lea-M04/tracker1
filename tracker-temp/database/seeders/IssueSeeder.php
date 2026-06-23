<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Issue::factory()
            ->count(50)
            ->create([
                'project_id' => fn () => Project::query()->inRandomOrder()->value('id'),
            ]);
    }
}
