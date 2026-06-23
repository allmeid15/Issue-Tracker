<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Project;
use App\Models\Issue;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tags = Tag::factory(5)->create();

        Project::factory(3)->create()->each(function ($project) use ($tags) {
            Issue::factory(rand(3, 5))->create(['project_id' => $project->id])->each(function ($issue) use ($tags) {
                $issue->tags()->attach($tags->random(rand(1, 3)));
                Comment::factory(rand(1, 3))->create(['issue_id' => $issue->id]);
            });
    });
    }
}
