<?php

use App\Http\Controllers\Api\{
    CastMemberController,
    CategoryController,
    GenreController
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('genres', GenreController::class);
Route::apiResource('cast-members', CastMemberController::class);

Route::get('/', function () {
    return response()->json(['message' => 'success']);
});
