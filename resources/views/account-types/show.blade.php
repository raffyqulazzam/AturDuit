@extends('layouts.app')

@section('title', 'Detail Jenis Akun')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white mr-4" style="background-color: {{ $accountType->color }}">
                    <i data-lucide="{{ $accountType->icon }}" class="h-6 w-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $accountType->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Detail jenis akun</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('account-types.edit', $accountType) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i data-lucide="edit" class="mr-2 h-4 w-4"></i>
                    Edit
                </a>
                <a href="{{ route('account-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Account Type Info -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Jenis Akun</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $accountType->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <div class="mt-1">
                                @if($accountType->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200 rounded-full">
                                        <i data-lucide="check-circle" class="mr-1 h-3 w-3"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200 rounded-full">
                                        <i data-lucide="x-circle" class="mr-1 h-3 w-3"></i>
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon</label>
                            <div class="mt-1 flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white mr-2" style="background-color: {{ $accountType->color }}">
                                    <i data-lucide="{{ $accountType->icon }}" class="h-4 w-4"></i>
                                </div>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $accountType->icon }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Warna</label>
                            <div class="mt-1 flex items-center">
                                <div class="w-6 h-6 rounded-full mr-2" style="background-color: {{ $accountType->color }}"></div>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $accountType->color }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dibuat</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $accountType->created_at->format('d M Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Diperbarui</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $accountType->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>

                    @if($accountType->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $accountType->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics & Actions -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Statistik</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i data-lucide="wallet" class="h-5 w-5 text-gray-400 mr-2"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Akun</span>
                        </div>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $accountType->user_accounts_count ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Aksi Cepat</h2>
                <div class="space-y-3">
                    <a href="{{ route('account-types.edit', $accountType) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i data-lucide="edit" class="mr-2 h-4 w-4"></i>
                        Edit Jenis Akun
                    </a>
                    
                    @if($accountType->user_accounts_count == 0)
                        <form action="{{ route('account-types.destroy', $accountType) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis akun ini?')">
                                <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>
                                Hapus Jenis Akun
                            </button>
                        </form>
                    @else
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                            <div class="flex items-start">
                                <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-500 mr-2 mt-0.5"></i>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Tidak Dapat Dihapus</h4>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                        Jenis akun ini sedang digunakan oleh {{ $accountType->user_accounts_count }} akun dan tidak dapat dihapus.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related Accounts -->
    @if(($accountType->user_accounts_count ?? 0) > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Akun Menggunakan Jenis Ini</h2>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i data-lucide="wallet" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $accountType->user_accounts_count ?? 0 }} Akun Terdaftar</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Akun-akun yang menggunakan jenis akun "{{ $accountType->name }}"</p>
                    <div class="mt-4">
                        <a href="{{ route('accounts.index') }}?type={{ $accountType->name }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i data-lucide="eye" class="mr-2 h-4 w-4"></i>
                            Lihat Semua Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
