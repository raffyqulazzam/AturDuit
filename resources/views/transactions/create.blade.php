@extends('layouts.app')

@section('title', 'Tambah Transaksi - AturDuit')
@section('page-title', 'Tambah Transaksi')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('transactions.index') }}" class="hover:text-gray-700">Transaksi</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>Tambah Transaksi</span>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Tambah Transaksi Baru</h2>
            <p class="text-sm text-gray-600">Catat transaksi pemasukan atau pengeluaran</p>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200">
            @csrf
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Transaksi</h3>
            </div>
            
            <div class="px-6 py-6 space-y-6">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Transaksi <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative">
                            <input type="radio" name="type" value="income" {{ old('type') == 'income' ? 'checked' : '' }} 
                                   class="sr-only peer" required>
                            <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50">
                                <div class="text-center">
                                    <i data-lucide="trending-up" class="w-8 h-8 text-green-600 mx-auto mb-2"></i>
                                    <span class="text-sm font-medium text-gray-900">Pemasukan</span>
                                </div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="expense" {{ old('type') == 'expense' ? 'checked' : '' }} 
                                   class="sr-only peer">
                            <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50">
                                <div class="text-center">
                                    <i data-lucide="trending-down" class="w-8 h-8 text-red-600 mx-auto mb-2"></i>
                                    <span class="text-sm font-medium text-gray-900">Pengeluaran</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="description" name="description" required
                           value="{{ old('description') }}"
                           placeholder="Contoh: Gaji bulanan, Belanja groceries, Bayar listrik"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="amount" name="amount" min="1" step="100" required
                               value="{{ old('amount') }}"
                               placeholder="0"
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="transaction_date" name="transaction_date" required
                           value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category and Account -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" name="category_id" required 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        data-type="{{ $category->type }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <a href="{{ route('categories.create') }}" class="text-blue-600 hover:text-blue-800">
                                + Buat kategori baru
                            </a>
                        </p>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Akun <span class="text-red-500">*</span>
                        </label>
                        <select id="account_id" name="account_id" required 
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Akun</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ ucfirst($account->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              placeholder="Catatan tambahan untuk transaksi ini..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('transactions.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const categorySelect = document.getElementById('category_id');
    const allOptions = Array.from(categorySelect.options);
    
    function filterCategories() {
        const selectedType = document.querySelector('input[name="type"]:checked')?.value;
        
        // Clear current options except the first one (Pilih Kategori)
        categorySelect.innerHTML = '';
        categorySelect.appendChild(allOptions[0].cloneNode(true));
        
        if (selectedType) {
            // Add options that match the selected type
            allOptions.slice(1).forEach(option => {
                if (option.dataset.type === selectedType) {
                    categorySelect.appendChild(option.cloneNode(true));
                }
            });
        } else {
            // Add all options if no type selected
            allOptions.slice(1).forEach(option => {
                categorySelect.appendChild(option.cloneNode(true));
            });
        }
    }
    
    typeRadios.forEach(radio => {
        radio.addEventListener('change', filterCategories);
    });
    
    // Filter on page load if type is already selected
    filterCategories();
});
</script>
@endsection
