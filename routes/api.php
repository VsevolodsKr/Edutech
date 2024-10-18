<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\PlaylistController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register/send', [AuthorizationController::class, 'registration_store']);
Route::post('/login/send', [AuthorizationController::class, 'login_store']);
Route::post('/update/send', [AuthorizationController::class, 'update_store']);
Route::post('/admin_update/send', [AuthorizationController::class, 'admin_update_store']);
Route::post('/admin_register/send', [AuthorizationController::class, 'admin_registration_store']);

Route::get('/playlists/{id}', [PlaylistController::class, 'get_playlists']);
Route::post('add_playlist/send', [PlaylistController::class, 'add_playlist']);