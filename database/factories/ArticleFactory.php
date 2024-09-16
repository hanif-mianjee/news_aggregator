<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source' => 'The Guardian',
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'author' => 'Author Name',
            'category' => 'Technology',
            'published_at' => now(),
        ];
    }
}
