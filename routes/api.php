<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
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
        Route::put('estates/update/{id}', 'update')->middleware('role.seller');

        Route::get('estates/show_seller_estates', 'show_seller_estates')->middleware('role.seller');
        Route::delete('estates/delete/{id}', 'delete')->middleware('role.seller');
    });

    Route::controller(ExtraController::class)->group(function () {
        Route::post('extras/add', 'add')->middleware('role.admin');
        Route::get('extras/get/{id}', 'get_by_id');
        Route::get('extras/get', 'get');
        Route::put('extras/update/{id}', 'update')->middleware('role.admin');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login_as_customer', 'login_as_customer');
    Route::post('login_as_seller', 'login_as_seller');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});


Route::middleware('auth:sanctum', 'role.admin')->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::put('estates/approve', 'approve');
        Route::get('estates/show_unapproved', 'show_unapproved');
    });
});
