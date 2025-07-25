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

@endsection
