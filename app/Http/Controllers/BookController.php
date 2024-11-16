<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Notifications\QuestionNotification;
use App\Models\Note;
use App\Models\Finbook;
use App\Models\Author;
use App\Models\Category;
use App\Models\ReadingTime;
use App\Models\Question;
use App\Models\Replay;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BookController extends Controller


{

    public function storeCategory(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'category_name_en' => 'nullable|string|max:255|unique:categories,category_name_en'
        ]);
    
        $category = Category::create($validatedData);
        return response()->json([
            'category' => $category,
        ], 201);
    }

public function deleteCategory($id)
{
    // Find the category by ID
    $category = Category::find($id);

    if (!$category) {
        return response()->json([
            'message' => 'Category not found',
        ], 404);
    }

    $books = $category->books;

    foreach ($books as $book) {
        $book->delete();
    }
    
    // Delete the category
    $category->delete();

    return response()->noContent(200);
}
public function getAllCategories(Request $request)
{
    // Fetch all categories from the database
    $categories = Category::all();

    // Check the user's language preference
    $language = $request->header('Accept-Language', 'ar');

    // Return the categories based on the user's language preference
    if ($language == 'ar') {
        $categoriesArray = $categories->map(function ($category) {
            if ($category->category_name !== null) {
                return [
                    'id' => $category->id,
                    'name' => $category->category_name
                ];
            }
        })->filter();
    } else {
        $categoriesArray = $categories->map(function ($category) {
            if ($category->category_name_en !== null) {
                return [
                    'id' => $category->id,
                    'name' => $category->category_name_en
                ];
            }
        })->filter();
    }

    return response()->json(['categories' => $categoriesArray->values()], 200);
}


public function getBooksByCategory(Request $request, $category_id)
{
    // Fetch the category information based on the preferred language
    $language = $request->header('Accept-Language', 'ar');
    $category = Category::select(
        'id',
        $language == 'ar' ? 'category_name' : 'category_name_en as category_name'
    )
    ->with(['books' => function ($query) use ($language) {
        $query->select(
            'id',
            'book_name',
            'cover_image',
            'author_id',
            'category_id'
        );
    }, 'books.author' => function ($query) {
        $query->select('id', 'author_name');
    }])
    ->find($category_id);

    if (!$category) {
        return response()->json(['message' => 'Category Not found'], 404);
    }

    // Check if there are books in the category
    if ($category->books->isEmpty()) {
        return response()->json(['message' => 'No book here in this category'], 200);
    }

    // Map the books to include only the desired information
    $books = $category->books->map(function ($book) {
        return [
            'book_id' => $book->id,
            'book_name' => $book->book_name,
            'cover_image' => $book->cover_image,
            'author_name' => $book->author->author_name,
        ];
    });

    return response()->json([
        'category_id' => $category->id,
        'category_name' => $category->category_name,
        'books' => $books
    ], 200);
}

    public function store(Request $request) //add Book to DB
    {
       /* if (!Auth::check() ||!Auth::user()->is_admin) {
           return response()->json(['message' => 'Unauthorized'], 401);
        }*/
        $validatedData = $request->validate([
            'book_name' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,bmp,jpg,gif,svg ',
            'abstract' => 'nullable|string',
            'pdf' => 'nullable|mimes:pdf,docx,doc,xlsx,csv,gdoc,pptx,ppt,|max:60000',
            'author_name' => 'required|string|max:255',
            'category_name' => 'nullable|string|max:255', 
            'category_name_en' => 'required|string|max:255',    
        ]);
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $coverImageName = time() . '.' . $coverImage->getClientOriginalExtension();
            
            // Store the image using the custom cover_images disk
            $coverImage->storeAs('cover_images', $coverImageName, 'cover_images');    
          //  $photoUrl = '/' . 'cover_images/' . $coverImageName;
            $baseUrl =  /*env('APP_URL') .*/  '/'. 'cover_images/';
            $photoUrl = $baseUrl . $coverImageName;
            $coverImagePath = $photoUrl; 
        }


        $pDFPath = null;
        if ($request->hasFile('pdf')) {
            $pDF = $request->file('pdf');
            $pDFName = time() . '.' . $pDF->getClientOriginalExtension();
            $pDFPath = $pDF->storeAs('public/pdfs', $pDFName);
            $pDFPath = Storage::url($pDFPath);
}
      $author = Author::firstOrCreate([
        'author_name' => $validatedData['author_name']]);

     $category = Category::firstOrCreate([
        'category_name' => $validatedData['category_name'],
        'category_name_en' => $validatedData['category_name_en']
    ]);

       // Create a book 
       $book = new Book([
        'book_name' => $validatedData['book_name'],
        'cover_image' => $coverImagePath,
        'abstract' => $validatedData['abstract'],
        'pdf' => $pDFPath,
    ]);

    $book->author()->associate($author);
    $book->category()->associate($category);

//store book
    $book->save();

    return response()->json([
      //  'message' => 'Book created successfully',
       'id' => $book->id,
        'book' => $book,
    ], 201);   
}


public function index() // show all book
{
    $books = Book::with(['author:id,author_name', 'category:id,category_name,category_name_en'])
        ->get(['id', 'book_name', 'cover_image', 'pdf','author_id','category_id'])
        ->map(function ($book) {

            return [
                'id' => $book->id,
                'book_name' => $book->book_name,
                'cover_image' => $book->cover_image,
                'pdf' => $book->pdf,
                //'author_name' => $book->author->author_name, 
                'category_name_en' => $book->category->category_name_en, 
            ];
        });
    return response()->json([
        'books' => $books,
    ]);
}

public function search(Request $request) //search book by book_name
{
    $validatedData = $request->validate([
        'book_name' => 'required|string|max:255',
    ]);
    $book_name = $request->input('book_name');
    $query = Book::select('id','book_name', 'cover_image', 'abstract','pdf', 'author_id');

    if ($book_name) {
        $query->where('book_name', $book_name);
    }
    $books = $query->get();
    $result = $books->map(function ($book) {
        $image = url($book->cover_image);
        return [
            'book_id' => $book->id,
            'book_name' => $book->book_name,
            'cover_image' => $book->cover_image,
            'pdf' => $book->pdf,
            'author_name' =>$book ->author->author_name,
        ];
    });
    if ($result->isNotEmpty()) {
        return response()->json([
            'books' => $result,
        ], 200);
    } else {
        return response()->json([
            'message' => 'Book not found',
        ], 404);
    }
}

public function show($id) // details of book
{
    $book = Book::with('author', 'category')->find($id);
//::with('author', 'category')-> you can delete it
    if ($book) {
        $data = [
            'id' => $book->id,
            'book_name' => $book->book_name,
            'cover_image' => $book->cover_image,
            'pdf' => $book->pdf,
            'abstract' => $book->abstract,
            'author_name' => $book->author->author_name, 
            'category_name_en' => $book->category->category_name_en, 
        ];
        return response()->json([
            'data' => $data,
        ]);
    } else {
        return response()->json([
            'message' => 'Book not found ',
        ], 404);
    }
}

public function addQuestion(Request $request)
{
    $validatedData = $request->validate([
        'book_id' => 'required|exists:books,id',  
        'question' => 'required|string', 
    ]);
    $question = Question::create([
        'user_id'=> Auth::id(),
        'book_id' => $request->book_id,
        'question' => $request->question,
    ]);
 $admin = User::where( 'is_admin')->first();
    $admin->notify(new QuestionNotification($question));

    if ($question) { 
    $user = User::find($question->user_id);
    if ($user) {
        return response()->json([
            'question' => $question,
            'user' => [
                'name' => $user->name,
                'user_photo' => $user->image,
            ]
        ], 200);
    } 
    else {
        return response()->json([
            'message' => 'User not found'
        ], 500);
    }
} }

public function updateQuestion(Request $request, $questionId)
{
    $validatedData = $request->validate([
        'book_id' => 'required|exists:books,id', 
        'question' => 'required|string', 
    ]);
    $question = Question::find($questionId);

    if (!$question) {
        return response()->json(['message' => 'Question not found'], 404);
    }

    if ($question->user_id!= auth()->user()->id) {
        return response()->json(['message' => 'You do not have permission'], 403);
    }
    $updated = $question->update([
        'book_id' => $request->book_id,
        'question' => $request->question,
    ]);
    if ($updated) {
        $user = User::find($question->user_id);
        if ($user) {
            return response()->json([
                'question' => $question,
                'user' => [
                    'name' => $user->name,
                    'user_photo' => $user->image,
                ]
            ], 200);
        } 
     } else {
        return response()->json([
            'message' => 'Failed to update question'
        ], 500);
    }
}
public function deleteQuestion($questionId)
{
    $question = Question::find($questionId);
    if (!$question) {
        return response()->json(['message' => 'question not found'], 404);
    }

    if (auth()->id() !== $question->user_id) {
        return response()->json(['message' => 'Unauthorized to delete this question'], 403);
    }
    $deleted = $question->delete();
    if ($deleted) {
        return response()->noContent(200);
    } else {
        return response()->json([
            'message' => 'Failed to delete question'
        ], 500);
    }
}

public function replay(Request $request)
{
    $validatedData = $request->validate([
        'replay' => 'required|string',
        'question_id' => 'required|exists:questions,id',
    ]);
    $replay = Replay::create([
        'user_id' => Auth::id(), 
        'question_id' => $request->question_id,
        'replay' => $request->replay,
    ]);

    if ($replay) {
    // Retrieve the user's details
    $user = User::find($replay->user_id);

    if ($user) {
        return response()->json([
            'replay' => $replay,
            'user' => [
                'name' => $user->name,
                'user_photo' => $user->image,
            ]
        ], 200);
    } else {
        return response()->json([
            'message' => 'User not found'
        ], 500);
    }
} else {
    return response()->json([
        'message' => 'Failed to create replay'
    ], 500);
}
}

public function deleteReplay($replayId)
{
    $replay = Replay::find($replayId);

    if (!$replay) {
        return response()->json(['message' => 'replay not found'], 404);
    }
    if (auth()->id() !== $replay->user_id) {
        return response()->json(['message' => 'Unauthorized to delete this replay'], 403);
    }

    $deleted = $replay->delete();
    if ($deleted) {
        return response()->noContent(200);
    } else {
        return response()->json([
            'message' => 'Failed to delete replay'
        ], 500);
    }
}
public function getTotalCommentCount($bookId) {

    $questionCount = Question::where('book_id', $bookId)->count();

        $replies = Replay::whereIn('question_id', Question::where('book_id', $bookId)->pluck('id'))->count();

    // Calculate the total number of comments by adding the number of questions and replies
    $totalCommentCount = $questionCount + $replies;

    return response()->json([
        'total_comments' => $totalCommentCount
    ], 200);
}



}

