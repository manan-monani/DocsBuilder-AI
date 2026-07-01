<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocRevision>
 */
class DocRevisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doc_page_id' => \App\Models\DocPage::factory(),
            'user_id' => \App\Models\User::first()?->id ?? \App\Models\User::factory(),
            'content_json' => json_encode(['type' => 'doc', 'content' => []]),
            'content_html' => '<p>'.$this->faker->paragraph().'</p>',
            'change_summary' => 'Updated content',
            'is_snapshot' => false,
        ];
    }
}
