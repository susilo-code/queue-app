<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QueueTypeController extends Controller
{
    public function index()
    {
        $queueTypes = QueueType::withCount('todayQueues')->get();
        return view('admin.queue-types.index', compact('queueTypes'));
    }

    public function create()
    {
        return view('admin.queue-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:queue_types',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7',
        ]);

        QueueType::create($request->all());

        return redirect()->route('admin.queue-types.index')
            ->with('success', 'Jenis antrian berhasil ditambahkan!');
    }

    public function edit(QueueType $queueType)
    {
        return view('admin.queue-types.edit', compact('queueType'));
    }

    public function update(Request $request, QueueType $queueType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:queue_types,code,' . $queueType->id,
            'description' => 'nullable|string',
            'color' => 'required|string|size:7',
        ]);

        $queueType->update($request->all());

        return redirect()->route('admin.queue-types.index')
            ->with('success', 'Jenis antrian berhasil diperbarui!');
    }

    public function destroy(QueueType $queueType)
    {
        $queueType->delete();

        return redirect()->route('admin.queue-types.index')
            ->with('success', 'Jenis antrian berhasil dihapus!');
    }
}
