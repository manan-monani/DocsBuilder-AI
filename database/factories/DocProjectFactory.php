<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocProject>
 */
class DocProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3);

        return [
            'slug' => \Illuminate\Support\Str::slug($name),
            'name' => $name,
            'description' => $this->faker->paragraph(),
            'is_active' => true,
        ];
    }
}
