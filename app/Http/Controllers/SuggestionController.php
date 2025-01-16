<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use App\Models\Situation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    /**
     * Display all suggestions under a specific situation.
     */
    public function index($situationId)
    {
        // Check if the situation exists
        $situation = Situation::find($situationId);

        if (! $situation) {
            return response()->json([
                'success' => false,
                'message' => 'Situation not found',
            ], 404);
        }

        // Get all suggestions for the given situation
        $suggestions = Suggestion::where('situation_id', $situationId)
            ->with(['user', 'situation'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Suggestions successfully retrieved",
            'data' => [
                'situation' => $situation,
                'suggestions' => $suggestions,
            ]
        ]);
    }

    /**
     * Store a new suggestion in the database.
     */
    public function store(Request $request, int $situationId)
    {

        $situation = Situation::find($situationId);

        // Validate the request
        $validatedData = $request->validate([
            'answer' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:51200',
        ]);

        // Handle the image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('suggestion_images', 'public');
        }

        // Create the suggestion
        $suggestion = Suggestion::create([
            'answer' => $validatedData['answer'],
            'situation_id' => $situationId,
            'user_id' => Auth::id(),
            'image' => $imagePath,
        ]);

        $suggestion->load([
            'user',
            'situation'
        ]);

        return response()->json([
            'success' => true,
            'data' => $suggestion,
        ]);
    }

    /**
     * Display a specific suggestion.
     */
    public function show($id)
    {
        // Find the suggestion by ID
        $suggestion = Suggestion::with(['situation', 'user'])->find($id);

        if (! $suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $suggestion,
        ]);
    }

    /**
     * Update a suggestion in the database.
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'answer' => 'sometimes|string|max:255',
            'situation_id' => 'sometimes|exists:situations,id',
            'user_id' => 'sometimes|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the suggestion by ID
        $suggestion = Suggestion::find($id);

        if (! $suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found',
            ], 404);
        }

        // Handle the image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('suggestion_images', 'public');
            $validatedData['image'] = $imagePath;
        }

        // Update the suggestion
        $suggestion->update($validatedData);

        return response()->json([
            'success' => true,
            'data' => $suggestion,
        ]);
    }

    /**
     * Remove a suggestion from the database.
     */
    public function destroy($id)
    {
        // Find the suggestion by ID
        $suggestion = Suggestion::find($id);

        if (! $suggestion) {
            return response()->json([
                'success' => false,
                'message' => 'Suggestion not found',
            ], 404);
        }

        // Delete the suggestion
        $suggestion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Suggestion deleted successfully',
        ]);
    }
}
