class QueueTextToSpeech {
    constructor(options = {}) {
        this.isSupported = 'speechSynthesis' in window;
        this.isEnabled = true;
        this.volume = options.volume || 0.8;
        this.rate = options.rate || 0.8;
        this.pitch = options.pitch || 1.0;
        this.language = options.language || 'id-ID';
        this.voice = null;
        this.queue = [];
        this.isSpeaking = false;

        this.init();
    }

    init() {
        if (!this.isSupported) {
            console.warn('Text-to-Speech not supported in this browser');
            return;
        }

        // Load available voices
        this.loadVoices();

        // Handle voice changes (some browsers load voices asynchronously)
        if (speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = () => {
                this.loadVoices();
            };
        }

        // Add UI controls
        this.createControls();
    }

    loadVoices() {
        const voices = speechSynthesis.getVoices();

        // Prioritize Indonesian voices
        this.voice = voices.find(voice =>
            voice.lang.includes('id') ||
            voice.name.toLowerCase().includes('indonesia')
        );

        // Fallback to English if Indonesian not available
        if (!this.voice) {
            this.voice = voices.find(voice =>
                voice.lang.includes('en') &&
                voice.name.toLowerCase().includes('female')
            );
        }

        // Last resort - use first available voice
        if (!this.voice && voices.length > 0) {
            this.voice = voices[0];
        }

        console.log('Available voices:', voices.length);
        console.log('Selected voice:', this.voice?.name || 'None');
    }

    createControls() {
        // Create floating TTS control panel
        const controlPanel = document.createElement('div');
        controlPanel.id = 'tts-controls';
        controlPanel.className = 'fixed bottom-20 right-4 bg-white rounded-lg shadow-lg p-4 z-50 border';
        controlPanel.style.display = 'none';

        controlPanel.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-800">🔊 Pengaturan Suara</h3>
                <button id="tts-close" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-3">
                <!-- Enable/Disable -->
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">Status</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="tts-enabled" class="sr-only peer" ${this.isEnabled ? 'checked' : ''}>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Volume -->
                <div>
                    <label class="text-xs text-gray-600">Volume</label>
                    <input type="range" id="tts-volume" min="0" max="1" step="0.1" value="${this.volume}"
                           class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                    <div class="text-xs text-gray-500 text-center">${Math.round(this.volume * 100)}%</div>
                </div>

                <!-- Speed -->
                <div>
                    <label class="text-xs text-gray-600">Kecepatan</label>
                    <input type="range" id="tts-rate" min="0.1" max="2" step="0.1" value="${this.rate}"
                           class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                    <div class="text-xs text-gray-500 text-center">${this.rate}x</div>
                </div>

                <!-- Test Button -->
                <button id="tts-test" class="w-full bg-blue-600 text-white text-xs py-2 px-3 rounded hover:bg-blue-700 transition-colors">
                    🎤 Test Suara
                </button>
            </div>
        `;

        document.body.appendChild(controlPanel);

        // Create toggle button
        const toggleButton = document.createElement('button');
        toggleButton.id = 'tts-toggle';
        toggleButton.className = 'fixed bottom-4 right-4 bg-blue-600 text-white w-12 h-12 rounded-full shadow-lg hover:bg-blue-700 transition-colors z-40 flex items-center justify-center';
        toggleButton.innerHTML = '🔊';
        toggleButton.title = 'Pengaturan Text-to-Speech';

        document.body.appendChild(toggleButton);

        // Add event listeners
        this.attachEventListeners();
    }

    attachEventListeners() {
        const toggleButton = document.getElementById('tts-toggle');
        const controlPanel = document.getElementById('tts-controls');
        const closeButton = document.getElementById('tts-close');
        const enabledToggle = document.getElementById('tts-enabled');
        const volumeSlider = document.getElementById('tts-volume');
        const rateSlider = document.getElementById('tts-rate');
        const testButton = document.getElementById('tts-test');

        // Toggle control panel
        toggleButton.addEventListener('click', () => {
            const isVisible = controlPanel.style.display !== 'none';
            controlPanel.style.display = isVisible ? 'none' : 'block';
        });

        // Close panel
        closeButton.addEventListener('click', () => {
            controlPanel.style.display = 'none';
        });

        // Enable/disable TTS
        enabledToggle.addEventListener('change', (e) => {
            this.isEnabled = e.target.checked;
            toggleButton.innerHTML = this.isEnabled ? '🔊' : '🔇';
            toggleButton.title = this.isEnabled ? 'Text-to-Speech Enabled' : 'Text-to-Speech Disabled';
        });

        // Volume control
        volumeSlider.addEventListener('input', (e) => {
            this.volume = parseFloat(e.target.value);
            e.target.nextElementSibling.textContent = Math.round(this.volume * 100) + '%';
        });

        // Rate control
        rateSlider.addEventListener('input', (e) => {
            this.rate = parseFloat(e.target.value);
            e.target.nextElementSibling.textContent = this.rate + 'x';
        });

        // Test button
        testButton.addEventListener('click', () => {
            this.announceQueue('A001', 'Layanan Umum', 'Test Customer');
        });

        // Close panel when clicking outside
        document.addEventListener('click', (e) => {
            if (!controlPanel.contains(e.target) && !toggleButton.contains(e.target)) {
                controlPanel.style.display = 'none';
            }
        });
    }

    speak(text, options = {}) {
        return new Promise((resolve, reject) => {
            if (!this.isSupported || !this.isEnabled) {
                console.warn('TTS not available or disabled');
                resolve();
                return;
            }

            // Cancel any ongoing speech
            speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(text);

            // Apply settings
            utterance.volume = options.volume || this.volume;
            utterance.rate = options.rate || this.rate;
            utterance.pitch = options.pitch || this.pitch;
            utterance.lang = options.language || this.language;

            if (this.voice) {
                utterance.voice = this.voice;
            }

            // Event handlers
            utterance.onstart = () => {
                this.isSpeaking = true;
                console.log('TTS started:', text);
            };

            utterance.onend = () => {
                this.isSpeaking = false;
                console.log('TTS ended:', text);
                resolve();
            };

            utterance.onerror = (error) => {
                this.isSpeaking = false;
                console.error('TTS error:', error);
                reject(error);
            };

            // Speak
            speechSynthesis.speak(utterance);
        });
    }

    async announceQueue(queueNumber, serviceName, customerName = null) {
        if (!this.isEnabled) return;

        // Build announcement text
        let announcement = `Nomor antrian ${this.formatQueueNumber(queueNumber)}`;

        if (serviceName) {
            announcement += `, layanan ${serviceName}`;
        }

        if (customerName) {
            announcement += `, atas nama ${customerName}`;
        }

        announcement += ', silahkan menuju ke loket pelayanan.';

        console.log('Announcing:', announcement);

        try {
            // Play notification sound first (optional)
            await this.playNotificationSound();

            // Wait a moment
            await this.delay(800);

            // Speak the announcement
            await this.speak(announcement);

            // Log announcement
            this.logAnnouncement(queueNumber, serviceName, customerName);

        } catch (error) {
            console.error('Failed to announce queue:', error);
        }
    }

    formatQueueNumber(queueNumber) {
        // Format queue number for better pronunciation
        // Example: "A001" becomes "A nol nol satu"
        return queueNumber.replace(/([A-Z])(\d+)/, (match, letter, numbers) => {
            const digitWords = {
                '0': 'nol', '1': 'satu', '2': 'dua', '3': 'tiga', '4': 'empat',
                '5': 'lima', '6': 'enam', '7': 'tujuh', '8': 'delapan', '9': 'sembilan'
            };

            const spokenNumbers = numbers.split('').map(digit => digitWords[digit]).join(' ');
            return `${letter} ${spokenNumbers}`;
        });
    }

    async playNotificationSound() {
        // Simple notification beep using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);

            await this.delay(300);
        } catch (error) {
            console.warn('Could not play notification sound:', error);
        }
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    logAnnouncement(queueNumber, serviceName, customerName) {
        // Log to console or send to server
        const logData = {
            timestamp: new Date().toISOString(),
            queueNumber,
            serviceName,
            customerName,
            language: this.language,
            voice: this.voice?.name
        };

        console.log('Announcement logged:', logData);

        // Optional: Send to server for analytics
        // fetch('/api/log-announcement', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(logData)
        // });
    }

    // Queue management
    addToQueue(text, priority = 'normal') {
        this.queue.push({ text, priority, timestamp: Date.now() });
        this.processQueue();
    }

    async processQueue() {
        if (this.isSpeaking || this.queue.length === 0) return;

        // Sort by priority and timestamp
        this.queue.sort((a, b) => {
            if (a.priority === 'high' && b.priority !== 'high') return -1;
            if (a.priority !== 'high' && b.priority === 'high') return 1;
            return a.timestamp - b.timestamp;
        });

        const next = this.queue.shift();
        if (next) {
            await this.speak(next.text);
            setTimeout(() => this.processQueue(), 500); // Small delay between announcements
        }
    }

    // Utility methods
    stop() {
        speechSynthesis.cancel();
        this.isSpeaking = false;
        this.queue = [];
    }

    pause() {
        speechSynthesis.pause();
    }

    resume() {
        speechSynthesis.resume();
    }

    getVoices() {
        return speechSynthesis.getVoices();
    }

    setVoice(voiceName) {
        const voices = this.getVoices();
        this.voice = voices.find(voice => voice.name === voiceName);
    }

    // Test different announcement styles
    async testAnnouncements() {
        const testCases = [
            { queueNumber: 'A001', serviceName: 'Layanan Umum' },
            { queueNumber: 'B015', serviceName: 'Layanan Khusus', customerName: 'Budi Santoso' },
            { queueNumber: 'V003', serviceName: 'Layanan VIP', customerName: 'Dr. Sinta' }
        ];

        for (const testCase of testCases) {
            await this.announceQueue(testCase.queueNumber, testCase.serviceName, testCase.customerName);
            await this.delay(2000); // Wait 2 seconds between announcements
        }
    }
}

// Integration with Queue Management System
class QueueManager {
    constructor() {
        this.tts = new QueueTextToSpeech({
            volume: 0.8,
            rate: 0.8,
            language: 'id-ID'
        });

        this.currentQueue = null;
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Listen for queue updates via polling or WebSocket
        this.startQueuePolling();

        // Manual call buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="call-queue"]')) {
                const queueId = e.target.dataset.queueId;
                this.callQueue(queueId);
            }
        });
    }

    async callQueue(queueId) {
        try {
            // Call API to update queue status
            const response = await fetch(`/admin/queue-management/${queueId}/call`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const queueData = await response.json();

                // Announce the queue
                await this.tts.announceQueue(
                    queueData.queue_number,
                    queueData.queue_type.name,
                    queueData.customer_name
                );

                // Update UI
                this.updateQueueDisplay(queueData);

                // Show success notification
                this.showNotification(`Antrian ${queueData.queue_number} telah dipanggil`, 'success');

            } else {
                throw new Error('Failed to call queue');
            }
        } catch (error) {
            console.error('Error calling queue:', error);
            this.showNotification('Gagal memanggil antrian', 'error');
        }
    }

    startQueuePolling() {
        // Poll for queue updates every 5 seconds
        setInterval(async () => {
            try {
                const response = await fetch('/api/display-queues');
                const data = await response.json();

                // Check for newly called queues
                const calledQueue = data.queues.find(q =>
                    q.status === 'called' &&
                    q.id !== this.currentQueue?.id
                );

                if (calledQueue && calledQueue.called_at) {
                    const calledTime = new Date(calledQueue.called_at);
                    const now = new Date();

                    // If queue was called within last 10 seconds, announce it
                    if (now - calledTime < 10000) {
                        await this.tts.announceQueue(
                            calledQueue.queue_number,
                            calledQueue.queue_type.name,
                            calledQueue.customer_name
                        );

                        this.currentQueue = calledQueue;
                    }
                }
            } catch (error) {
                console.error('Error polling queues:', error);
            }
        }, 5000);
    }

    updateQueueDisplay(queueData) {
        // Update the display board
        const displayElement = document.getElementById('current-queue-display');
        if (displayElement) {
            displayElement.innerHTML = `
                <div class="text-center">
                    <div class="text-6xl font-bold text-green-600 mb-2">${queueData.queue_number}</div>
                    <div class="text-xl font-medium">${queueData.queue_type.name}</div>
                    ${queueData.customer_name ? `<div class="text-lg text-gray-600">${queueData.customer_name}</div>` : ''}
                </div>
            `;
        }
    }

    showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white z-50 transform transition-transform duration-300 translate-x-full ${
            type === 'success' ? 'bg-green-600' :
            type === 'error' ? 'bg-red-600' : 'bg-blue-600'
        }`;
        toast.textContent = message;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);

        // Remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on admin/management pages
    if (window.location.pathname.includes('/admin/queue-management') ||
        window.location.pathname.includes('/display')) {
        window.queueManager = new QueueManager();
        console.log('✅ Queue TTS System initialized');
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { QueueTextToSpeech, QueueManager };
} else {
    window.QueueTextToSpeech = QueueTextToSpeech;
    window.QueueManager = QueueManager;
}

// Keyboard shortcuts (optional)
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.shiftKey) {
        switch (e.code) {
            case 'KeyS': // Ctrl+Shift+S - Toggle TTS
                e.preventDefault();
                const toggleButton = document.getElementById('tts-toggle');
                if (toggleButton) toggleButton.click();
                break;

            case 'KeyT': // Ctrl+Shift+T - Test TTS
                e.preventDefault();
                if (window.queueManager) {
                    window.queueManager.tts.testAnnouncements();
                }
                break;
        }
    }
});
