<?php

namespace Database\Factories;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(), // Associate with a user
            'sources' => json_encode(['The Guardian']), // Example default data
            'categories' => json_encode(['Technology']),
            'authors' => json_encode(['Author Name']),
        ];
    }
}
