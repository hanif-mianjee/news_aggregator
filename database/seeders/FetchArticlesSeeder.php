<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Article;
use App\Models\UserPreference;
class FetchArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Call the custom command to fetch articles
        Artisan::call('app:fetch-articles');

        // Optionally, you can log the output if needed
        $output = Artisan::output();
        // You can log or print the output if required
        // echo $output;
        $user = User::first();
        // Fetch 10 articles
        $articles = Article::take(10)->get();

        foreach ($articles as $article) {
            // Assuming Article has 'source', 'category', and 'author' fields
            $preferencesData = [
                'sources' => $article->source ? $article->source : null,
                'categories' => $article->category ? $article->category : null,
                'authors' => $article->author ? $article->author : null,
            ];

            // Insert preferences for each article
            UserPreference::create([
                'user_id' => $user->id,
                'sources' => $preferencesData['sources'], // Store as an array or string directly
                'categories' => $preferencesData['categories'],
                'authors' => $preferencesData['authors'],
            ]);
        }

        $this->command->info('User preferences seeded successfully.');
    }
}
