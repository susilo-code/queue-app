

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-gray-600 mt-1">Kelola sistem antrian dan monitor aktivitas</p>
            </div>
            <div class="text-sm text-gray-500">
                {{ date('d F Y') }}
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Antrian Hari Ini</p>
                    <p class="text-3xl font-bold">{{ $todayQueues }}</p>
                    <p class="text-blue-100 text-sm mt-1">+{{ $todayQueues }} dari kemarin</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Sedang Menunggu</p>
                    <p class="text-3xl font-bold">{{ $waitingQueues }}</p>
                    <p class="text-yellow-100 text-sm mt-1">Antrian aktif</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Telah Dilayani</p>
                    <p class="text-3xl font-bold">{{ $servedQueues }}</p>
                    <p class="text-green-100 text-sm mt-1">Hari ini</p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.queue-management.index') }}"
               class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 text-center transition-colors">
                <div class="text-blue-600 text-2xl mb-2">🎯</div>
                <div class="text-sm font-medium text-blue-900">Kelola Antrian</div>
            </a>

            <a href="{{ route('admin.queue-types.index') }}"
               class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 text-center transition-colors">
                <div class="text-green-600 text-2xl mb-2">📝</div>
                <div class="text-sm font-medium text-green-900">Jenis Antrian</div>
            </a>

            <a href="{{ route('queue.display') }}"
               class="bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg p-4 text-center transition-colors">
                <div class="text-purple-600 text-2xl mb-2">📺</div>
                <div class="text-sm font-medium text-purple-900">Display Antrian</div>
            </a>

            <button onclick="resetDaily()"
               class="bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg p-4 text-center transition-colors">
                <div class="text-red-600 text-2xl mb-2">🔄</div>
                <div class="text-sm font-medium text-red-900">Reset Harian</div>
            </button>
        </div>
    </div>

    <!-- Queue Types Overview -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Ringkasan Jenis Antrian</h2>
            <a href="{{ route('admin.queue-types.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                + Tambah Baru
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($queueTypes as $queueType)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                             style="background-color: {{ $queueType->color }}">
                            {{ $queueType->code }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $queueType->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $queueType->description }}</p>
                        </div>
                    </div>
                    @if($queueType->is_active)
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Aktif</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Nonaktif</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded p-3 text-center">
                        <div class="text-xl font-bold text-gray-900">{{ $queueType->today_queues_count }}</div>
                        <div class="text-xs text-gray-600">Hari Ini</div>
                    </div>
                    <div class="bg-blue-50 rounded p-3 text-center">
                        <div class="text-xl font-bold text-blue-600">{{ $queueType->waiting_queues_count }}</div>
                        <div class="text-xs text-blue-600">Menunggu</div>
                    </div>
                </div>

                <div class="mt-4 flex space-x-2">
                    <a href="{{ route('admin.queue-types.edit', $queueType) }}"
                       class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded text-sm text-center hover:bg-gray-200 transition-colors">
                        Edit
                    </a>
                    <form action="{{ route('admin.queue-types.destroy', $queueType) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus?')"
                          class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-100 text-red-700 px-3 py-2 rounded text-sm hover:bg-red-200 transition-colors">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8">
                <div class="text-gray-400 text-4xl mb-4">📋</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Jenis Antrian</h3>
                <p class="text-gray-600 mb-4">Tambahkan jenis antrian pertama untuk memulai</p>
                <a href="{{ route('admin.queue-types.create') }}"
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Tambah Jenis Antrian
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Aktivitas Terkini</h2>

        <div class="space-y-4" x-data="recentActivity()" x-init="fetchActivity()">
            <template x-for="activity in activities" :key="activity.id">
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                    <div class="w-2 h-2 rounded-full"
                         :class="activity.type === 'new' ? 'bg-blue-500' :
                                 activity.type === 'called' ? 'bg-yellow-500' :
                                 activity.type === 'served' ? 'bg-green-500' : 'bg-gray-500'">
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900" x-text="activity.message"></p>
                        <p class="text-xs text-gray-500" x-text="activity.time"></p>
                    </div>
                    <span class="text-xs font-medium px-2 py-1 rounded-full"
                          :class="activity.type === 'new' ? 'bg-blue-100 text-blue-800' :
                                  activity.type === 'called' ? 'bg-yellow-100 text-yellow-800' :
                                  activity.type === 'served' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                          x-text="activity.type_label">
                    </span>
                </div>
            </template>

            <div x-show="activities.length === 0" class="text-center py-8">
                <div class="text-gray-400 text-2xl mb-2">📝</div>
                <p class="text-gray-500">Belum ada aktivitas hari ini</p>
            </div>
        </div>
    </div>
</div>

<script>
function recentActivity() {
    return {
        activities: [],

        async fetchActivity() {
            try {
                const response = await fetch('/api/recent-activity');
                if (response.ok) {
                    this.activities = await response.json();
                }
            } catch (error) {
                console.error('Error fetching activity:', error);
            }
        }
    }
}

function resetDaily() {
    if (confirm('Yakin ingin reset antrian harian? Tindakan ini tidak dapat dibatalkan.')) {
        fetch('/admin/reset-daily', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Gagal reset antrian harian');
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}

// Auto refresh dashboard every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endsection
