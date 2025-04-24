<?php

use Illuminate\Support\Facades\Route;

// Main page route (shows the Blade view)
Route::get('/', function () {
    return view('todo');
});

// API routes for AJAX requests (used by JS)
Route::prefix('api')->group(function () {
    Route::get('todos', [App\Http\Controllers\TodoController::class, 'index']);
    Route::post('todos', [App\Http\Controllers\TodoController::class, 'store']);
    Route::put('todos/{id}', [App\Http\Controllers\TodoController::class, 'update']);
    Route::delete('todos/{id}', [App\Http\Controllers\TodoController::class, 'destroy']);
    // Could add more endpoints here later
});
