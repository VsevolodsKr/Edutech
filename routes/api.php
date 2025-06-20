<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthorizationController;
use App\Http\Controllers\Api\PlaylistsController;
use App\Http\Controllers\Api\ContentsController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\LikesController;
use App\Http\Controllers\Api\BookmarksController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CommentsController;
use App\Http\Controllers\Api\EngagementController;
use App\Http\Controllers\Api\DevelopersController;

// Public Routes
Route::get('playlists/latest', [PlaylistsController::class, 'latest']);
Route::get('teachers/find/{id}', [TeacherController::class, 'find_teacher']);
Route::get('contents/playlist/{id}/amount', [ContentsController::class, 'get_playlist_contents_amount']);
Route::get('playlists/all', [PlaylistsController::class, 'all']);
Route::get('playlists/active', [PlaylistsController::class, 'active']);
Route::get('playlists/{id}/teacher', [PlaylistsController::class, 'playlist_teacher']);
Route::get('teachers/all', [TeacherController::class, 'all']);
Route::post('playlists/search', [PlaylistsController::class, 'search']);
Route::post('teachers/search', [TeacherController::class, 'search_teachers']);
Route::get('playlists/amount/{id}', [PlaylistsController::class, 'get_amount']);
Route::get('contents/amount/{id}', [ContentsController::class, 'get_amount']);
Route::get('contents/find/{id}', [ContentsController::class, 'get_single']);
Route::get('playlists/{id}/contents', [ContentsController::class, 'get_playlist_contents']);
Route::get('likes/count_content/{id}', [LikesController::class, 'count_content_likes']);
Route::get('comments/content_amount/{id}', [CommentsController::class, 'count_comments']);
Route::get('comments/video/{id}', [CommentsController::class, 'get_video_comments']);
Route::get('playlists/find/{id}', [PlaylistsController::class, 'find']);
Route::get('playlists/teacher_playlists/{id}', [PlaylistsController::class, 'teacher_playlists']);
Route::get('playlists/teacher_active_playlists/{id}', [PlaylistsController::class, 'active_teacher_playlists']);
Route::get('teachers/{id}/playlists', [TeacherController::class, 'get_teacher_playlists']);

// Engagement routes
Route::get('engagement/teacher/{id}', [EngagementController::class, 'get_teacher_engagement']);
Route::get('contents/popular/{id}', [EngagementController::class, 'get_popular_contents']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // User Profile Routes
    Route::get('/user/profile', [AuthorizationController::class, 'get_profile']);
    Route::post('/user/update-profile', [AuthorizationController::class, 'update_profile']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
        // Teacher Management
        Route::get('/teachers', [TeacherController::class, 'index']);
        Route::post('/teachers', [TeacherController::class, 'store']);
        Route::put('/teachers/{id}', [TeacherController::class, 'update']);
        Route::delete('/teachers/{id}', [TeacherController::class, 'destroy']);

        // User Management
        Route::get('/users', [AuthorizationController::class, 'index']);
        Route::post('/users', [AuthorizationController::class, 'store']);
        Route::put('/users/{id}', [AuthorizationController::class, 'update']);
        Route::delete('/users/{id}', [AuthorizationController::class, 'destroy']);

        // Message Management
        Route::get('/messages', [ContactController::class, 'index']);
        Route::post('/messages/{id}/reply', [ContactController::class, 'reply']);
        Route::put('/messages/{id}/read', [ContactController::class, 'mark_as_read']);
        Route::delete('/messages/{id}', [ContactController::class, 'destroy']);
    });

    // Playlist Routes
    Route::post('playlists/add', [PlaylistsController::class, 'add']);
    Route::post('playlists/update/{id}', [PlaylistsController::class, 'update']);
    Route::delete('playlists/delete/{id}', [PlaylistsController::class, 'delete']);

    // Content Routes
    Route::get('contents/{id}', [ContentsController::class, 'get_teacher_contents']);
    Route::post('add_content/send', [ContentsController::class, 'add_content']);
    Route::post('contents/update/{id}/send', [ContentsController::class, 'store']);
    Route::delete('contents/delete/{id}', [ContentsController::class, 'delete']);

    // Like Routes
    Route::get('likes/count_user/{id}', [LikesController::class, 'count_user_likes']);
    Route::get('likes/user/{id}', [LikesController::class, 'get_user_likes']);
    Route::get('likes/count_teacher/{id}', [LikesController::class, 'count_teacher']);
    Route::post('likes/check', [LikesController::class, 'check_like']);
    Route::post('likes/add', [LikesController::class, 'add_like']);
    Route::delete('likes/delete/{id}', [LikesController::class, 'delete_like']);

    // Bookmark Routes
    Route::get('bookmarks/count_user/{id}', [BookmarksController::class, 'count_user_bookmarks']);
    Route::get('bookmarks/user/{id}', [BookmarksController::class, 'get_user_bookmarks']);
    Route::post('bookmarks/check', [BookmarksController::class, 'check_bookmark']);
    Route::post('bookmarks/add', [BookmarksController::class, 'add_bookmark']);
    Route::delete('bookmarks/delete/{id}', [BookmarksController::class, 'delete_bookmark']);

    // Comment Routes
    Route::get('comments/count_user/{id}', [CommentsController::class, 'count_user']);
    Route::get('comments/user/{id}', [CommentsController::class, 'get_user_comments']);
    Route::get('comments/count_teacher/{id}', [CommentsController::class, 'count_teacher']);
    Route::get('comments/teacher/{id}', [CommentsController::class, 'get_teacher_comments']);
    Route::get('comments/find/{id}', [CommentsController::class, 'find']);
    Route::post('comments/add', [CommentsController::class, 'add_comment']);
    Route::post('comments/{id}/edit', [CommentsController::class, 'edit_comment']);
    Route::delete('comments/delete/{id}', [CommentsController::class, 'delete_comment']);

    // Developer Dashboard Routes
    Route::prefix('developer')->group(function () {
        Route::get('/dashboard/stats', [EngagementController::class, 'get_dashboard_stats']);
        Route::get('/dashboard/activity', [EngagementController::class, 'get_system_activity']);
        Route::get('/dashboard/message-status', [ContactController::class, 'get_message_status_distribution']);
        Route::get('/dashboard/top-teachers', [TeacherController::class, 'get_top_teachers']);
        Route::get('/dashboard/unread-messages', [ContactController::class, 'get_unread_messages']);
    });
});

// Public Routes
Route::post('register/send', [AuthorizationController::class, 'registration_store']);
Route::post('login/send', [AuthorizationController::class, 'login_store']);
Route::post('update/send', [AuthorizationController::class, 'update_store']);
Route::post('admin_update/send', [AuthorizationController::class, 'admin_update_store']);
Route::post('admin_register/send', [AuthorizationController::class, 'admin_registration_store']);
Route::post('contact/send', [ContactController::class, 'send']);

// Developer Routes
Route::prefix('developer')->middleware('auth:sanctum')->group(function () {
    // Profile Management
    Route::post('/profile/update', [DevelopersController::class, 'update']);

    // Teacher Management
    Route::get('/teachers', [TeacherController::class, 'index']);
    Route::post('/teachers', [TeacherController::class, 'store']);
    Route::put('/teachers/{id}', [TeacherController::class, 'update']);
    Route::delete('/teachers/{id}', [TeacherController::class, 'destroy']);
    Route::delete('/teachers/{id}/playlists', [TeacherController::class, 'destroy_teacher_content']);

    // User Management
    Route::get('/users', [AuthorizationController::class, 'get_users']);
    Route::post('/users', [AuthorizationController::class, 'store_user']);
    Route::put('/users/{id}', [AuthorizationController::class, 'update_user']);
    Route::delete('/users/{id}', [AuthorizationController::class, 'delete_user']);

    // Message Management
    Route::get('/messages', [ContactController::class, 'index']);
    Route::post('/messages/{id}/reply', [ContactController::class, 'reply']);
    Route::put('/messages/{id}/read', [ContactController::class, 'mark_as_read']);
    Route::put('/messages/{id}/status', [ContactController::class, 'update_status']);
    Route::delete('/messages/{id}', [ContactController::class, 'destroy']);
});