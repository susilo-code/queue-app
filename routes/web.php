<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QueueTypeController;
use App\Http\Controllers\Admin\QueueManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QueueController::class, 'index'])->name('home');
Route::get('/display', [QueueController::class, 'display'])->name('queue.display');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Queue routes
Route::prefix('queue')->name('queue.')->group(function () {
    Route::get('/create/{queueType}', [QueueController::class, 'create'])->name('create');
    Route::post('/store', [QueueController::class, 'store'])->name('store');
    Route::get('/ticket/{queue}', [QueueController::class, 'ticket'])->name('ticket');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('queue-types', QueueTypeController::class);

    Route::prefix('queue-management')->name('queue-management.')->group(function () {
        Route::get('/', [QueueManagementController::class, 'index'])->name('index');
        Route::post('/{queue}/call', [QueueManagementController::class, 'call'])->name('call');
        Route::post('/{queue}/serve', [QueueManagementController::class, 'serve'])->name('serve');
        Route::post('/{queue}/cancel', [QueueManagementController::class, 'cancel'])->name('cancel');
    });
});

require __DIR__.'/auth.php';
