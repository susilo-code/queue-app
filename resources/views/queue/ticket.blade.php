@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Success Message -->
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Nomor Antrian Berhasil Diambil!</h1>
        <p class="text-gray-600">Simpan tiket ini dan tunggu panggilan sesuai nomor antrian Anda</p>
    </div>

    <!-- Ticket -->
    <div id="ticket" class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 print:shadow-none">
        <!-- Header -->
        <div class="text-center py-6 px-6" style="background-color: {{ $queue->queueType->color }}">
            <h2 class="text-white text-2xl font-bold">TIKET ANTRIAN</h2>
            <p class="text-white opacity-90">{{ config('app.name', 'Sistem Antrian') }}</p>
        </div>

        <!-- Ticket Content -->
        <div class="p-8">
            <!-- Queue Number -->
            <div class="text-center mb-8">
                <div class="inline-block p-6 rounded-xl border-4 border-dashed" style="border-color: {{ $queue->queueType->color }}">
                    <div class="text-6xl font-bold mb-2" style="color: {{ $queue->queueType->color }}">
                        {{ $queue->queue_number }}
                    </div>
                    <div class="text-lg font-semibold text-gray-700">
                        {{ $queue->queueType->name }}
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            @if($queue->customer_name || $queue->customer_phone)
            <div class="border-t pt-6 mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-3">INFORMASI PELANGGAN</h3>
                @if($queue->customer_name)
                <p class="text-gray-900 mb-2">
                    <span class="font-medium">Nama:</span> {{ $queue->customer_name }}
                </p>
                @endif
                @if($queue->customer_phone)
                <p class="text-gray-900">
                    <span class="font-medium">Telepon:</span> {{ $queue->customer_phone }}
                </p>
                @endif
            </div>
            @endif

            <!-- Queue Info -->
            <div class="border-t pt-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal:</span>
                    <span class="font-medium">{{ $queue->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Waktu:</span>
                    <span class="font-medium">{{ $queue->created_at->format('H:i') }} WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status:</span>
                    <span class="font-medium capitalize">{{ $queue->status === 'waiting' ? 'Menunggu' : $queue->status }}</span>
                </div>
            </div>

            <!-- QR Code Placeholder (Optional) -->
            <div class="border-t pt-6 mt-6 text-center">
                <div class="w-24 h-24 bg-gray-200 mx-auto rounded-lg flex items-center justify-center mb-2">
                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zm8-2v8h8V3h-8zm6 6h-4V5h4v4zM3 21h8v-8H3v8zm2-6h4v4H5v-4z"/>
                        <path d="M15 15h2v2h-2zm0 4h2v2h-2zm4-4h2v2h-2zm0 4h2v2h-2z"/>
                    </svg>
                </div>
                <p class="text-xs text-gray-500">ID: {{ $queue->id }}</p>
            </div>

            <!-- Instructions -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
                <h4 class="font-medium text-yellow-800 mb-2">Petunjuk:</h4>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Harap tunggu panggilan sesuai nomor antrian</li>
                    <li>• Jika nomor terlewat, silahkan konfirmasi ke petugas</li>
                    <li>• Simpan tiket ini hingga selesai dilayani</li>
                    <li>• Lihat display antrian untuk status terkini</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 text-center text-sm text-gray-500">
            Terima kasih telah menggunakan layanan kami
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-1 gap-4 mb-8">
        <button onclick="window.print()"
                class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak Tiket
        </button>

        <button onclick="shareTicket()"
                class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
            </svg>
            Bagikan
        </button>

        <a href="{{ route('queue.display') }}"
           class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Lihat Display Antrian
        </a>

        <a href="{{ route('home') }}"
           class="w-full bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-gray-700 transition-colors flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Kembali ke Beranda
        </a>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #ticket, #ticket * {
        visibility: visible;
    }
    #ticket {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<script>
function shareTicket() {
    const ticketInfo = {
        title: 'Tiket Antrian',
        text: `Nomor Antrian: {{ $queue->queue_number }}\nLayanan: {{ $queue->queueType->name }}\nTanggal: {{ $queue->created_at->format('d/m/Y H:i') }} WIB`,
        url: window.location.href
    };

    if (navigator.share) {
        navigator.share(ticketInfo).catch(console.error);
    } else {
        // Fallback: Copy to clipboard
        const textToCopy = `${ticketInfo.text}\nLink: ${ticketInfo.url}`;
        navigator.clipboard.writeText(textToCopy).then(() => {
            alert('Informasi tiket telah disalin ke clipboard!');
        }).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = textToCopy;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Informasi tiket telah disalin ke clipboard!');
        });
    }
}

// Auto refresh page every 30 seconds to update status
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endsection

