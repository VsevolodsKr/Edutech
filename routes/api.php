<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\ContentsController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\LikesController;
use App\Http\Controllers\Api\BookmarksController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CommentsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register/send', [AuthorizationController::class, 'registration_store']);
Route::post('login/send', [AuthorizationController::class, 'login_store']);
Route::post('update/send', [AuthorizationController::class, 'update_store']);
Route::post('admin_update/send', [AuthorizationController::class, 'admin_update_store']);
Route::post('admin_register/send', [AuthorizationController::class, 'admin_registration_store']);

Route::get('playlists/all', [PlaylistController::class, 'get_all']);
Route::post('playlists/search', [PlaylistController::class, 'search_playlists']);
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
Route::post('teachers/search', [TeacherController::class, 'search_teachers']);
Route::get('teachers/find/{id}', [TeacherController::class, 'find_teacher']);

Route::get('likes/count_content/{id}', [LikesController::class, 'count_content_likes']);
Route::get('likes/count_user/{id}', [LikesController::class, 'count_user_likes']);
Route::get('likes/user/{id}', [LikesController::class, 'get_user_likes']);
Route::post('likes/check', [LikesController::class, 'check_like']);
Route::post('likes/add', [LikesController::class, 'add_like']);
Route::delete('likes/delete/{id}', [LikesController::class, 'delete_like']);

Route::get('bookmarks/count_user/{id}', [BookmarksController::class, 'count_user_bookmarks']);
Route::get('bookmarks/user/{id}', [BookmarksController::class, 'get_user_bookmarks']);
Route::post('bookmarks/check', [BookmarksController::class, 'check_bookmark']);
Route::post('bookmarks/add', [BookmarksController::class, 'add_bookmark']);
Route::delete('bookmarks/delete/{id}', [BookmarksController::class, 'delete_bookmark']);

Route::post('contact/send', [ContactController::class, 'send']);

Route::get('comments/content_amount/{id}', [CommentsController::class, 'count_comments']);
Route::get('comments/count_user/{id}', [CommentsController::class, 'count_user']);
Route::get('comments/video/{id}', [CommentsController::class, 'get_video_comments']);
Route::get('comments/user/{id}', [CommentsController::class, 'get_user_comments']);
Route::get('comments/find/{id}', [CommentsController::class, 'find']);
Route::post('comments/add', [CommentsController::class, 'add_comment']);
Route::post('comments/{id}/edit', [CommentsController::class, 'edit_comment']);
Route::delete('comments/delete/{id}', [CommentsController::class, 'delete_comment']);