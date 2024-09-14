<?php

namespace App\Console\Commands;
use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'app:fetch-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from external news APIs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fetchFromNewsAPI();
        $this->fetchFromGuardianAPI();
        $this->fetchFromNYTimesAPI();
        $this->info('Articles fetched successfully!');
    }
    // Fetch From News API
    protected function fetchFromNewsAPI()
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => config('services.news_api.key'),
            'country' => 'us',
            'category' => 'technology',
            'pageSize' => 10,
        ]);

        $articles = $response->json()['articles'] ?? [];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['title']],
                [
                    'content' => $article['description'],
                    'author' => $article['author'],
                    'category' => 'Technology',
                    'source' => $article['source']['name'],
                    'published_at' => $article['publishedAt'],
                ]
            );
        }
    }

    protected function fetchFromGuardianAPI()
    {
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => config('services.guardian_api.key'),
            'section' => 'technology',
            'page-size' => 10,
            'show-fields' => 'all',
        ]);

        $articles = $response->json()['response']['results'] ?? [];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['webTitle']],
                [
                    'content' => $article['fields']['bodyText'] ?? '',
                    'author' => $article['fields']['byline'] ?? '',
                    'category' => 'Technology',
                    'source' => 'The Guardian',
                    'published_at' => $article['webPublicationDate'],
                ]
            );
        }
    }

    protected function fetchFromNYTimesAPI()
    {
        $response = Http::get('https://api.nytimes.com/svc/topstories/v2/technology.json', [
            'api-key' => config('services.nytimes_api.key'),
        ]);

        $articles = $response->json()['results'] ?? [];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['title']],
                [
                    'content' => $article['abstract'],
                    'author' => $article['byline'],
                    'category' => 'Technology',
                    'source' => 'New York Times',
                    'published_at' => $article['published_date'],
                ]
            );
        }
    }
}
}
