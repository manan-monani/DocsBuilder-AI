<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocVersion>
 */
class DocVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->numerify('#.#.x');

        return [
            'doc_project_id' => \App\Models\DocProject::factory(),
            'slug' => \Illuminate\Support\Str::slug($name),
            'name' => $name,
            'is_default' => false,
            'is_archived' => false,
        ];
    }
}
