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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative">
                            <input type="radio" name="type" value="savings" 
                                   {{ ($account->type == 'savings' || old('type') == 'savings') ? 'checked' : '' }} 
                                   class="sr-only peer" required>
                            <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                <div class="text-center">
                                    <i data-lucide="piggy-bank" class="w-8 h-8 text-blue-600 mx-auto mb-2"></i>
                                    <span class="text-sm font-medium text-gray-900">Tabungan</span>
                                </div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="checking" 
                                   {{ ($account->type == 'checking' || old('type') == 'checking') ? 'checked' : '' }} 
                                   class="sr-only peer">
                            <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50">
                                <div class="text-center">
                                    <i data-lucide="credit-card" class="w-8 h-8 text-green-600 mx-auto mb-2"></i>
                                    <span class="text-sm font-medium text-gray-900">Giro</span>
                                </div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="cash" 
                                   {{ ($account->type == 'cash' || old('type') == 'cash') ? 'checked' : '' }} 
                                   class="sr-only peer">
                            <div class="flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50">
                                <div class="text-center">
                                    <i data-lucide="wallet" class="w-8 h-8 text-purple-600 mx-auto mb-2"></i>
                                    <span class="text-sm font-medium text-gray-900">Tunai</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Initial Balance -->
                <div>
                    <label for="balance" class="block text-sm font-medium text-gray-700 mb-2">
                        Saldo Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="balance" name="balance" min="0" step="100" required
                               value="{{ old('balance', $account->balance) }}"
                               placeholder="0"
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Masukkan saldo akun saat ini</p>
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
@endsection
