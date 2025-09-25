

{{-- resources/views/admin/queue-types/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola Jenis Antrian</h1>
            <p class="text-gray-600 mt-1">Atur dan kustomisasi jenis layanan antrian</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.dashboard') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                ← Dashboard
            </a>
            <a href="{{ route('admin.queue-types.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                + Tambah Jenis Antrian
            </a>
        </div>
    </div>

    <!-- Queue Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($queueTypes as $queueType)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <!-- Header with color -->
                <div class="h-3" style="background-color: {{ $queueType->color }}"></div>

                <div class="p-6">
                    <!-- Queue Type Info -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg font-bold"
                                 style="background-color: {{ $queueType->color }}">
                                {{ $queueType->code }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $queueType->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $queueType->description }}</p>
                            </div>
                        </div>

                        @if($queueType->is_active)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
                                Aktif
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <!-- Statistics -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ $queueType->today_queues_count }}</div>
                                <div class="text-xs text-gray-600">Antrian Hari Ini</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold" style="color: {{ $queueType->color }}">
                                    {{ $queueType->queues()->where('status', 'waiting')->whereDate('created_at', today())->count() }}
                                </div>
                                <div class="text-xs text-gray-600">Menunggu</div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.queue-types.edit', $queueType) }}"
                           class="flex-1 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg text-sm text-center font-medium hover:bg-blue-100 transition-colors">
                            ✏️ Edit
                        </a>

                        <form action="{{ route('admin.queue-types.destroy', $queueType) }}"
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus jenis antrian ini? Data antrian terkait akan ikut terhapus.')"
                              class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-red-50 text-red-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                🗑️ Hapus
                            </button>
                        </form>
                    </div>

                    <!-- Toggle Status -->
                    <form action="{{ route('admin.queue-types.update', $queueType) }}"
                          method="POST"
                          class="mt-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="name" value="{{ $queueType->name }}">
                        <input type="hidden" name="code" value="{{ $queueType->code }}">
                        <input type="hidden" name="description" value="{{ $queueType->description }}">
                        <input type="hidden" name="color" value="{{ $queueType->color }}">
                        <input type="hidden" name="is_active" value="{{ $queueType->is_active ? '0' : '1' }}">

                        <button type="submit"
                                class="w-full bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                            {{ $queueType->is_active ? '🔴 Nonaktifkan' : '🟢 Aktifkan' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="text-gray-400 text-6xl mb-4">📋</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Jenis Antrian</h3>
                    <p class="text-gray-600 mb-6">Tambahkan jenis antrian pertama untuk memulai sistem antrian</p>
                    <a href="{{ route('admin.queue-types.create') }}"
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                        + Tambah Jenis Antrian
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

{{-- resources/views/admin/queue-types/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('admin.queue-types.index') }}"
               class="text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Tambah Jenis Antrian</h1>
        </div>
        <p class="text-gray-600">Buat jenis antrian baru untuk sistem layanan Anda</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.queue-types.store') }}" method="POST" x-data="queueTypeForm()">
            @csrf

            <!-- Preview -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Preview:</h3>
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-xl font-bold"
                         :style="'background-color: ' + color">
                        <span x-text="code || 'A'">A</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900" x-text="name || 'Nama Layanan'">Nama Layanan</h4>
                        <p class="text-gray-600" x-text="description || 'Deskripsi layanan'">Deskripsi layanan</p>
                    </div>
                </div>
            </div>

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Layanan *
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       x-model="name"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                       placeholder="Contoh: Layanan Umum"
                       value="{{ old('name') }}"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Antrian *
                </label>
                <input type="text"
                       name="code"
                       id="code"
                       x-model="code"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                       placeholder="Contoh: A"
                       maxlength="10"
                       value="{{ old('code') }}"
                       required>
                <p class="text-sm text-gray-500 mt-1">Kode singkat untuk nomor antrian (max 10 karakter)</p>
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="description"
                          id="description"
                          x-model="description"
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                          placeholder="Deskripsi singkat tentang layanan ini">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color -->
            <div class="mb-6">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Warna Theme *
                </label>
                <div class="flex items-center space-x-4">
                    <input type="color"
                           name="color"
                           id="color"
                           x-model="color"
                           class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer"
                           value="{{ old('color', '#3b82f6') }}">

                    <div class="flex space-x-2">
                        <template x-for="presetColor in presetColors" :key="presetColor">
                            <button type="button"
                                    @click="color = presetColor"
                                    class="w-8 h-8 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform"
                                    :style="'background-color: ' + presetColor"
                                    :class="color === presetColor ? 'ring-2 ring-gray-400' : ''">
                            </button>
                        </template>
                    </div>
                </div>
                @error('color')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-8">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Aktifkan jenis antrian ini</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    💾 Simpan Jenis Antrian
                </button>
                <a href="{{ route('admin.queue-types.index') }}"
                   class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-semibold text-center hover:bg-gray-400 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function queueTypeForm() {
    return {
        name: '{{ old("name") }}',
        code: '{{ old("code") }}',
        description: '{{ old("description") }}',
        color: '{{ old("color", "#3b82f6") }}',
        presetColors: [
            '#3b82f6', // Blue
            '#10b981', // Green
            '#f59e0b', // Yellow
            '#ef4444', // Red
            '#8b5cf6', // Purple
            '#06b6d4', // Cyan
            '#f97316', // Orange
            '#84cc16', // Lime
            '#ec4899', // Pink
            '#6b7280'  // Gray
        ]
    }
}
</script>
@endsection

{{-- resources/views/admin/queue-types/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('admin.queue-types.index') }}"
               class="text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Jenis Antrian</h1>
        </div>
        <p class="text-gray-600">Ubah pengaturan jenis antrian: {{ $queueType->name }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.queue-types.update', $queueType) }}" method="POST" x-data="queueTypeForm()">
            @csrf
            @method('PATCH')

            <!-- Preview -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Preview:</h3>
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-xl font-bold"
                         :style="'background-color: ' + color">
                        <span x-text="code">{{ $queueType->code }}</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900" x-text="name">{{ $queueType->name }}</h4>
                        <p class="text-gray-600" x-text="description">{{ $queueType->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Warning if has active queues -->
            @if($queueType->queues()->whereDate('created_at', today())->count() > 0)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800">Perhatian!</h3>
                        <div class="text-sm text-yellow-700 mt-1">
                            Jenis antrian ini memiliki {{ $queueType->queues()->whereDate('created_at', today())->count() }} antrian aktif hari ini.
                            Perubahan kode akan mempengaruhi tampilan antrian yang sudah ada.
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Layanan *
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       x-model="name"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                       placeholder="Contoh: Layanan Umum"
                       value="{{ old('name', $queueType->name) }}"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Antrian *
                </label>
                <input type="text"
                       name="code"
                       id="code"
                       x-model="code"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                       placeholder="Contoh: A"
                       maxlength="10"
                       value="{{ old('code', $queueType->code) }}"
                       required>
                <p class="text-sm text-gray-500 mt-1">Kode singkat untuk nomor antrian (max 10 karakter)</p>
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="description"
                          id="description"
                          x-model="description"
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                          placeholder="Deskripsi singkat tentang layanan ini">{{ old('description', $queueType->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Color -->
            <div class="mb-6">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                    Warna Theme *
                </label>
                <div class="flex items-center space-x-4">
                    <input type="color"
                           name="color"
                           id="color"
                           x-model="color"
                           class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer"
                           value="{{ old('color', $queueType->color) }}">

                    <div class="flex space-x-2">
                        <template x-for="presetColor in presetColors" :key="presetColor">
                            <button type="button"
                                    @click="color = presetColor"
                                    class="w-8 h-8 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform"
                                    :style="'background-color: ' + presetColor"
                                    :class="color === presetColor ? 'ring-2 ring-gray-400' : ''">
                            </button>
                        </template>
                    </div>
                </div>
                @error('color')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-8">
                <label class="flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                           {{ old('is_active', $queueType->is_active) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Aktifkan jenis antrian ini</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    💾 Update Jenis Antrian
                </button>
                <a href="{{ route('admin.queue-types.index') }}"
                   class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-semibold text-center hover:bg-gray-400 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function queueTypeForm() {
    return {
        name: '{{ old("name", $queueType->name) }}',
        code: '{{ old("code", $queueType->code) }}',
        description: '{{ old("description", $queueType->description) }}',
        color: '{{ old("color", $queueType->color) }}',
        presetColors: [
            '#3b82f6', // Blue
            '#10b981', // Green
            '#f59e0b', // Yellow
            '#ef4444', // Red
            '#8b5cf6', // Purple
            '#06b6d4', // Cyan
            '#f97316', // Orange
            '#84cc16', // Lime
            '#ec4899', // Pink
            '#6b7280'  // Gray
        ]
    }
}
</script>
@endsection

Views untuk Queue Types Management | Claude | Claude
