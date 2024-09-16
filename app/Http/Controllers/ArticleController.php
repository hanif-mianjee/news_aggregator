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

    /**
     * @OA\Get(
     *     path="/api/articles/search",
     *     summary="Search for articles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false, 
     *         description="Keyword to search in title or content",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false, 
     *         description="Filter by category",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         required=false, 
     *         description="Filter by source",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false, 
     *         description="Start date for filtering articles (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false, 
     *         description="End date for filtering articles (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search results",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Article"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No articles found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No articles found.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Retrieve an article by ID",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the article to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of the article",
     *         @OA\JsonContent(ref="#/components/schemas/Article") 
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Article not found")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/user/news-feed",
     *     summary="Get personalized news feed",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with personalized articles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Article Title"),
     *                     @OA\Property(property="source", type="string", example="The Guardian"),
     *                     @OA\Property(property="category", type="string", example="Technology"),
     *                     @OA\Property(property="author", type="string", example="John Doe"),
     *                     @OA\Property(property="published_at", type="string", format="date-time", example="2024-09-16T12:00:00Z"),
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="per_page", type="integer", example=10),
     *             @OA\Property(property="last_page", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No articles found for the user's preferences",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No articles found.")
     *         )
     *     )
     * )
     */
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
