<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('users', UserController::class);
Route::get('users/search/{search}', [UserController::class, 'search']);
Route::patch('users/{id}/status', [UserController::class, 'updateStatus']);

Route::get('/health',function () {
    return response()->json(['status' => 'OK'], 200);
});
