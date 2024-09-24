<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorizationController;

Route::post('/register/enter', [AuthorizationController::class, 'store']);