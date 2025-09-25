
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Queue Type Info -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="h-2" style="background-color: {{ $queueType->color }}"></div>
        <div class="p-8 text-center">
            <div class="w-24 h-24 mx-auto mb-4 rounded-full flex items-center justify-center text-white text-4xl font-bold"
                 style="background-color: {{ $queueType->color }}">
                {{ $queueType->code }}
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $queueType->name }}</h1>
            <p class="text-gray-600 text-lg">{{ $queueType->description }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Ambil Nomor Antrian</h2>

        <form action="{{ route('queue.store') }}" method="POST" x-data="queueForm()">
            @csrf
            <input type="hidden" name="queue_type_id" value="{{ $queueType->id }}">

            <!-- Customer Name -->
            <div class="mb-6">
                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama (Opsional)
                </label>
                <input type="text"
                       name="customer_name"
                       id="customer_name"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                       placeholder="Masukkan nama Anda"
                       value="{{ old('customer_name') }}">
                @error('customer_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer Phone -->
            <div class="mb-6">
                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Telepon (Opsional)
                </label>
                <input type="tel"
                       name="customer_phone"
                       id="customer_phone"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                       placeholder="Contoh: 081234567890"
                       value="{{ old('customer_phone') }}">
                @error('customer_phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Informasi:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Nomor antrian akan otomatis dibuat setelah Anda submit</li>
                            <li>Nama dan telepon bersifat opsional</li>
                            <li>Silahkan tunggu panggilan sesuai nomor antrian Anda</li>
                            <li>Tiket antrian dapat dicetak atau di-screenshot</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    x-bind:disabled="loading"
                    x-bind:class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:from-blue-600 hover:to-blue-700 hover:scale-105'"
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 px-6 rounded-lg font-semibold text-lg transform transition-all duration-200 shadow-md">
                <span x-show="!loading" class="flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ambil Nomor Antrian
                </span>
                <span x-show="loading" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </form>
    </div>

    <!-- Current Queue Stats -->
    <div class="bg-white rounded-xl shadow-lg p-6 mt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Status Antrian Saat Ini</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $queueType->todayQueues()->count() }}</div>
                <div class="text-sm text-gray-600">Total Hari Ini</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $queueType->waitingQueues()->count() }}</div>
                <div class="text-sm text-blue-600">Menunggu</div>
            </div>
        </div>
    </div>
</div>

<script>
function queueForm() {
    return {
        loading: false,

        submitForm(event) {
            this.loading = true;
            // Form will submit normally, loading will be reset on page reload/redirect
        }
    }
}
</script>
@endsection
