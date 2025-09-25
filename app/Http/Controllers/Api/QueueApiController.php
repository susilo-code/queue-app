<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use App\Models\QueueType;
use Illuminate\Http\Request;

class QueueApiController extends Controller
{
    public function stats()
    {
        $today = today();

        $stats = [
            'total' => Queue::whereDate('created_at', $today)->count(),
            'waiting' => Queue::whereDate('created_at', $today)->where('status', 'waiting')->count(),
            'called' => Queue::whereDate('created_at', $today)->where('status', 'called')->count(),
            'served' => Queue::whereDate('created_at', $today)->where('status', 'served')->count(),
            'cancelled' => Queue::whereDate('created_at', $today)->where('status', 'cancelled')->count(),
        ];

        return response()->json($stats);
    }

    public function displayQueues()
    {
        $queues = Queue::with('queueType')
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'called', 'served'])
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'queues' => $queues,
            'last_updated' => now()->toISOString()
        ]);
    }

    public function queueTypes()
    {
        $queueTypes = QueueType::where('is_active', true)
            ->select('id', 'name', 'code', 'color')
            ->get();

        return response()->json($queueTypes);
    }

    public function recentActivity()
    {
        $activities = [];

        // Get recent queue activities from today
        $recentQueues = Queue::with('queueType')
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentQueues as $queue) {
            $time = $queue->updated_at->format('H:i');

            switch ($queue->status) {
                case 'waiting':
                    $activities[] = [
                        'id' => $queue->id . '_new',
                        'type' => 'new',
                        'type_label' => 'Baru',
                        'message' => "Antrian {$queue->queue_number} ({$queue->queueType->name}) diambil",
                        'time' => $time
                    ];
                    break;

                case 'called':
                    if ($queue->called_at) {
                        $activities[] = [
                            'id' => $queue->id . '_called',
                            'type' => 'called',
                            'type_label' => 'Dipanggil',
                            'message' => "Antrian {$queue->queue_number} sedang dipanggil",
                            'time' => $queue->called_at->format('H:i')
                        ];
                    }
                    break;

                case 'served':
                    if ($queue->served_at) {
                        $activities[] = [
                            'id' => $queue->id . '_served',
                            'type' => 'served',
                            'type_label' => 'Selesai',
                            'message' => "Antrian {$queue->queue_number} telah dilayani",
                            'time' => $queue->served_at->format('H:i')
                        ];
                    }
                    break;

                case 'cancelled':
                    $activities[] = [
                        'id' => $queue->id . '_cancelled',
                        'type' => 'cancelled',
                        'type_label' => 'Dibatalkan',
                        'message' => "Antrian {$queue->queue_number} dibatalkan",
                        'time' => $time
                    ];
                    break;
            }
        }

        // Sort by time descending and limit to 10
        usort($activities, function($a, $b) {
            return strcmp($b['time'], $a['time']);
        });

        return response()->json(array_slice($activities, 0, 10));
    }
}

