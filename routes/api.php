<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health',function () {
    return response()->json(['status' => 'OK'], 200);
});

//user routes
Route::apiResource('users', UserController::class);
Route::get('users/search/{search}', [UserController::class, 'search']);
Route::patch('users/{id}/status', [UserController::class, 'updateStatus']);


//category routes
Route::apiResource('categories', CategoyController::class);

//brand routes
Route::apiResource('brands', BrandController::class);

//product routes
Route::apiResource('products', ProductController::class);
Route::post('products/{id}/images', [ProductController::class, 'addImage']);
Route::delete('products/{id}/images/{imageId}', [ProductController::class, 'deleteImage']);