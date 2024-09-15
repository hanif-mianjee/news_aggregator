<?php

namespace App\Http\Controllers;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Get all articles with pagination",
     *     @OA\Response(
     *         response=200,
     *         description="A list of articles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Article")
     *         )
     *     )
     * )
     */
    public function index()
    {   
        // Fetch all articles with pagination (10 articles per page)
        $articles = Article::paginate(10);
        return response()->json($articles, 200);
    }

    public function search(Request $request)
    {   
        // Search parameters from the request
        
        $keyword = $request->input('keyword');
        $category = $request->input('category');
        $source = $request->input('source');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Base query
        $query = Article::query();

        // Filtering by keyword in title or content
        if ($keyword) {
            $query->where('title', 'LIKE', "%$keyword%")
                ->orWhere('content', 'LIKE', "%$keyword%");
        }

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Filter by source
        if ($source) {
            $query->where('source', $source);
        }

        // Filter by date range
        if ($startDate && $endDate) {
            $query->whereBetween('published_at', [$startDate, $endDate]);
        }

        // Paginate the result
        $articles = $query->paginate(10);

        return response()->json($articles, 200);
    }

    public function show($id)
    {   
        try {
            // Attempt to find the article by ID
            $article = Article::findOrFail($id);
            // If found, return the article as JSON
            return response()->json($article, 200);
        } catch (\Exception $e) {
            // If not found, catch the exception and return a custom 404 response
            return response()->json(['error' => 'Article not found'], 404);
        }
    }

    public function getPersonalizedNewsFeed()
    {   
        // Get the authenticated user and their preferences
        $user = Auth::user();
        $preferences = $user->preference;

        // Base query to fetch articles
        $query = Article::query();

        // Filter by sources
        if ($preferences && $preferences->sources) { 
            $query->whereIn('source', $preferences->sources);
        }

        // Filter by categories
        if ($preferences && $preferences->categories) {
            $query->whereIn('category', $preferences->categories);
        }

        // Filter by authors
        if ($preferences && $preferences->authors) {
            $query->whereIn('author', $preferences->authors);
        }

        // Fetch articles with pagination
        $articles = $query->paginate(10);

        return response()->json($articles, 200);
    }


}
