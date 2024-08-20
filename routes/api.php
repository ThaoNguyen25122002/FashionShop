<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::prefix('admin')->group(function(){
    Route::post('login',[AuthController::class,'login']);




    Route::middleware('auth:sanctum')->group(function(){
        Route::post('logout',[AuthController::class,'logout']);
    });
});