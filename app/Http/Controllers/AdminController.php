<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Note;
use App\Models\Rate;

use App\Models\Finbook;
use App\Models\Author;
use App\Models\Category;
use App\Models\ReadingTime;
use App\Models\Favorite;
use App\Models\SuggeorComp;
use App\Models\Question;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    
public function update(Request $request, $id) // Update Book in DB
{
    if (!Auth::check() ||!Auth::user()->is_admin) {
        return response()->json(['message' => 'Unauthorized'], 401);
    } 
    // Validate the incoming request
    $validatedData = $request->validate([
        'book_name' => 'required|string|max:255',
        'cover_image' => 'nullable|image|mimes:jpeg,png,bmp,jpg,gif,svg',
        'abstract' => 'nullable|string',
        'pdf' => 'nullable|mimes:pdf,docx,doc,xlsx,csv,gdoc,pptx,ppt,|max:2048',
        'author_name' => 'required|string|max:255',
        'category_name' => 'required|string|max:255',
        'category_name_en' => 'nullable|string|max:255',    

    ]);

    // Check if the book exists
    $book = Book::find($id);
    if (!$book) {
        return response()->json([
            'message' => 'Book not found',
        ], 404);
    }

    $book->book_name = $validatedData['book_name'];
    $book->abstract = $validatedData['abstract'];
    $book->pdf = $validatedData['pdf'];

 // Handle the cover image update
 if ($request->hasFile('cover_image')) {
    // Delete the old cover image
    if (Storage::disk('public')->exists('cover_images/' . $book->cover_image)) {
        Storage::disk('public')->delete('cover_images/' . $book->cover_image);
    }

     // Update the cover image
     $coverImage = $request->file('cover_image');
     $coverImageName = time() . '.' . $coverImage->getClientOriginalExtension();
     $coverImage->storeAs('cover_images', $coverImageName, 'cover_images');

     $baseUrl = '/' . 'cover_images/';
     $photoUrl = $baseUrl . $coverImageName;
     $coverImagePath = $photoUrl;

     $book->cover_image = $coverImagePath;
   
 }
    // Handle the PDF update
    if ($request->hasFile('pdf')) {
        // Delete the old PDF
        if (Storage::disk('public')->exists('pdf/'. $book->pdf)) {
            Storage::disk('public')->delete('pdf/'. $book->pdf);
        }

        // Update the PDF
        $pDF = $request->file('pdf');
        $pDFName = time(). '.'. $pDF->getClientOriginalExtension();
        $pDFPath = $pDF->storeAs('public/pdfs', $pDFName);
        $book->pdf = Storage::url($pDFPath);
    }

    $author = Author::firstOrCreate([
        'author_name' => $validatedData['author_name']
    ]);

    $category = Category::firstOrCreate([
        'category_name' => $validatedData['category_name'],
        'category_name_en' => $validatedData['category_name_en']

    ]);
   
    $book->author()->associate($author);
    $book->category()->associate($category);

    // Save the updated book
    $book->save();

    return response()->json([
       // 'message' => 'Book updated successfully',
        'book' => $book,
    ], 200);
}


public function deleteBook($bookId)
{
    if (!Auth::check() ||!Auth::user()->is_admin) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    // Find the book by its ID
    $book = Book::find($bookId);
    if (!$book) {
        return response()->json(['message' => 'book not found'], 404);
    }
    $book->delete();
    return response()->noContent(200);
}

public function getMostPopularBooks()
{
    if (!Auth::check() ||!Auth::user()->is_admin) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $popularBooks = Favorite::with('book')
        ->select('book_id', DB::raw('COUNT(*) as favorites_count'))
        ->groupBy('book_id')
        ->orderBy('favorites_count', 'desc')
        ->take(10)

        ->get()
        ->map(function ($favorite) {
            return [
                'book_id' => $favorite->book_id,
                'book_name' => $favorite->book->book_name,
               // 'cover_image' => $favorite->book->cover_image,
               // 'category_name' => $favorite->book->category->category_name,
                'favorites_count' => $favorite->favorites_count,
            ];
        });

    return response()->json(['popular_books' => $popularBooks], 200);
}

 public function getBooksRatings()
{
    if (!Auth::check() ||!Auth::user()->is_admin) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Juggling books with average ratings, excluding those without any ratings
    $booksWithRatings = Book::select('books.id', DB::raw('AVG(rates.rating) as average_rating'))
        ->leftJoin('rates', 'rates.book_id', '=', 'books.id')
        ->groupBy('books.id')
        ->havingRaw('AVG(rates.rating) IS NOT NULL') // Exclude books without ratings
        ->get();

    return response()->json([
       // 'message' => 'Books and their ratings retrieved successfully',
        'books' => $booksWithRatings,
    ], 200);
}


    public function displayCom() // عرض شكاوي المستخدمين
    { 
        // Ensure the request is coming from an authenticated admin
        if (!Auth::check() ||!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Fetch suggestions or complaints
        $suggestionsOrComplaints = SuggeorComp::with(['user' => function ($query) {
            $query->select('id', 'name'); 
        }])
        ->get();

        return response()->json([
            'suggestions_or_complaints' => $suggestionsOrComplaints->map(function ($item) {
                return [
                    'user_id' => $item->user->id,
                    'user_name' => $item->user->name,
                    'user_photo' => $item->user->image,
                    'suggestion_or_complaint' => $item->suggestionorcomplaint,
                ];
            })
          //  'message' => 'Suggestions or complaints retrieved successfully'
        ], 200);
    }

    public function displayQuestion()
    {
        if (!Auth::check() ||!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }  

        $questions = Question::with(['user' => function ($query) {
            $query->select('id', 'name', 'image'); 
        }])
        ->get();
        
        return response()->json([
            'questions' => $questions->map(function ($item) {
                return [
                    'question_id'=> $item->id,
                    'user_id' => $item->user->id,
                    'user_name' => $item->user->name,
                    'book_name' => $item->book->book_name,
                    'user_photo' => $item->user->image,
                    'question' => $item->question,
                ];
            })
        ], 200);
    }


}

 

