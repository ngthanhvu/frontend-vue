<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\MailController;

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
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->get('user', [AuthController::class, 'getUser']);
Route::post('password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);

Route::post('cart', [CartController::class, 'addToCart']);
Route::get('cart/{userId}', [CartController::class, 'getCart']);
Route::delete('cart/{cartId}', [CartController::class, 'removeFromCart']);
Route::put('/cart/update/{cartId}', [CartController::class, 'updateQuantity']);
Route::put('/cart/update/discount/{cartId}', [CartController::class, 'updateDiscount']);
Route::delete('cart/clear/{userId}', [CartController::class, 'clearCart']);

Route::apiResource('coupons', CouponController::class);
Route::post('/coupons/apply', [CouponController::class, 'apply']);

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{orderId}', [OrderController::class, 'getOrderStatus']);
Route::get('/orders/user/{userId}', [OrderController::class, 'getOrdersByUserId']);
Route::get('/orders', [OrderController::class, 'getOdersAll']);
Route::put('/orders/{orderId}', [OrderController::class, 'updateOrderStatus']);
Route::delete('/orders/{orderId}', [OrderController::class, 'deleteOrder']);

Route::post('/vnpay', [VnpayController::class, 'create']);
Route::get('/vnpay/callback', [VnpayController::class, 'callback']);

Route::get('orders/{orderId}/details', [OrderDetailController::class, 'index']);
Route::post('order-details', [OrderDetailController::class, 'store']);

Route::put('user/update-info', [AuthController::class, 'updateInfo']);
Route::put('user/update-password', [AuthController::class, 'updatePassword']);

Route::get('products/category/{categoryId}', [ProductController::class, 'showByCategory']);

Route::post('/sendMail', [MailController::class, 'sendMail']);
