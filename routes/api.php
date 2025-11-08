<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);

    Route::get('/books/search', [BookController::class, 'search']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);

    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    Route::middleware([EnsureUserIsAdmin::class])->group(function () {
        Route::apiResource('/users', UserController::class)->except(['index', 'show']);
        Route::apiResource('/books', BookController::class)->except(['index', 'show']);
    });

    Route::post('/users/{user}/borrow', [BorrowingController::class, 'borrow']);
    Route::post('/users/{user}/return', [BorrowingController::class, 'return']);
    Route::get('/users/{user}/borrowed', [BorrowingController::class, 'userBorrowed']);
});
