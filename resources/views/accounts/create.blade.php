@extends('layouts.app')

@section('title', 'Tambah Akun - AturDuit')
@section('page-title', 'Tambah Akun')

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('accounts.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Akun</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: BCA Utama, Dompet Tunai, GoPay" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="account_type_id" class="block text-sm font-medium text-gray-700 mb-2">Jenis Akun</label>
                    @if($accountTypes->count() > 0)
                        <select name="account_type_id" id="account_type_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih jenis akun</option>
                            @foreach($accountTypes as $accountType)
                                <option value="{{ $accountType->id }}" 
                                        {{ old('account_type_id') == $accountType->id ? 'selected' : '' }}
                                        data-icon="{{ $accountType->icon }}" 
                                        data-color="{{ $accountType->color }}">
                                    {{ $accountType->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div class="flex">
                                <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-400 mr-2"></i>
                                <div>
                                    <h3 class="text-sm font-medium text-yellow-800">Belum ada jenis akun</h3>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Anda perlu membuat jenis akun terlebih dahulu.
                                        <a href="{{ route('account-types.create') }}" class="font-medium underline hover:text-yellow-600">
                                            Buat jenis akun baru
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    @error('account_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700 mb-2">
                        Saldo Awal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-600 text-sm font-medium">Rp</span>
                        </div>
                        <input type="text" id="balance_display" 
                               placeholder="0"
                               class="block w-full pl-12 pr-4 py-3 text-lg border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-1">
                        <input type="hidden" id="balance" name="balance" required value="{{ old('balance', 0) }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Format: Rp 1.000.000 (gunakan titik sebagai pemisah ribuan)
                    </p>
                    @error('balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Catatan tambahan tentang akun ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('accounts.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Currency formatting for balance
    const balanceDisplay = document.getElementById('balance_display');
    const balanceHidden = document.getElementById('balance');
    
    // Handle balance input with better cursor management
    balanceDisplay.addEventListener('input', function(e) {
        let cursorPosition = e.target.selectionStart;
        const oldValue = e.target.value;
        
        // Get numeric value only (remove all dots)
        const numericOnly = oldValue.replace(/\D/g, '');
        
        // Format the numeric value
        const formatted = numericOnly ? numericOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
        
        // Only update if the formatted value is different
        if (formatted !== oldValue) {
            e.target.value = formatted;
            
            // Calculate new cursor position
            // Count digits before cursor position
            const digitsBeforeCursor = (oldValue.substring(0, cursorPosition).replace(/\D/g, '')).length;
            
            // Find the position in formatted string that corresponds to the same number of digits
            let newCursorPosition = 0;
            let digitCount = 0;
            
            for (let i = 0; i < formatted.length && digitCount < digitsBeforeCursor; i++) {
                if (/\d/.test(formatted[i])) {
                    digitCount++;
                }
                newCursorPosition = i + 1;
            }
            
            e.target.setSelectionRange(newCursorPosition, newCursorPosition);
        }
        
        // Update hidden field with numeric value
        balanceHidden.value = numericOnly;
    });
    
    // Handle paste event
    balanceDisplay.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = (e.clipboardData || window.clipboardData).getData('text');
        const numericOnly = pastedData.replace(/\D/g, '');
        const formatted = numericOnly ? numericOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
        e.target.value = formatted;
        balanceHidden.value = numericOnly;
    });
    
    // Initialize with old value if exists (after validation error)
    if (balanceHidden.value) {
        const rawBalance = balanceHidden.value.toString().trim();
        // Only format if the value is purely numeric (no dots)
        if (/^\d+$/.test(rawBalance)) {
            balanceDisplay.value = rawBalance.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        } else {
            // If value already has formatting, display as-is
            balanceDisplay.value = rawBalance;
        }
    }
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const balance = balanceHidden.value;
        if (balance && parseInt(balance) < 0) {
            e.preventDefault();
            alert('Saldo tidak boleh minus');
            balanceDisplay.focus();
            return false;
        }
    });
});
</script>
@endsection
