<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Favorite;

use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
public function store_and_deletFavorite(Request $request) //add and delete favorite

{ 
  $validatedData = $request->validate([
    'book_id' => 'required|exists:books,id',
]);

$favorite = Favorite::where('user_id',Auth::id())
                    ->where('book_id', $request->book_id)
                    ->first();
if (!$favorite) {
    $favorite = Favorite::create([
        'user_id'=> Auth::id(),
        'book_id'=> $request->book_id
    ]);
    return response()->json(['favorite' => $favorite], 200);
}
else {
    $favorite->delete();
    return response()->json(['message' => 'Book removed from favorites'], 200);
}
}

public function getUserFavorites( ) // show all favorite library
{
    $favorites = Favorite::with('book')
    ->where('user_id', Auth::id())
    ->get()
    ->map(function ($favorite) {
       return [
            'book_id' => $favorite->book_id,
            'book_name' => $favorite->book->book_name,
            'cover_image'=>$favorite->book->cover_image,   
          //  'pdf'=>$favorite->book->pdf, 
          //  'author_name' => $favorite->book->author->author_name,
            'category_name' => $favorite->book->category->category_name,
       ];
    });
    if ($favorites->isEmpty()) {
        return response()->json(['message' => 'There are no books added'], 200);
    }
    
return response()->json(['favorites' => $favorites], 200);

}


}
