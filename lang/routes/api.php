<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//_________________________________
//dont forget auth
//_________________________________

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Admin Activities:
//add medicine
Route::post('/medicine', [MedicineController::class, 'store']);
//edit medicine
Route::post('/medicine/{medicine}/edit', [MedicineController::class, 'update']);
//delete medicine
Route::delete('/medicine/{medicine}', [MedicineController::class, 'destroy']);
//send order
Route::post('/send/{id}', [OrderController::class, 'send_order']);
//view all orders
Route::get('/admin_order', [OrderController::class, 'show_order_admin']);
//change prepare status
Route::post('/prepare/{id}', [OrderController::class, 'change_prepare']);
//change payment status
Route::post('/payment/{id}', [OrderController::class, 'change_payment']);

//Pharmasist Activities:
//order
Route::post('/order/{id}', [OrderController::class, 'store'])->middleware('auth');
//register
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
//login
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
//logout
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');
//recieve order
Route::post('/recieve/{id}', [OrderController::class, 'recieve order'])->middleware('auth');
//add favorite
Route::post('/favorite', [MedicineController::class, 'add_favorite'])->middleware('auth');
//remove favorite
Route::delete('/favorite/{id}', [MedicineController::class, 'remove_favorite'])->middleware('auth');
//view favorites
Route::get('/favorite', [MedicineController::class, 'view_favorites']);
//for developer not users remember to delete before productin
Route::get('/who', [AuthController::class, 'who']);
//view user orders
Route::get('/order', [OrderController::class, 'show_order'])->middleware('auth');


//For ALL Users:
//view all
Route::get('/', [MedicineController::class, 'index'])->middleware('auth');
//view single
Route::get('/medicine/{id}', [MedicineController::class, 'show'])->middleware('auth');
//view orders