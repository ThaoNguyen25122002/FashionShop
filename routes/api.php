<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Member\AuthMemberController;
use App\Http\Controllers\Member\CategoryProductController;
use App\Http\Controllers\Member\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// ====================== Home ================== //
Route::get('products/new', [CategoryProductController::class, 'newProducts']);
Route::get('categories/sort', [CategoryController::class, 'sortCategories']);
Route::get('product/{id}/detail', [ProductController::class, 'show']);
Route::get('category/{id}/products', [ProductController::class, 'getProductsByCategory']);
Route::get('products/search', [ProductController::class, 'search']);
// ===================== Test Api Here =================//













// ====================== Auth Member ==================== //
Route::post('register', [UserManagementController::class, 'create']);
Route::post('login', [AuthMemberController::class, 'login']);
Route::post('forgot-password', [AuthMemberController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [AuthMemberController::class, 'ResetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    // ==================== Profile ==================== //
    Route::get('profile', [AdminProfileController::class, 'index']);
    Route::post('profile/update', [AdminProfileController::class, 'update']);
    Route::post('order/create', [OrderController::class, 'storeOrder']);
    Route::get('orders', [OrderController::class, 'orderOfUser']);
    Route::get('order/{id}/details', [OrderController::class, 'orderDetail']);
});

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum', 'isAdmin')->group(function () {
        // ==================== Dashboard =================== //
        Route::get('orders/pending', [OrderController::class, 'getPendingOrders']);
        Route::put('order/{orderId}/confirm', [OrderController::class, 'confirmOrder']);
        Route::get('stats', [DashboardController::class, 'index']);
        // ==================== Profile ====================
        Route::get('profile', [AdminProfileController::class, 'index']);
        Route::post('profile/update', [AdminProfileController::class, 'update']);
        // ==================== Auth ====================
        Route::post('logout', [AuthController::class, 'logout']);
        // ==================== Category ====================
        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('category/create', [CategoryController::class, 'create']);
        Route::get('category/{id}', [CategoryController::class, 'getCategory']);
        Route::put('category/{id}/update', [CategoryController::class, 'updateCategory']);
        Route::delete('category/{id}/delete', [CategoryController::class, 'deleteCategory']);
        // ==================== Product ====================
        Route::get('products', [ProductController::class, 'index']);
        Route::post('product/create', [ProductController::class, 'create']);
        Route::get('product/{id}', [ProductController::class, 'show']);
        Route::post('product/{id}/update', [ProductController::class, 'update']);
        Route::delete('product/{id}/delete', [ProductController::class, 'delete']);

        // ==================== User Management ====================
        Route::get('users', [UserManagementController::class, 'index']);
        Route::post('user/create', [UserManagementController::class, 'create']);
        Route::get('user/{id}/edit', [UserManagementController::class, 'show']);
        Route::post('user/{id}/update', [UserManagementController::class, 'update']);
        Route::delete('user/{id}/delete', [UserManagementController::class, 'delete']);
        
        // ==================== Order =================== //

        Route::get('order/{id}/details', [OrderController::class, 'orderDetailAdmin']);
        Route::get('orders', [OrderController::class, 'getOrders']);
        Route::put('/order/{order}/status', [OrderController::class, 'updateStatus']);
        Route::get('/orders/search', [OrderController::class, 'search']);
    });
});
