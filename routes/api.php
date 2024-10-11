<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register/send', [AuthorizationController::class, 'registration_store']);
Route::post('/login/send', [AuthorizationController::class, 'login_store']);
Route::post('/update/send', [AuthorizationController::class, 'update_store']);