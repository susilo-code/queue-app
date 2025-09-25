@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Selamat Datang</h1>
        <p class="text-xl text-gray-600">Silahkan pilih jenis layanan untuk mengambil nomor antrian</p>
    </div>

    <!-- Queue Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
        @forelse($queueTypes as $queueType)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <!-- Header with color -->
                <div class="h-2" style="background-color: {{ $queueType->color }}"></div>

                <div class="p-8">
                    <!-- Queue Type Info -->
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center text-white text-3xl font-bold"
                             style="background-color: {{ $queueType->color }}">
                            {{ $queueType->code }}
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $queueType->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $queueType->description }}</p>
                    </div>

                    <!-- Statistics -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $queueType->today_queues_count }}</div>
                            <div class="text-sm text-gray-600">Total Hari Ini</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $queueType->waiting_queues_count }}</div>
                            <div class="text-sm text-blue-600">Menunggu</div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('queue.create', $queueType) }}"
                       class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-lg font-semibold text-center block hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-md">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ambil Nomor Antrian
                        </span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📋</div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Layanan Tersedia</h3>
                <p class="text-gray-600">Silahkan hubungi administrator untuk informasi lebih lanjut.</p>
            </div>
        @endforelse
    </div>

    <!-- Live Stats -->
    <div class="bg-white rounded-xl shadow-lg p-8" x-data="liveStats()" x-init="init()">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Statistik Hari Ini</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white text-center">
                <div class="text-3xl font-bold" x-text="stats.total || '0'">0</div>
                <div class="text-blue-100">Total Antrian</div>
            </div>
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white text-center">
                <div class="text-3xl font-bold" x-text="stats.waiting || '0'">0</div>
                <div class="text-yellow-100">Sedang Menunggu</div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white text-center">
                <div class="text-3xl font-bold" x-text="stats.served || '0'">0</div>
                <div class="text-green-100">Telah Dilayani</div>
            </div>
        </div>
    </div>
</div>

<script>
function liveStats() {
    return {
        stats: {
            total: 0,
            waiting: 0,
            served: 0
        },

        init() {
            this.fetchStats();
            // Update every 30 seconds
            setInterval(() => {
                this.fetchStats();
            }, 30000);
        },

        async fetchStats() {
            try {
                const response = await fetch('/api/queue-stats');
                if (response.ok) {
                    this.stats = await response.json();
                }
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        }
    }
}
</script>
@endsection

