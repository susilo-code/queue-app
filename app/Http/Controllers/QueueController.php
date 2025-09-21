<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index()
    {
        $queueTypes = QueueType::where('is_active', true)
            ->withCount(['todayQueues', 'waitingQueues'])
            ->get();

        return view('queue.index', compact('queueTypes'));
    }

    public function create(QueueType $queueType)
    {
        return view('queue.create', compact('queueType'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'queue_type_id' => 'required|exists:queue_types,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $queue = Queue::create([
            'queue_type_id' => $request->queue_type_id,
            'queue_number' => Queue::generateQueueNumber($request->queue_type_id),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
        ]);

        return redirect()->route('queue.ticket', $queue->id)
            ->with('success', 'Nomor antrian berhasil diambil!');
    }

    public function ticket(Queue $queue)
    {
        return view('queue.ticket', compact('queue'));
    }

    public function display()
    {
        $queues = Queue::with('queueType')
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'called'])
            ->orderBy('created_at')
            ->get()
            ->groupBy('queueType.name');

        return view('queue.display', compact('queues'));
    }
}
