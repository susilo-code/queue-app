

<?php
// routes/api.php
use App\Http\Controllers\Api\QueueApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API routes for real-time features
Route::get('/queue-stats', [QueueApiController::class, 'stats']);
Route::get('/display-queues', [QueueApiController::class, 'displayQueues']);
Route::get('/queue-types', [QueueApiController::class, 'queueTypes']);
Route::get('/recent-activity', [QueueApiController::class, 'recentActivity']);

