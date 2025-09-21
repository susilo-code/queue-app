<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QueueManagementController extends Controller
{
    public function index()
    {
        $queues = Queue::with('queueType')
            ->whereDate('created_at', today())
            ->orderBy('created_at')
            ->get();

        return view('admin.queue-management.index', compact('queues'));
    }

    public function call(Queue $queue)
    {
        $queue->update([
            'status' => 'called',
            'called_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function serve(Queue $queue)
    {
        $queue->update([
            'status' => 'served',
            'served_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function cancel(Queue $queue)
    {
        $queue->update(['status' => 'cancelled']);

        return response()->json(['success' => true]);
    }
}
