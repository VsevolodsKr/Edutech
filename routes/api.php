<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\ContentsController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\LikesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register/send', [AuthorizationController::class, 'registration_store']);
Route::post('login/send', [AuthorizationController::class, 'login_store']);
Route::post('update/send', [AuthorizationController::class, 'update_store']);
Route::post('admin_update/send', [AuthorizationController::class, 'admin_update_store']);
Route::post('admin_register/send', [AuthorizationController::class, 'admin_registration_store']);

Route::get('playlists/all', [PlaylistController::class, 'get_all']);
Route::get('playlists/{id}', [PlaylistController::class, 'get_teacher_playlists']);
Route::get('playlists/find/{id}', [PlaylistController::class, 'get_single']);
Route::get('playlists/{id}/teacher', [PlaylistController::class, 'get_teacher']);
Route::get('playlists/amount/{id}', [PlaylistController::class, 'get_amount']);
Route::post('add_playlist/send', [PlaylistController::class, 'add_playlist']);
Route::post('playlists/update/{id}/send', [PlaylistController::class, 'store']);
Route::delete('playlists/delete/{id}', [PlaylistController::class, 'delete']);

Route::get('contents/{id}', [ContentsController::class, 'get_teacher_contents']);
Route::get('contents/find/{id}', [ContentsController::class, 'get_single']);
Route::get('playlists/{id}/contents', [ContentsController::class, 'get_playlist_contents']);
Route::get('contents/amount/{id}', [ContentsController::class, 'get_amount']);
Route::get('contents/playlist/{id}/amount', [ContentsController::class, 'get_playlist_contents_amount']);
Route::post('add_content/send', [ContentsController::class, 'add_content']);
Route::post('contents/update/{id}/send', [ContentsController::class, 'store']);
Route::delete('contents/delete/{id}', [ContentsController::class, 'delete']);

Route::get('teachers/all', [TeacherController::class, 'get_all']);

Route::post('likes/check', [LikesController::class, 'check_like']);
Route::post('likes/add', [LikesController::class, 'add_like']);
Route::delete('likes/delete/{id}', [LikesController::class, 'delete_like']);