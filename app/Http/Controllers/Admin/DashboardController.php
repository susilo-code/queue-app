<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todayQueues = Queue::whereDate('created_at', today())->count();
        $waitingQueues = Queue::where('status', 'waiting')
            ->whereDate('created_at', today())->count();
        $servedQueues = Queue::where('status', 'served')
            ->whereDate('created_at', today())->count();

        $queueTypes = QueueType::withCount([
            'todayQueues',
            'waitingQueues'
        ])->get();

        return view('admin.dashboard', compact(
            'todayQueues',
            'waitingQueues',
            'servedQueues',
            'queueTypes'
        ));
    }
}
