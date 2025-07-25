@extends('layouts.app')

@section('title', 'Edit Akun - AturDuit')
@section('page-title', 'Edit Akun')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('accounts.index') }}" class="hover:text-gray-700">Akun</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>Edit Akun</span>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Edit Akun</h2>
            <p class="text-sm text-gray-600">Ubah informasi akun {{ $account->name }}</p>
        </div>

        <form action="{{ route('accounts.update', $account) }}" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Akun</h3>
            </div>
            
            <div class="px-6 py-6 space-y-6">
                <!-- Account Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name', $account->name) }}"
                           placeholder="Contoh: BCA Tabungan Utama, Dana Cash"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Akun <span class="text-red-500">*</span>
                    </label>
                    @if($accountTypes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($accountTypes as $accountType)
                                <label class="relative">
                                    <input type="radio" name="account_type_id" value="{{ $accountType->id }}" 
                                           {{ ($account->account_type_id == $accountType->id || old('account_type_id') == $accountType->id) ? 'checked' : '' }} 
                                           class="sr-only peer" required>
                                    <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300 transition-colors">
                                        <div class="text-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white mx-auto mb-2" style="background-color: {{ $accountType->color }}">
                                                <i data-lucide="{{ $accountType->icon }}" class="w-5 h-5"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $accountType->name }}</span>
                                            @if($accountType->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($accountType->description, 30) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
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

                <!-- Current Balance -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700 mb-2">
                        Saldo Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-600 text-sm font-medium">Rp</span>
                        </div>
                        <input type="text" id="balance_display" 
                               placeholder="0"
                               class="block w-full pl-12 pr-4 py-3 text-lg border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-1">
                        <input type="hidden" id="balance" name="balance" required value="{{ old('balance', $account->balance) }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Format: Rp 1.000.000 (gunakan titik sebagai pemisah ribuan). Saldo tidak boleh minus.
                    </p>
                    @error('balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Deskripsi tambahan untuk akun ini..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $account->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('accounts.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Update Akun
                </button>
            </div>
        </form>
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
