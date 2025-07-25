@extends('layouts.app')

@section('title', 'Tambah Jenis Akun')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Jenis Akun</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Buat jenis akun baru untuk mengorganisir akun Anda</p>
            </div>
            <a href="{{ route('account-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('account-types.store') }}" method="POST" x-data="{ 
            selectedIcon: 'wallet', 
            selectedColor: '#3B82F6',
            icons: [
                'wallet', 'credit-card', 'banknote', 'piggy-bank', 'landmark', 'building-2', 
                'coins', 'dollar-sign', 'euro', 'pound-sterling', 'yen', 'bitcoin',
                'smartphone', 'laptop', 'car', 'home', 'briefcase', 'graduation-cap',
                'heart', 'shield', 'gift', 'shopping-cart', 'coffee', 'utensils',
                'plane', 'train', 'bus', 'bike', 'fuel', 'wrench'
            ],
            colors: [
                '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899',
                '#06B6D4', '#84CC16', '#F97316', '#6366F1', '#14B8A6', '#F43F5E'
            ]
        }">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Jenis Akun</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Contoh: Bank, E-Wallet, Kas" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi (Opsional)</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                  placeholder="Deskripsi singkat tentang jenis akun ini">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview</label>
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" :style="{ backgroundColor: selectedColor }">
                                    <i :data-lucide="selectedIcon" class="h-5 w-5"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="document.getElementById('name').value || 'Nama Jenis Akun'"></h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400" x-text="document.getElementById('description').value || 'Deskripsi jenis akun'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Icon Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Icon</label>
                        <div class="grid grid-cols-6 gap-2 max-h-48 overflow-y-auto p-2 border rounded-lg dark:border-gray-600">
                            <template x-for="icon in icons" :key="icon">
                                <button type="button" 
                                        @click="selectedIcon = icon"
                                        :class="selectedIcon === icon ? 'bg-blue-100 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'"
                                        class="p-3 border rounded-lg text-center transition-colors duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                                    <i :data-lucide="icon" class="h-5 w-5 mx-auto"></i>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="icon" :value="selectedIcon">
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Warna</label>
                        <div class="grid grid-cols-6 gap-2">
                            <template x-for="color in colors" :key="color">
                                <button type="button" 
                                        @click="selectedColor = color"
                                        :style="{ backgroundColor: color }"
                                        :class="selectedColor === color ? 'ring-2 ring-offset-2 ring-gray-400' : ''"
                                        class="w-10 h-10 rounded-lg transition-all duration-150">
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="color" :value="selectedColor">
                        @error('color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('account-types.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                    Simpan Jenis Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
