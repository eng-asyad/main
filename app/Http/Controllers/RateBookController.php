<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Illuminate\Http\Request;

class RateBookController extends Controller
{
    public function storeRating(Request $request) // Update or add Rate
    {
        $validatedData = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|in:Bad,Average,Good,Very Good,Excellent',
        ]);
        $userId = auth()->user()->id; 
        $rating = Rate::updateOrCreate(
            [
                'user_id' => $userId,
                'book_id' => $validatedData['book_id']
            ],
            ['rating' => $validatedData['rating']]
        );

        return response()->json([
            'rating' => $rating,
        ], 201);
    }

public function removeRating($rating_id) // delete rate
{
    $rating = Rate::find($rating_id);
    
    if (!$rating) {
        return response()->json(['message' => 'Rate not found'], 404);
    }
    if (auth()->id() !== $rating->user_id) {
        return response()->json(['message' => 'Unauthorized to delete this rate'], 403);
    }
    if ($rating) {
        $rating->delete();
        return response()->noContent(200);
    } else {
        return response()->json([
            'message' => 'Rating not found',
        ], 404);
    }
}

}
