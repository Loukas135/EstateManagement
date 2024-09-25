<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EstateController;
use App\Http\Controllers\ExtraController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ReportController;
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


    Route::middleware('roles:seller')->controller(EstateController::class)->group(function () {
        Route::post('estates/add', 'add');
        Route::put('estates/{id}/sold', 'soldEstate');
        Route::put('estates/{id}', 'update');
        Route::get('seller/estates/', 'showSellerEstates');
        Route::delete('estates/delete/{id}', 'delete');
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


Route::middleware('auth:sanctum', 'roles:admin,manager')->group(function () {
    Route::controller(EstateController::class)->group(function () {
        Route::put('estate/{id}/approve', 'approve');
        Route::get('estate/show_unapproved', 'showAdminEstates');
    });


    Route::controller(ReportController::class)->group(function () {
        Route::get('/reports/estates', 'estateReport');
        Route::get('/reports/users', 'userReport');
        Route::get('/reports/works', 'workReport');
        Route::get('/reports/custom', 'customReport');
    });


    Route::controller(ManagerController::class)->group(function () {
        Route::post('/managers', 'addManager');
        Route::get('/managers', 'getAllManagers');
        Route::put('/managers/{id}', 'updateManager');
        Route::delete('/managers/{id}', 'deleteManager');
    });
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
    Route::get('extras/', 'get_all');
    Route::get('extras/{id}', 'get_by_id');
});
