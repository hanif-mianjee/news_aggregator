<?php

namespace App\Console\Commands;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     * This defines the name used to run the command from the terminal.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles';

    /**
     * The console command description.
     * A short description of what the command does, shown in Artisan command lists.
     *
     * @var string
     */
    protected $description = 'Fetch articles from external news APIs';

    /**
     * Execute the console command.
     * This is the main entry point of the command, responsible for running the tasks to fetch articles.
     */
    public function handle()
    {   
        // Call each method that fetches articles from different sources.
        $this->fetchFromCredAPI();     // Fetch data from the Coresignal API.
        $this->fetchFromNewsAPI();     // Fetch data from the NewsAPI.
        $this->fetchFromGuardianAPI(); // Fetch data from The Guardian API.
        
        // Output a message after successfully fetching the data.
        $this->info('Articles fetched successfully!');
    }
    
    /**
     * Fetch articles from the Coresignal API.
     * Uses an API key to authenticate, retrieves data, and saves it to the database.
     */
    protected function fetchFromCredAPI()
    {
        // Make a request to the Coresignal API with the API key.
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.core_signal.key'),
        ])->get('https://api.coresignal.com/v1/data-endpoint');

        // Extract the data from the API response.
        $data = $response->json()['data'] ?? [];

        // Loop through the articles returned from the API.
        foreach ($data as $item) {
            // Convert the published date to a format suitable for the database.
            $publishedAt = str_replace(['T', 'Z'], [' ', ''], $item['published_at']);
            
            // Skip the article if the content field is missing or empty.
            if (!isset($item['content']) || empty($item['content'])) {
                continue;
            }

            // Insert or update the article in the database.
            Article::updateOrCreate(
                ['title' => $item['title']],
                [
                    'content' => $item['content'],
                    'author' => $item['author'] ?? 'Unknown', // Use 'Unknown' if no author is provided.
                    'category' => 'Business',                 // Set a default category.
                    'source' => 'Coresignal',
                    'published_at' => $publishedAt,
                ]
            );
        }

        // Output a message after successfully fetching Coresignal data.
        $this->info('Data from Coresignal fetched successfully.');
    }

    /**
     * Fetch articles from the NewsAPI.
     * Retrieves news articles related to technology and saves them to the database.
     */
    protected function fetchFromNewsAPI()
    {   
        // Make a request to the NewsAPI with the configured API key and other parameters.
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => config('services.news_api.key'),
            'country' => 'us',
            'category' => 'technology',
            'pageSize' => 10,
        ]);

        // Extract the articles from the API response.
        $articles = $response->json()['articles'] ?? [];

        // Loop through the articles.
        foreach ($articles as $article) {
            // Convert the published date to a format suitable for the database.
            $publishedAt = str_replace(['T', 'Z'], [' ', ''], $article['publishedAt']);
            $content = $article['description'];

            // Skip the article if the content is missing or empty.
            if ($content === null || $content === '') {
                continue;
            }

            // Insert or update the article in the database.
            Article::updateOrCreate(
                ['title' => $article['title']],
                [
                    'content' => $content,
                    'author' => $article['author'],          // Set the article's author.
                    'category' => 'Technology',              // Set the category as Technology.
                    'source' => $article['source']['name'],  // Set the source name.
                    'published_at' => $publishedAt,
                ]
            );
        }
    }

    /**
     * Fetch articles from The Guardian API.
     * Retrieves technology-related articles and saves them to the database.
     */
    protected function fetchFromGuardianAPI()
    {   
        // Make a request to The Guardian API with the configured API key and other parameters.
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => config('services.guardian_api.key'),
            'section' => 'technology',
            'page-size' => 10,
            'show-fields' => 'all',
        ]);
        
        // Extract the articles from the API response.
        $articles = $response->json()['response']['results'] ?? [];

        // Loop through the articles.
        foreach ($articles as $article) {
            // Convert the published date to a suitable format for the database.
            $publishedAt = Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s');

            // Insert or update the article in the database.
            Article::updateOrCreate(
                ['title' => $article['webTitle']],
                [
                    'content' => $article['fields']['bodyText'] ?? '',   // Use an empty string if the body text is missing.
                    'author' => $article['fields']['byline'] ?? '',      // Use an empty string if no byline is provided.
                    'category' => 'Technology',                         // Set the category as Technology.
                    'source' => 'The Guardian',                         // Set the source as The Guardian.
                    'published_at' => $publishedAt,
                ]
            );
        }
    }
}

