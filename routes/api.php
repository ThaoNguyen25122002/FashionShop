<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('admin')->group(function(){
    Route::post('login',[AuthController::class,'login']);




    Route::middleware('auth:sanctum')->group(function(){
        // ==================== Profile ====================
        Route::get('profile',[AdminProfileController::class,'index']);
        Route::post('profile/update',[AdminProfileController::class,'update']);
        // ==================== Auth ====================
        Route::post('logout',[AuthController::class,'logout']);
        // ==================== Category ====================
        Route::get('categories',[CategoryController::class,'index']);
        Route::get('category/{id}',[CategoryController::class,'getCategory']);
        Route::put('category/{id}/update',[CategoryController::class,'updateCategory']);
        Route::delete('category/{id}/delete',[CategoryController::class,'deleteCategory']);
        // ==================== Product ====================
        Route::get('products',[ProductController::class,'index']);
        Route::post('product/create',[ProductController::class,'create']);
        Route::get('product/{id}',[ProductController::class,'show']);
        Route::post('product/{id}/update',[ProductController::class,'update']);
        Route::delete('product/{id}/delete',[ProductController::class,'delete']);
    });
});