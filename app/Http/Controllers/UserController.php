<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Note;
use App\Models\Finbook;
use App\Models\Author;
use App\Models\Category;
use App\Models\ReadingTime;
use App\Models\SuggeorComp;



use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class UserController extends Controller
{
    
    public function addnote(Request $request)
    {
        $validatedData = $request->validate([
            'book_id' => 'required|exists:books,id',
            'note'=>'required|string',
        ]);

        $note = Note::create([
            'user_id'=> Auth::id(),
            'book_id'=> $request->book_id,
            'note'=> $request->note
        ]);
        if ($note) {
        return response()->json(['note' => $note], 200);
    }
}

        public function deleteNote($noteId)
        {
            $note = Note::find($noteId);

            // Check if the note exists
            if (!$note) {
                return response()->json(['message' => 'Note not found'], 404);
            }
            if (auth()->id() !== $note->user_id) {
                return response()->json(['message' => 'Unauthorized to delete this note'], 403);
            }
            if ($note->delete()) {
                return response()->noContent(200);
            } else {
                return response()->json(['message' => 'Failed to delete note'], 500);
            }
        } 

        public function getAllNotes()
        {
            $userId = auth()->user()->id;
            // Fetch all notes from the database
            $notes = Note::where('user_id', $userId)->get();

            if ($notes->isEmpty()) {
                return response()->noContent(200);            }

            return response()->json([
                'notes' => $notes->map(function ($note) {
                    return [
                        'book_id' => $note->book_id,
                        'book_name'=> $note->book->book_name,
                        'note' => $note->note,
                    ];
                })
            ], 200);
        }

   
    public function addFinbook(Request $request)
{
    $validatedData = $request->validate([
        'book_id' => 'required|exists:books,id',
    ]);

    $finbook = Finbook::where('user_id', Auth::id())
                      ->where('book_id', $request->book_id)
                      ->first();

    if (!$finbook) {
        $finbook = Finbook::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id
        ]);

        return response()->json(['finbook' => $finbook], 200);
    } else {
        return response()->json(['message' => 'Book already exists'], 200);
    }
}

public function removeFinbook($finbookId)
{  
    $finbook = Finbook::find($finbookId);
    if (!$finbook) {
    return response()->json(['message' => 'book not found'], 404);
}

    if (auth()->id() !== $finbook->user_id) {
    return response()->json(['message' => 'Unauthorized to delete this book'], 403);
}
    $deleted = $finbook->delete();
    if ($deleted) {
        return response()->noContent(200);

}   else {
        return response()->json([
        'message' => 'Failed to delete Book'
        ], 500);
}
}


    public function getUserFinBook( ) //show user finished book library
   {
        $finbooks = Finbook::with('book')
        ->where('user_id', Auth::id())
        ->get()
        ->map(function ($finbook) {
        return [
                'book_id' => $finbook->book_id,
                'book_name' => $finbook->book->book_name,
                'cover_image'=>$finbook->book->cover_image,   
               // 'pdf'=>$finbook->book->pdf, 
               //  'author_name' => $finbook->book->author->author_name,
                'category_name' => $finbook->book->category->category_name,        
        ];
        });
        if ($finbooks->isEmpty()) {
            return response()->noContent(200);
        }
        
    return response()->json(['finbooks' => $finbooks], 200);

    }

  
public function countFinishedBooksForUser($userId) // count num of Books finished reading
{
 
    $user = User::find($userId);

    if (!$user) {
        return response()->json(['message' => "User not found"], 404);
    }

    $finishedBooksCount = Finbook::where('user_id', $userId)
     ->count();

    return response()->json([
        'finished_books_count' => $finishedBooksCount,
    ], 200);
}

public function uploadUserImage(Request $request) // personal photo
{
    $validatedData = $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $user = auth()->user();
    $photoName = null;
    $photoPath = null; 

    if ($request->hasFile('image')) {
        $photo = $request->file('image');
        $photoName = time() . '.' . $photo->getClientOriginalExtension();
        
        // Store the photo using the custom user_photos disk
        $photo->storeAs('user_photos', $photoName, 'user_photos'); 
       
        $baseUrl =  /*env('APP_URL') .*/  '/'. 'user_photos/';
        $photoUrl = $baseUrl . $photoName;
        
        $user->image = $photoUrl;
        $user->save(); 

        return response()->json([
            'photo_path' => $photoUrl,
        ]);
    } else {
        return response()->json([
            'message' => 'No photo provided',
        ]);
    }
}


    public function AddSeggOrCom(Request $request) //Suggestion Or Complaint
{
    $validatedData = $request->validate([
        'suggestionorcomplaint' => 'required|string',
    ]);
    $suggestionorcomplaint = SuggeorComp::create([
        'user_id' => Auth::id(), 
        'suggestionorcomplaint' => $request->suggestionorcomplaint,
    ]);

    if ($suggestionorcomplaint) {
    // Retrieve the user's details
    $user = User::find($suggestionorcomplaint->user_id);

    if ($user) {
        return response()->json([
            'suggestionorcomplaint' => $suggestionorcomplaint,
            'user' => [
                'name' => $user->name,
            ]
           
        ], 200);
    } else {
        return response()->json([
            'message' => 'User not found'
        ], 500);
    }
} else {
    return response()->json([
        'message' => 'Failed'
    ], 500);
}
}

public function deleteSegge($suggestionorcomplaintId)
{
    $suggestionorcomplaint = SuggeorComp::find($suggestionorcomplaintId);
    if (!$suggestionorcomplaint) {
        return response()->json(['message' => 'suggestion or complaint not found'], 404);
    }
    if (auth()->id() !== $suggestionorcomplaint->user_id) {
        return response()->json(['message' => 'Unauthorized to delete'], 403);
    }
    $deleted = $suggestionorcomplaint->delete();
    if ($deleted) {
        return response()->noContent(200);
    } else {
        return response()->json([
            'message' => 'Failed to delete'
        ], 500);
    }
}








}
