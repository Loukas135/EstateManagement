<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EstateController;
use App\Http\Controllers\ExtraController;
use App\Http\Controllers\FilterController;
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
        Route::post('extras/add', 'add')->middleware('role.admin');
        Route::get('extras/{id}', 'get_by_id');
        Route::get('extras/', 'get_all');
        Route::put('extras/{id}', 'update')->middleware('role.admin');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login_as_customer', 'login_as_customer');
    Route::post('login_as_seller', 'login_as_seller');
    Route::post('login_as_admin', 'login_as_admin');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});


Route::controller(EstateController::class)->middleware('auth:sanctum', 'role.admin')->group(function () {
    Route::put('estate/{id}/approve', 'approve');
    Route::get('estate/show_unapproved', 'show_unapproved');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('chat/{userId}', [ChatController::class, 'getOrCreateChat']);
    Route::post('send-message', [ChatController::class, 'sendMessage']);
    Route::get('messages/{chatId}', [ChatController::class, 'fetchMessages']);
    Route::get('user-chats', [ChatController::class, 'getUserChats']);
});
