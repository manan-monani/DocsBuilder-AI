<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DocumentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = \App\Models\DocProject::updateOrCreate(
            ['slug' => 'main-product'],
            [
                'name' => 'Main Product Documentation',
                'description' => 'Official documentation for the main product.',
                'is_active' => true,
            ]
        );

        $version = $project->versions()->updateOrCreate(
            ['slug' => 'v1'],
            [
                'name' => 'Version 1.0',
                'is_default' => true,
            ]
        );

        $categories = [
            ['name' => 'Getting Started', 'slug' => 'getting-started', 'order' => 1],
            ['name' => 'Advanced Features', 'slug' => 'advanced-features', 'order' => 2],
        ];

        foreach ($categories as $catData) {
            $category = $version->categories()->updateOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );

            for ($i = 1; $i <= 3; $i++) {
                $pageTitle = $category->name.' Page '.$i;
                $page = $category->pages()->updateOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($pageTitle)],
                    [
                        'title' => $pageTitle,
                        'content_html' => '<h2>Section 1</h2><p>This is some content for '.$pageTitle.'.</p><h2>Section 2</h2><p>More details here.</p>',
                        'content_json' => json_encode(['type' => 'doc', 'content' => []]),
                        'order' => $i,
                        'status' => 'published',
                    ]
                );

                if ($page->revisions()->count() === 0) {
                    $page->revisions()->create([
                        'user_id' => \App\Models\User::first()?->id ?? 1,
                        'content_json' => $page->content_json,
                        'content_html' => $page->content_html,
                        'change_summary' => 'Initial publication',
                        'is_snapshot' => true,
                    ]);
                }
            }
        }
    }
}
