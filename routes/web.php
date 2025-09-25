<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\QueueTypeController;
use App\Http\Controllers\Admin\QueueManagementController;
use App\Http\Controllers\Api\QueueApiController;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES (NO AUTHENTICATION REQUIRED)
Route::get('/', [QueueController::class, 'index'])->name('home');
Route::get('/display', [QueueController::class, 'display'])->name('queue.display');

// QUEUE ROUTES - PUBLIC (Anyone can take queue numbers)
Route::prefix('queue')->name('queue.')->group(function () {
    Route::get('/create/{queueType}', [QueueController::class, 'create'])->name('create');
    Route::post('/store', [QueueController::class, 'store'])->name('store');
    Route::get('/ticket/{queue}', [QueueController::class, 'ticket'])->name('ticket');
});

// API ROUTES - PUBLIC (For real-time features)
Route::prefix('api')->group(function () {
    Route::get('/queue-stats', [QueueApiController::class, 'stats']);
    Route::get('/display-queues', [QueueApiController::class, 'displayQueues']);
    Route::get('/queue-types', [QueueApiController::class, 'queueTypes']);
    Route::get('/recent-activity', [QueueApiController::class, 'recentActivity']);
});

// AUTHENTICATED USER ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) { return redirect()->route('admin.dashboard'); }
        return view('dashboard'); })->name('dashboard');
});

// ADMIN ONLY ROUTES
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Queue Types Management
    Route::resource('queue-types', QueueTypeController::class);

    // Queue Management
    Route::prefix('queue-management')->name('queue-management.')->group(function () {
        Route::get('/', [QueueManagementController::class, 'index'])->name('index');
        Route::post('/{queue}/call', [QueueManagementController::class, 'call'])->name('call');
        Route::post('/{queue}/serve', [QueueManagementController::class, 'serve'])->name('serve');
        Route::post('/{queue}/cancel', [QueueManagementController::class, 'cancel'])->name('cancel');
    });

    // Utility routes
    Route::post('/reset-daily', [QueueManagementController::class, 'resetDaily'])->name('reset-daily');
});

// DEBUG ROUTE (REMOVE IN PRODUCTION)
Route::get('/debug-auth', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_admin' => $user->isAdmin(),
            ]
        ]);
    }

    return response()->json(['authenticated' => false]);
});

// Add TTS routes to web.php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // ... existing admin routes ...

    // TTS Settings
    Route::prefix('tts')->name('tts.')->group(function () {
        Route::get('/settings', [TTSController::class, 'getSettings'])->name('settings');
        Route::post('/settings', [TTSController::class, 'updateSettings'])->name('settings.update');
        Route::post('/test', [TTSController::class, 'testAnnouncement'])->name('test');
    });
});

require __DIR__.'/auth.php';
