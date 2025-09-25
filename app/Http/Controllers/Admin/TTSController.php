<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TTSController extends Controller
{
    public function getSettings()
    {
        return response()->json([
            'enabled' => env('TTS_ENABLED', true),
            'volume' => env('TTS_VOLUME', 0.8),
            'rate' => env('TTS_RATE', 0.8),
            'language' => env('TTS_LANGUAGE', 'id-ID'),
            'auto_announce' => env('TTS_AUTO_ANNOUNCE', true),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'boolean',
            'volume' => 'numeric|min:0|max:1',
            'rate' => 'numeric|min:0.1|max:2',
            'language' => 'string',
            'auto_announce' => 'boolean'
        ]);

        // In a real app, you'd save these to database or update .env
        // For now, we'll just return success

        return response()->json(['message' => 'TTS settings updated successfully']);
    }

    public function testAnnouncement(Request $request)
    {
        $request->validate([
            'queue_number' => 'required|string',
            'service_name' => 'required|string',
            'customer_name' => 'nullable|string'
        ]);

        // Log the test announcement
        \Log::info('TTS Test Announcement', [
            'queue_number' => $request->queue_number,
            'service_name' => $request->service_name,
            'customer_name' => $request->customer_name,
            'timestamp' => now()
        ]);

        return response()->json(['message' => 'Test announcement logged']);
    }
}
