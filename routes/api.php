<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EstateController;
use App\Http\Controllers\ExtraController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\ReportController;
use App\Models\Estate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

Route::controller(EstateController::class)->group(function () {
    Route::get('estates/{id}', 'get_by_id');
    Route::get('estates/', 'get_all');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(EstateController::class)->group(function () {
        Route::post('estates/add', 'add')->middleware('role.seller');
        Route::put('estates/{id}/sold', 'soldEstate')->middleware('role.seller');
        Route::put('estates/{id}', 'update')->middleware('role.seller');
        Route::get('seller/estates/', 'showSellerEstates')->middleware('role.seller');
        Route::delete('estates/delete/{id}', 'delete')->middleware('role.seller');
    });

    Route::controller(ExtraController::class)->group(function () {
        Route::post('extras/add_work', 'add_work');
        Route::get('extras/me', 'get_me');
        Route::delete('extras/work/{id}', 'delete_work');
        Route::post('extras/{id}', 'update');
    });

    Route::controller(ChatController::class)->group(function () {
        Route::get('chat/{userId}',  'getOrCreateChat');
        Route::post('send-message',  'sendMessage');
        Route::get('messages/{chatId}',  'fetchMessages');
        Route::get('user-chats',  'getUserChats');
    });
});


Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('register_service', 'register_service');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});


Route::controller(EstateController::class)->middleware('auth:sanctum', 'role.admin')->group(function () {
    Route::put('estate/{id}/approve', 'approve');
    Route::get('estate/show_unapproved', 'showAdminEstates');
});

Route::controller(ReportController::class)->middleware('auth:sanctum', 'role.admin')->group(function () {
    Route::get('/reports/estates',  'estateReport');
    Route::get('/reports/users',  'userReport');
    Route::get('/reports/works',  'workReport');
    Route::get('/reports/custom',  'customReport');
});


Route::controller(CategoriesController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::post('/categories', 'store');
    Route::post('/categories/{id}', 'show');
    Route::put('/categories/{id}', 'update');
    Route::delete('/categories/{id}', 'destroy');
    Route::get('/estate/categories', 'getEstateCategories');
    Route::get('/extra/categories', 'getExtraCategories');
});

Route::controller(ExtraController::class)->group(function () {
    Route::get('extras/',  'get_all');
    Route::get('extras/{id}', 'get_by_id');
});
