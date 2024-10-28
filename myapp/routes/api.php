<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::get('email/verification/{id}/{code}', [AuthController::class, 'verify']);

Route::post('password/forgot', [AuthController::class, 'forgotPassword']);

Route::post('password/reset', [AuthController::class, 'resetPassword']);


//newsletter
Route::post('subscribe', [NewsletterController::class, 'subscribe']);
Route::get('unsubscribe/{email}', [NewsletterController::class, 'unsubscribe']);

//Message contacts
Route::post('contact', [ContactController::class, 'send']);



Route::group(['middleware' => ['auth:api']], function () {

    // Récupération des données du user connecté
    Route::get('profil', [AuthController::class, 'profil']);
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:api', 'admin'])->group(function () {

    // Routes pour les posts 
    Route::post('create_post', [PostController::class, 'create']);
    Route::delete('delete_post/{id}', [PostController::class, 'delete']);
    Route::get('list_post', [PostController::class, 'list']);
    Route::get('show_post/{id}', [PostController::class, 'show']);
    Route::put('update_post/{id}', [PostController::class, 'update']);

    // Routes pour les catégories
    Route::post('categories', [CategoryController::class, 'store']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::delete('delete_categories/{id}', [CategoryController::class, 'delete']);

    //Routes pour newsletter
    Route::post('send-newsletter', [NewsletterController::class, 'send']);
});
