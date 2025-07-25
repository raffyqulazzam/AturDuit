@extends('layouts.app')

@section('page-title', 'Tambah Kategori')

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Kategori</h2>
            <p class="text-gray-600 dark:text-gray-400">Buat kategori baru untuk transaksi Anda</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Kategori
                        </label>
                        <input type="text" name="name" id="name" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Masukkan nama kategori"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipe Kategori
                        </label>
                        <select name="type" id="type" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="">Pilih tipe kategori</option>
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Warna
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="color" id="color" 
                                   class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer" 
                                   value="{{ old('color', '#3B82F6') }}">
                            <input type="text" 
                                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="#3B82F6" 
                                   readonly
                                   x-data="{ value: '{{ old('color', '#3B82F6') }}' }"
                                   x-model="value"
                                   @input="document.getElementById('color').value = value">
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icon -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Icon
                        </label>
                        <div class="grid grid-cols-8 gap-2 mb-3" id="icon-grid">
                            <!-- Icons will be populated by JavaScript -->
                        </div>
                        <input type="hidden" name="icon" id="icon" value="{{ old('icon', 'tag') }}">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Pilih icon yang sesuai dengan kategori Anda
                        </p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('categories.index') }}" 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const iconGrid = document.getElementById('icon-grid');
    const iconInput = document.getElementById('icon');
    const colorInput = document.getElementById('color');
    
    // Available icons for categories
    const icons = [
        'tag', 'credit-card', 'shopping-cart', 'utensils', 'car', 'home', 'heart',
        'gamepad-2', 'book', 'graduation-cap', 'briefcase', 'plane', 'gift',
        'coffee', 'shirt', 'zap', 'wifi', 'smartphone', 'laptop', 'headphones',
        'camera', 'film', 'music', 'dumbbell', 'stethoscope', 'pill', 'baby',
        'dog', 'fuel', 'wrench', 'palette', 'scissors', 'hammer', 'shopping-bag',
        'wallet', 'coins', 'banknote', 'trending-up', 'trending-down', 'calculator',
        'chart-bar', 'piggy-bank', 'landmark', 'building-2', 'store', 'factory'
    ];
    
    function renderIconGrid() {
        iconGrid.innerHTML = '';
        icons.forEach(icon => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `w-10 h-10 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 flex items-center justify-center transition-colors ${iconInput.value === icon ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/50' : 'hover:bg-gray-50 dark:hover:bg-gray-700'}`;
            button.innerHTML = `<i data-lucide="${icon}" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>`;
            
            button.addEventListener('click', function() {
                iconInput.value = icon;
                renderIconGrid();
            });
            
            iconGrid.appendChild(button);
        });
        
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
    
    // Color picker sync
    colorInput.addEventListener('input', function() {
        const colorText = document.querySelector('input[readonly]');
        if (colorText) {
            colorText.value = this.value;
        }
    });
    
    // Initial render
    renderIconGrid();
});
</script>
@endsection
