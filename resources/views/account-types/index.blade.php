@extends('layouts.app')

@section('title', 'Jenis Akun')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Jenis Akun</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola jenis akun Anda</p>
            </div>
            <a href="{{ route('account-types.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                Tambah Jenis Akun
            </a>
        </div>
    </div>

    <!-- Account Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($accountTypes as $accountType)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 border-l-4" style="border-left-color: {{ $accountType->color }}">
                <div class="flex justify-between items-start">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" style="background-color: {{ $accountType->color }}">
                                <i data-lucide="{{ $accountType->icon }}" class="h-5 w-5"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $accountType->name }}</h3>
                            @if($accountType->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $accountType->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($accountType->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200 rounded-full">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200 rounded-full">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Account Count -->
                <div class="mt-4">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i data-lucide="wallet" class="mr-1 h-4 w-4"></i>
                        <span>{{ $accountType->user_accounts_count ?? 0 }} akun menggunakan jenis ini</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex space-x-2">
                        <a href="{{ route('account-types.edit', $accountType) }}" class="inline-flex items-center px-3 py-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 text-sm font-medium rounded-md transition-colors duration-150">
                            <i data-lucide="edit" class="mr-1 h-3 w-3"></i>
                            Edit
                        </a>
                        <a href="{{ route('account-types.show', $accountType) }}" class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors duration-150">
                            <i data-lucide="eye" class="mr-1 h-3 w-3"></i>
                            Detail
                        </a>
                    </div>
                    @if($accountType->user_accounts_count == 0)
                        <form action="{{ route('account-types.destroy', $accountType) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-md transition-colors duration-150" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis akun ini?')">
                                <i data-lucide="trash-2" class="mr-1 h-3 w-3"></i>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <i data-lucide="layers" class="h-24 w-24"></i>
                    </div>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Belum ada jenis akun</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Mulai dengan membuat jenis akun pertama Anda.</p>
                    <div class="mt-6">
                        <a href="{{ route('account-types.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                            Tambah Jenis Akun
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($accountTypes->hasPages())
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 rounded-lg">
            {{ $accountTypes->links() }}
        </div>
    @endif
</div>
@endsection
