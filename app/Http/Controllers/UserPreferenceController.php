<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;
class UserPreferenceController extends Controller
{
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
    public function getPreferences()
    {   
        // Get the authenticated user
        $user = Auth::user();

        // Find the user's preferences
        $preferences = UserPreference::where('user_id', $user->id)->first();

        return response()->json($preferences, 200);
    }
}
