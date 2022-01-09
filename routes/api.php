<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::middleware('api:auth')->group(function () {
            Route::get('/users', [AuthController::class, 'users']);
            Route::get('/user', [AuthController::class, 'user']);
            Route::put('/update_user/{id}', [AuthController::class, 'updateUser']);
            Route::delete('/delete_user/{id}', [AuthController::class, 'deleteUser']);
        });
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/reset_password_link', [AuthController::class, 'sendEmailPasswordLink']);
        Route::post('/reset_password', [AuthController::class, 'resetPassword']);
    });
});
