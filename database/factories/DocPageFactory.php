<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocPage>
 */
class DocPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);

        return [
            'doc_category_id' => \App\Models\DocCategory::factory(),
            'slug' => \Illuminate\Support\Str::slug($title),
            'title' => $title,
            'content_html' => '<h2>'.$this->faker->sentence().'</h2><p>'.$this->faker->paragraphs(3, true).'</p><h2>'.$this->faker->sentence().'</h2><p>'.$this->faker->paragraphs(2, true).'</p>',
            'content_json' => json_encode(['type' => 'doc', 'content' => []]), // Placeholder
            'order' => $this->faker->numberBetween(0, 100),
            'status' => 'published',
        ];
    }
}
