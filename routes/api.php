<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTController;
use App\Http\Controllers\UserController;

Route::group(['middleware' => 'api'], function($router) {
    Route::post('/login', [JWTController::class, 'login']);
    Route::post('/logout', [JWTController::class, 'logout']);
    Route::post('/createAdmin', [JWTController::class, 'createAdmin']);


    Route::get('/getUsers', [UserController::class, 'get']);
    Route::post('/storeUser', [UserController::class, 'store']);
    Route::post('/deleteUser', [UserController::class, 'delete']);
});


