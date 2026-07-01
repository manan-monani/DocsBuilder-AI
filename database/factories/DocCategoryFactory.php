<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocCategory>
 */
class DocCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'doc_version_id' => \App\Models\DocVersion::factory(),
            'slug' => \Illuminate\Support\Str::slug($name),
            'name' => ucfirst($name),
            'order' => $this->faker->numberBetween(0, 100),
            'icon' => 'folder',
        ];
    }
}
