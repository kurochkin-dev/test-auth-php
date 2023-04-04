<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\JwtMiddleware;

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

// Авторизация и регистрация
Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Маршруты для авторизованных пользователей
Route::middleware(['auth:sanctum'])->group(function () {
    // Получить текущего пользователя
    Route::get('user', function (Request $request) {
        return auth()->user();
    });

    // Каталог товаров
    Route::get('products', [ProductController::class, 'index']);

    // Создание товара
    Route::post('products', [ProductController::class, 'store']);

    // Страница с товарами
    Route::get('products-page', [ProductController::class, 'show']);
});
