<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Display Antrian - {{ config('app.name', 'Sistem Antrian') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen" x-data="displayQueue()" x-init="init()">
        <!-- Header -->
        <header class="bg-white/10 backdrop-blur-sm border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-3xl font-bold text-white">📺 Display Antrian</h1>
                        <div class="bg-white/20 rounded-full px-4 py-2">
                            <span class="text-white font-medium" x-text="currentTime">--:--:--</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 rounded-lg px-4 py-2">
                            <span class="text-white text-sm">Auto Refresh: </span>
                            <span class="text-green-300 font-medium">ON</span>
                        </div>
                        <a href="{{ route('home') }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors">
                            🏠 Home
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Current Queue Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Now Serving -->
            <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl p-8 mb-8">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">SEDANG DILAYANI</h2>

                    <div x-show="currentQueue" class="slide-in">
                        <div class="inline-block bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-8 text-white shadow-lg">
                            <div class="text-8xl font-bold mb-4 pulse-animation" x-text="currentQueue?.queue_number">---</div>
                            <div class="text-2xl font-medium" x-text="currentQueue?.queue_type?.name">---</div>
                            <div class="text-lg opacity-90 mt-2" x-text="currentQueue?.customer_name || 'Pelanggan'">---</div>
                        </div>
                    </div>

                    <div x-show="!currentQueue" class="text-gray-500">
                        <div class="text-6xl mb-4">⏳</div>
                        <p class="text-xl">Tidak ada antrian yang sedang dilayani</p>
                    </div>
                </div>
            </div>

            <!-- Next Queue Section -->
            <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">ANTRIAN SELANJUTNYA</h2>

                <div x-show="nextQueues.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <template x-for="(queue, index) in nextQueues.slice(0, 3)" :key="queue.id">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white text-center transform hover:scale-105 transition-transform">
                            <div class="text-4xl font-bold mb-2" x-text="queue.queue_number">---</div>
                            <div class="text-lg font-medium" x-text="queue.queue_type.name">---</div>
                            <div class="text-sm opacity-90 mt-1" x-text="queue.customer_name || 'Pelanggan'">---</div>
                        </div>
                    </template>
                </div>

                <div x-show="nextQueues.length === 0" class="text-center text-gray-500">
                    <div class="text-4xl mb-4">📋</div>
                    <p class="text-lg">Tidak ada antrian selanjutnya</p>
                </div>
            </div>

            <!-- Queue by Type -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <template x-for="(queues, typeName) in queuesByType" :key="typeName">
                    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-800" x-text="typeName">---</h3>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium"
                                  x-text="queues.filter(q => q.status === 'waiting').length + ' menunggu'">0 menunggu</span>
                        </div>

                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            <template x-for="queue in queues.slice(0, 8)" :key="queue.id">
                                <div class="flex items-center justify-between p-3 rounded-lg"
                                     :class="queue.status === 'called' ? 'bg-yellow-100 border border-yellow-300' :
                                            queue.status === 'served' ? 'bg-green-100 border border-green-300' :
                                            'bg-gray-50 border border-gray-200'">
                                    <div class="flex items-center space-x-3">
                                        <span class="font-bold text-lg" x-text="queue.queue_number">---</span>
                                        <div>
                                            <div class="font-medium text-gray-800" x-text="queue.customer_name || 'Pelanggan'">---</div>
                                            <div class="text-sm text-gray-500" x-text="formatTime(queue.created_at)">---</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium"
                                              :class="queue.status === 'waiting' ? 'bg-blue-100 text-blue-800' :
                                                     queue.status === 'called' ? 'bg-yellow-100 text-yellow-800' :
                                                     queue.status === 'served' ? 'bg-green-100 text-green-800' :
                                                     'bg-gray-100 text-gray-800'"
                                              x-text="getStatusText(queue.status)">---</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Statistics -->
            <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-8 mt-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">STATISTIK HARI INI</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                        <div class="text-3xl font-bold" x-text="stats.total">0</div>
                        <div class="text-blue-100 mt-1">Total Antrian</div>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-6 text-white">
                        <div class="text-3xl font-bold" x-text="stats.waiting">0</div>
                        <div class="text-yellow-100 mt-1">Menunggu</div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                        <div class="text-3xl font-bold" x-text="stats.called">0</div>
                        <div class="text-orange-100 mt-1">Dipanggil</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                        <div class="text-3xl font-bold" x-text="stats.served">0</div>
                        <div class="text-green-100 mt-1">Dilayani</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white/10 backdrop-blur-sm border-t border-white/20 py-4 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-white/80">
                    {{ config('app.name', 'Sistem Antrian') }} -
                    <span x-text="currentDate">{{ date('d/m/Y') }}</span>
                    | Last Update: <span x-text="lastUpdate">--:--:--</span>
                </p>
            </div>
        </footer>
    </div>

   <script>
function displayQueue() {
    return {
        currentQueue: null,
        nextQueues: [],
        queuesByType: {},
        stats: { total: 0, waiting: 0, called: 0, served: 0 },
        currentTime: '',
        lastUpdate: '',
        tts: null,
        lastAnnouncedQueue: null,

        init() {
            // Initialize TTS for display
            this.initializeTTS();

            this.updateTime();
            this.fetchQueues();

            setInterval(() => { this.updateTime(); }, 1000);
            setInterval(() => { this.fetchQueues(); }, 5000);
        },

        initializeTTS() {
            try {
                this.tts = new window.QueueTextToSpeech({
                    volume: 1.0,  // Full volume for display
                    rate: 0.7,    // Slower for clarity
                    language: 'id-ID'
                });

                console.log('Display TTS initialized');
            } catch (error) {
                console.error('Failed to initialize display TTS:', error);
            }
        },

        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID');
        },

        async fetchQueues() {
            try {
                const response = await fetch('/api/display-queues');
                if (response.ok) {
                    const data = await response.json();
                    await this.updateQueueData(data);
                    this.lastUpdate = new Date().toLocaleTimeString('id-ID');
                }
            } catch (error) {
                console.error('Error fetching queue data:', error);
            }
        },

        async updateQueueData(data) {
            const previousCurrentQueue = this.currentQueue;

            // Update data
            this.currentQueue = data.queues.find(q => q.status === 'called') || null;
            this.nextQueues = data.queues
                .filter(q => q.status === 'waiting')
                .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            // Group by type
            this.queuesByType = data.queues.reduce((acc, queue) => {
                const typeName = queue.queue_type.name;
                if (!acc[typeName]) {
                    acc[typeName] = [];
                }
                acc[typeName].push(queue);
                return acc;
            }, {});

            // Update stats
            this.stats = {
                total: data.queues.length,
                waiting: data.queues.filter(q => q.status === 'waiting').length,
                called: data.queues.filter(q => q.status === 'called').length,
                served: data.queues.filter(q => q.status === 'served').length
            };

            // Check for new queue calls and announce
            if (this.currentQueue &&
                this.currentQueue.id !== previousCurrentQueue?.id &&
                this.currentQueue.id !== this.lastAnnouncedQueue) {

                // Only announce if queue was called recently (within last 30 seconds)
                const calledTime = new Date(this.currentQueue.called_at);
                const now = new Date();

                if (now - calledTime < 30000) {
                    await this.announceCurrentQueue();
                    this.lastAnnouncedQueue = this.currentQueue.id;
                }
            }
        },

        async announceCurrentQueue() {
            if (this.tts && this.currentQueue) {
                try {
                    await this.tts.announceQueue(
                        this.currentQueue.queue_number,
                        this.currentQueue.queue_type.name,
                        this.currentQueue.customer_name
                    );

                    console.log('Announced queue:', this.currentQueue.queue_number);
                } catch (error) {
                    console.error('Failed to announce queue:', error);
                }
            }
        },

        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        getStatusText(status) {
            const statusMap = {
                'waiting': 'Menunggu',
                'called': 'Dipanggil',
                'served': 'Dilayani',
                'cancelled': 'Dibatalkan'
            };
            return statusMap[status] || status;
        }
    }
}

// Auto-start TTS announcement when page loads
document.addEventListener('DOMContentLoaded', () => {
    // Add manual test button for display
    const testButton = document.createElement('button');
    testButton.className = 'fixed bottom-20 left-4 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors z-40';
    testButton.textContent = '🎤 Test TTS';
    testButton.onclick = () => {
        if (window.displayTTS) {
            window.displayTTS.announceQueue('A001', 'Test Layanan', 'Test Customer');
        }
    };

    document.body.appendChild(testButton);
});
</script>
</body>
</html>

