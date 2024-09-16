<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class UserPreferenceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/preferences",
     *     summary="Set user preferences",
     *     description="Requires Bearer token authorization",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    // Set user preferences
    public function setPreferences(Request $request)
    {   
        // Validate the request
        $validated = $request->validate([
            'sources' => 'nullable|array',
            'categories' => 'nullable|array',
            'authors' => 'nullable|array',
        ], [
            'required_without_all' => 'At least one of :values must be provided.'
        ]);
        
        if (empty($request->input('sources')) && empty($request->input('categories')) && empty($request->input('authors'))) {
            return response()->json(['error' => 'At least one preference (sources, categories, or authors) must be provided.'], 422);
        }
        
        // Get the authenticated user
        $user = Auth::user();

        // Prepare the data with defaults to ensure null values don't overwrite existing preferences
        $preferencesData = [
            'sources' => $validated['sources'] ?? null,
            'categories' => $validated['categories'] ?? null,
            'authors' => $validated['authors'] ?? null,
        ];
        // Find or create the user's preferences and save the data
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            $preferencesData
        );

        return response()->json(['message' => 'Preferences updated successfully'], 200);
    }

    // Get user preferences
    /**
     * @OA\Get(
     *     path="/api/user/preferences",
     *     summary="Get User Preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized. User is not authenticated."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Preferences not found."
     *     ),
     * )
     */
    public function getPreferences()
    {   
        // Get the authenticated user
        $user = Auth::user();

        // Find the user's preferences
        $preferences = UserPreference::where('user_id', $user->id)->get();

        return response()->json($preferences, 200);
    }
}
