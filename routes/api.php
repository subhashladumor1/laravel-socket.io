<?php

use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/send', [MessageController::class, 'send']);
    Route::get('/pending', [MessageController::class, 'pending']);
    Route::get('/messages', [MessageController::class, 'messages']);
    Route::post('/mark-delivered/{id}', [MessageController::class, 'markDelivered']);
    Route::post('/mark-read', [MessageController::class, 'markAsRead']);
    Route::get('/online-users', [MessageController::class, 'getOnlineUsers']);
});
