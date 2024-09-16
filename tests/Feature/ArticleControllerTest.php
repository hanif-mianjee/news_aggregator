<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;
class ArticleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_set_preferences()
    {   
       // Create a user and act as the user
        $user = User::factory()->create();
        Auth::login($user);

        // Prepare the data to send
        $data = [
            'sources' => ['Ars Technica', 'The Guardian'],
            'categories' => ['Technology'],
            'authors' => ['Hannah Al-Othman and Dan Milmo', 'Kevin Purdy']
        ];

        // Send a POST request
        $response = $this->postJson('/api/user/preferences', $data);

        // Check the response
        $response->assertStatus(200)
                ->assertJson(['message' => 'Preferences updated successfully']);

        // Fetch the saved preferences from the database
        $savedPreferences = \App\Models\UserPreference::where('user_id', $user->id)->first();

        // If the saved values are already arrays, compare them directly
        $this->assertEquals($data['sources'], is_array($savedPreferences->sources) ? $savedPreferences->sources : json_decode($savedPreferences->sources, true));
        $this->assertEquals($data['categories'], is_array($savedPreferences->categories) ? $savedPreferences->categories : json_decode($savedPreferences->categories, true));
        $this->assertEquals($data['authors'], is_array($savedPreferences->authors) ? $savedPreferences->authors : json_decode($savedPreferences->authors, true));
    }

    public function test_get_preferences()
    {
       // Create a user and preferences
        $user = User::factory()->create();
        $this->actingAs($user);

        // Use the factory to create a UserPreference
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'sources' => json_encode(['The Guardian']),
            'categories' => json_encode(['Technology']),
            'authors' => json_encode(['Author Name'])
        ]);

        // Send a GET request
        $response = $this->getJson('/api/user/preferences');

        // Check the response
        $response->assertStatus(200)
                ->assertJsonStructure(['sources', 'categories', 'authors']);
    }

    public function test_get_personalized_news_feed()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        UserPreference::factory()->create(['user_id' => $user->id, 'sources' => json_encode(['The Guardian'])]);

        // Create articles
        Article::factory()->create(['source' => 'The Guardian']);
        Article::factory()->create(['source' => 'Some Other Source']);

        // Send a GET request
        $response = $this->getJson('/api/user/news-feed');

        // Check the response
        $response->assertStatus(200)
             ->assertJsonCount(1); // Assuming only one article from The Guardian is created
    }
}
