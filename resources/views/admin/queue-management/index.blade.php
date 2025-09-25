

@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="queueManagementWithTTS()" x-init="init()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Antrian</h1>
            <p class="text-gray-600 mt-1">Kelola dan pantau antrian dengan pengumuman suara</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- TTS Status Indicator -->
            <div class="bg-white rounded-lg shadow px-4 py-2">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">🔊 TTS:</span>
                    <span x-text="ttsStatus"
                          :class="ttsEnabled ? 'text-green-600' : 'text-red-600'"
                          class="text-sm font-medium">
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Current Queue Display with TTS Controls -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-8 mb-8 text-white relative">
        <!-- TTS Controls in header -->
        <div class="absolute top-4 right-4 flex space-x-2">
            <button @click="testTTS()"
                    class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-colors"
                    title="Test Text-to-Speech">
                🎤
            </button>
            <button @click="toggleTTS()"
                    :class="ttsEnabled ? 'bg-green-500/30' : 'bg-red-500/30'"
                    class="hover:bg-white/30 p-2 rounded-lg transition-colors"
                    :title="ttsEnabled ? 'Disable TTS' : 'Enable TTS'">
                <span x-text="ttsEnabled ? '🔊' : '🔇'"></span>
            </button>
        </div>

        <div class="text-center">
            <h2 class="text-2xl font-bold mb-4">SEDANG DILAYANI</h2>
            <div x-show="currentQueue" id="current-queue-display">
                <div class="text-8xl font-bold mb-4 animate-pulse" x-text="currentQueue?.queue_number">---</div>
                <div class="text-2xl font-medium" x-text="currentQueue?.queue_type?.name">---</div>
                <div class="text-lg opacity-90" x-text="currentQueue?.customer_name || 'Pelanggan'">---</div>
            </div>
            <div x-show="!currentQueue" class="text-blue-100">
                <div class="text-6xl mb-4">⏳</div>
                <p class="text-xl">Tidak ada antrian yang sedang dilayani</p>
            </div>
        </div>
    </div>

    <!-- Quick Control Buttons with TTS -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Kontrol Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button @click="callNext()"
                    :disabled="!nextQueue"
                    :class="nextQueue ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                    class="text-white px-6 py-3 rounded-lg font-medium transition-colors relative">
                🔔 Panggil Selanjutnya
                <span x-show="isCalling" class="absolute inset-0 flex items-center justify-center bg-blue-700 rounded-lg">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                </span>
            </button>

            <button @click="serveCurrent()"
                    :disabled="!currentQueue"
                    :class="currentQueue ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
                    class="text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ✅ Selesai Layani
            </button>

            <button @click="repeatAnnouncement()"
                    :disabled="!currentQueue"
                    :class="currentQueue ? 'bg-orange-600 hover:bg-orange-700' : 'bg-gray-300 cursor-not-allowed'"
                    class="text-white px-6 py-3 rounded-lg font-medium transition-colors">
                🔁 Ulangi Pengumuman
            </button>

            <button @click="refreshData()"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                🔄 Refresh
            </button>
        </div>

        <!-- Next Queue Info with Announce Button -->
        <div x-show="nextQueue" class="mt-6 p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Antrian Selanjutnya:</h4>
                    <div class="flex items-center space-x-4">
                        <span class="text-2xl font-bold text-blue-600" x-text="nextQueue?.queue_number">---</span>
                        <div>
                            <div class="font-medium text-blue-900" x-text="nextQueue?.queue_type?.name">---</div>
                            <div class="text-sm text-blue-700" x-text="nextQueue?.customer_name || 'Pelanggan'">---</div>
                        </div>
                    </div>
                </div>
                <button @click="previewAnnouncement(nextQueue)"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    🎤 Preview
                </button>
            </div>
        </div>
    </div>

    <!-- Queue List with TTS buttons -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                Daftar Antrian - <span x-text="filteredQueues.length">0</span> item
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="queue in filteredQueues" :key="queue.id">
                        <tr :class="queue.status === 'called' ? 'bg-yellow-50' :
                                   queue.status === 'served' ? 'bg-green-50' :
                                   queue.status === 'cancelled' ? 'bg-red-50' : ''">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3"
                                         :style="'background-color: ' + queue.queue_type.color">
                                        <span x-text="queue.queue_type.code"></span>
                                    </div>
                                    <div class="text-lg font-bold text-gray-900" x-text="queue.queue_number"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="queue.queue_type.name"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900" x-text="queue.customer_name || 'Pelanggan'"></div>
                                <div class="text-sm text-gray-500" x-text="queue.customer_phone || '-'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div x-text="formatTime(queue.created_at)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      :class="getStatusClass(queue.status)"
                                      x-text="getStatusText(queue.status)">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- Call Button with TTS -->
                                    <button x-show="queue.status === 'waiting'"
                                            @click="callQueueWithTTS(queue)"
                                            class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors flex items-center space-x-1">
                                        <span>🔔</span>
                                        <span>Panggil</span>
                                    </button>

                                    <!-- Repeat Announcement -->
                                    <button x-show="queue.status === 'called'"
                                            @click="repeatQueueAnnouncement(queue)"
                                            class="bg-orange-600 text-white px-3 py-1 rounded text-xs hover:bg-orange-700 transition-colors flex items-center space-x-1">
                                        <span>🔁</span>
                                        <span>Ulangi</span>
                                    </button>

                                    <!-- Serve Button -->
                                    <button x-show="queue.status === 'called'"
                                            @click="serveQueue(queue)"
                                            class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition-colors">
                                        Selesai
                                    </button>

                                    <!-- Cancel Button -->
                                    <button x-show="['waiting', 'called'].includes(queue.status)"
                                            @click="cancelQueue(queue)"
                                            class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function queueManagementWithTTS() {
    return {
        // Queue data
        queues: @json($queues ?? []),
        filteredQueues: [],
        currentQueue: null,
        nextQueue: null,

        // TTS status
        tts: null,
        ttsEnabled: true,
        ttsStatus: 'Loading...',
        isCalling: false,

        // Stats
        stats: {
            waiting: 0,
            called: 0,
            served: 0,
            total: 0
        },

        init() {
            // Initialize TTS
            this.initializeTTS();

            // Process queue data
            this.processQueues();
            this.filteredQueues = [...this.queues];

            // Auto refresh
            setInterval(() => {
                this.refreshData();
            }, 10000);

            console.log('Queue Management with TTS initialized');
        },

        initializeTTS() {
            try {
                this.tts = new window.QueueTextToSpeech({
                    volume: 0.8,
                    rate: 0.8,
                    language: 'id-ID'
                });

                this.ttsEnabled = this.tts.isEnabled;
                this.updateTTSStatus();

                // Listen for TTS events
                setTimeout(() => {
                    this.updateTTSStatus();
                }, 1000);

            } catch (error) {
                console.error('Failed to initialize TTS:', error);
                this.ttsStatus = 'Error';
            }
        },

        updateTTSStatus() {
            if (!this.tts) {
                this.ttsStatus = 'Not Available';
                return;
            }

            if (!this.tts.isSupported) {
                this.ttsStatus = 'Not Supported';
                return;
            }

            this.ttsStatus = this.tts.isEnabled ? 'Active' : 'Disabled';
            this.ttsEnabled = this.tts.isEnabled;
        },

        processQueues() {
            // Find current and next queue
            this.currentQueue = this.queues.find(q => q.status === 'called') || null;
            const waitingQueues = this.queues.filter(q => q.status === 'waiting')
                .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            this.nextQueue = waitingQueues[0] || null;

            // Calculate stats
            this.stats = {
                waiting: this.queues.filter(q => q.status === 'waiting').length,
                called: this.queues.filter(q => q.status === 'called').length,
                served: this.queues.filter(q => q.status === 'served').length,
                total: this.queues.length
            };
        },

        // TTS Controls
        toggleTTS() {
            if (this.tts) {
                this.tts.isEnabled = !this.tts.isEnabled;
                this.updateTTSStatus();
            }
        },

        async testTTS() {
            if (this.tts) {
                await this.tts.announceQueue('A001', 'Test Layanan', 'Test Customer');
            }
        },

        async previewAnnouncement(queue) {
            if (this.tts && queue) {
                await this.tts.announceQueue(
                    queue.queue_number,
                    queue.queue_type.name,
                    queue.customer_name
                );
            }
        },

        async repeatAnnouncement() {
            if (this.currentQueue && this.tts) {
                await this.tts.announceQueue(
                    this.currentQueue.queue_number,
                    this.currentQueue.queue_type.name,
                    this.currentQueue.customer_name
                );
            }
        },

        async repeatQueueAnnouncement(queue) {
            if (this.tts) {
                await this.tts.announceQueue(
                    queue.queue_number,
                    queue.queue_type.name,
                    queue.customer_name
                );
            }
        },

        // Queue Management with TTS
        async callNext() {
            if (!this.nextQueue) return;
            await this.callQueueWithTTS(this.nextQueue);
        },

        async callQueueWithTTS(queue) {
            this.isCalling = true;

            try {
                // Update queue status via API
                const response = await fetch(`{{ route('admin.queue-management.index') }}/${queue.id}/call`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    // Update local data
                    queue.status = 'called';
                    queue.called_at = new Date().toISOString();

                    // Make TTS announcement
                    if (this.tts) {
                        await this.tts.announceQueue(
                            queue.queue_number,
                            queue.queue_type.name,
                            queue.customer_name
                        );
                    }

                    // Update UI
                    this.processQueues();

                    // Show success notification
                    this.showNotification(`Antrian ${queue.queue_number} telah dipanggil dan diumumkan`, 'success');
                } else {
                    throw new Error('Failed to call queue');
                }
            } catch (error) {
                console.error('Error calling queue:', error);
                this.showNotification('Gagal memanggil antrian', 'error');
            } finally {
                this.isCalling = false;
            }
        },

        async serveCurrent() {
            if (!this.currentQueue) return;
            await this.serveQueue(this.currentQueue);
        },

        async serveQueue(queue) {
            try {
                const response = await fetch(`{{ route('admin.queue-management.index') }}/${queue.id}/serve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    queue.status = 'served';
                    queue.served_at = new Date().toISOString();
                    this.processQueues();

                    this.showNotification(`Antrian ${queue.queue_number} telah selesai dilayani`, 'success');
                } else {
                    throw new Error('Failed to serve queue');
                }
            } catch (error) {
                console.error('Error serving queue:', error);
                this.showNotification('Gagal menyelesaikan layanan antrian', 'error');
            }
        },

        async cancelQueue(queue) {
            if (!confirm('Yakin ingin membatalkan antrian ini?')) return;

            try {
                const response = await fetch(`{{ route('admin.queue-management.index') }}/${queue.id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    queue.status = 'cancelled';
                    this.processQueues();

                    this.showNotification(`Antrian ${queue.queue_number} telah dibatalkan`, 'success');
                } else {
                    throw new Error('Failed to cancel queue');
                }
            } catch (error) {
                console.error('Error cancelling queue:', error);
                this.showNotification('Gagal membatalkan antrian', 'error');
            }
        },

        async refreshData() {
            try {
                const response = await fetch(window.location.href);
                // In a real implementation, you'd have an API endpoint
                // that returns JSON data instead of reloading the page

                // For now, we'll just reload the page
                // location.reload();

                // Better approach: fetch JSON data
                const apiResponse = await fetch('/api/display-queues');
                if (apiResponse.ok) {
                    const data = await apiResponse.json();
                    this.queues = data.queues;
                    this.processQueues();
                    this.filteredQueues = [...this.queues];
                }
            } catch (error) {
                console.error('Error refreshing data:', error);
            }
        },

        // Utility methods
        formatTime(dateString) {
            if (!dateString) return '-';
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
        },

        getStatusClass(status) {
            const classMap = {
                'waiting': 'bg-blue-100 text-blue-800',
                'called': 'bg-yellow-100 text-yellow-800',
                'served': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return classMap[status] || 'bg-gray-100 text-gray-800';
        },

        showNotification(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white z-50 transform transition-transform duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-600' :
                type === 'error' ? 'bg-red-600' : 'bg-blue-600'
            }`;

            // Add icon based on type
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
            toast.innerHTML = `<div class="flex items-center space-x-2"><span>${icon}</span><span>${message}</span></div>`;

            document.body.appendChild(toast);

            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }
    }
}
</script>
@endsection

