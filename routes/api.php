<?php
use illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ReadprogressController;
use App\Http\Controllers\RateBookController;
use App\Http\Controllers\PdfandimageController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify_code', [AuthController::class, 'verifyCode']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    
    Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/user-photo', [UserController::class, 'uploadUserImage']);

    Route::post('/storeCategory', [BookController::class, 'storeCategory']);
    Route::delete('/categories/{id}', [BookController::class, 'deleteCategory']); //delete category and its books
    Route::get('/Allcategories', [BookController::class, 'getAllCategories']); 
    Route::get('/category/{category_id}', [BookController::class, 'getBooksByCategory']); // category s book

    Route::post('/storeBook', [BookController::class, 'store']);
    Route::post('/updateBook/{id}', [AdminController::class, 'update']);

    Route::get('/books', [BookController::class, 'index']); //show all book
    Route::post('/search',[ BookController::class,'search']); // search book_name
    Route::delete('/books/{bookId}',[ AdminController::class,'deleteBook']);
    Route::get('/bookshow/{id}', [BookController::class, 'show']); // book details

    Route::post('/storeAuthor', [AuthorController::class, 'storeAuthor']);
    Route::post('/searchAuthor',[ AuthorController::class,'searchAuthor']);
    Route::get('/authors/{author_id}/books', [AuthorController::class, 'showAuthorBooks']); // show authors book

    Route::post('/favoriteAddDelete',[FavoriteController::class,'store_and_deletFavorite']);
    Route::get('/getbookfavorite',[FavoriteController::class,'getUserFavorites']);
    Route::post('/add/note',[UserController::class,'addnote']); 
    Route::delete('/delete/note/{noteId}', [UserController::class,'deleteNote']);
    Route::get('/Allnote',[UserController::class,'getAllNotes']); // all for each user
    Route::post('/Add/finbook',[UserController::class,'addFinbook']); // finished lib
    Route::delete('/Delete/finbook/{finbookId}',[UserController::class,'removeFinbook']); // finished lib
    Route::get('/getfinbook',[UserController::class,'getUserFinBook']);
    Route::get('/user/{userId}/finished-books-count',[UserController::class,'countFinishedBooksForUser']); //progress

    Route::get('/reading/start', [ReadprogressController::class, 'startReading']);
    Route::put('/reading/stop/{readingTimeId}', [ReadprogressController::class, 'endReading']);
    Route::get('/reading/total-time/{userId}', [ReadprogressController::class, 'getAllReadingSessions']); //progress
    Route::post('/addRate',[RateBookController::class,'storeRating']); 
    Route::delete('/remove/{rating_id}', [RateBookController::class,'removeRating']);

    Route::post('/add/question',[BookController::class,'addQuestion']); 
    Route::post('/update/question/{questionId}',[BookController::class,'updateQuestion']); 
    Route::delete('/deleteQuestion/{questionId}',[BookController::class,'deleteQuestion']); 
    Route::post('/add/replay',[BookController::class,'replay']); 
    Route::delete('/deleteReplay/{replayId}',[BookController::class,'deleteReplay']); 
    Route::get('/totalcomments/{bookId}',[BookController::class,'getTotalCommentCount']); 

    Route::post('/add/segg',[UserController::class,'AddSeggOrCom']); 
    Route::delete('/deletSegg/{suggestionorcomplaintId}',[UserController::class,'deleteSegge']); 

    Route::get('popularbooks', [AdminController::class, 'getMostPopularBooks']);
    Route::get('rateBooks', [AdminController::class, 'getBooksRatings']);
    Route::get('display/Com', [AdminController::class, 'displayCom']);
    Route::get('displayQuestion', [AdminController::class, 'displayQuestion']);

  //  Route::post('/upload',[PdfandimageController::class,'upload']); 
   Route::get('/download/{filename}', [PdfandimageController::class, 'download']); //download pdf 

    
    Route::get('/email/verify', function () {
        return response()->json(['message' => 'Email verification notice']);
    })->name('verification.notice');
    
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified']);
    })->middleware(['signed'])->name('verification.verify');
    
    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent']);
    })->middleware(['throttle:6,1'])->name('verification.send');
   

});




