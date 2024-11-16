<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Author;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class AuthorController extends Controller
{
 
 public function storeAuthor(Request $request)
 {
     $validatedData = $request->validate([
         'author_name' => 'required|string|max:255|unique:authors,author_name',  
     ]);
     $author = Author::create($validatedData);
     return response()->json([
         'author' => $author,
     ], 201);   
 }
 
  public function searchAuthor(Request $request) //search author by name
 {
     $validatedData = $request->validate([
         'author_name' => 'required|string|max:255',
     ]);

     $author_name = $request->input('author_name');

     $authors = Author::where('author_name', 'LIKE', '%' . $author_name . '%')->get();

     if ($authors->isNotEmpty()) {
         return response()->json([
             'authors' => $authors,
         ], 200);
     } else {
         return response()->json([
             'message' => 'Author not found',
         ], 404);
     }
 }
    
 public function showAuthorBooks($author_id) //display authors book
{

    $author = Author::with(['books'])->find($author_id);

    if ($author) {
        $books = $author->books->map(function ($book) {
            return [
                'id' => $book->id,
                'book_name' => $book->book_name,
                'cover_image' => $book->cover_image,
                'pdf' => $book->pdf,
                'abstract' => $book->abstract,
                'author_name' => $book->author->author_name,
                'category_name' => $book->category->category_name,
            ];
        });
        // Check if the author has any books
        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'This author has no books',
            ], 200);
        }
        return response()->json([
            'author' => $author->author_name,         
            'books' => $books,
        ], 200);
    } 
    else {
        return response()->json([
            'message' => 'Author not found',
        ], 404);
    }
}


















}
