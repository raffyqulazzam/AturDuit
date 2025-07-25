@extends('layouts.app')

@section('title', 'Jenis Akun')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 shadow-sm rounded-xl p-8 border border-blue-100 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Jenis Akun</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2 text-base">Kelola dan atur jenis akun untuk mengorganisir keuangan Anda</p>
            </div>
            <a href="{{ route('account-types.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                <i data-lucide="plus" class="mr-2 h-5 w-5"></i>
                Tambah Jenis Akun
            </a>
        </div>
    </div>

    <!-- Account Types Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($accountTypes as $accountType)
            <div class="group bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl rounded-xl p-6 border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-600 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden">
                <!-- Color accent -->
                <div class="absolute top-0 left-0 w-full h-1 rounded-t-xl" style="background-color: {{ $accountType->color }}"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center flex-1">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-200" style="background: linear-gradient(135deg, {{ $accountType->color }}, {{ $accountType->color }}dd)">
                                <i data-lucide="{{ $accountType->icon }}" class="h-6 w-6"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $accountType->name }}</h3>
                            @if($accountType->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $accountType->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center ml-2">
                        @if($accountType->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full ring-1 ring-green-600/20">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full ring-1 ring-red-600/20">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Accounts List -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Akun yang Menggunakan</h4>
                        <span class="text-xs font-medium px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                            {{ $accountType->user_accounts_count ?? 0 }}
                        </span>
                    </div>
                    
                    @if($accountType->userAccounts && $accountType->userAccounts->count() > 0)
                        <div class="space-y-2">
                            @foreach($accountType->userAccounts as $account)
                                <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-600 rounded-lg border border-gray-200 dark:border-gray-500">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div class="flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-gray-500 rounded mr-2">
                                            <i data-lucide="credit-card" class="h-3 w-3 text-gray-600 dark:text-gray-300"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $account->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ format_idr($account->balance) }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($account->is_active)
                                        <span class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></span>
                                    @else
                                        <span class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full"></span>
                                    @endif
                                </div>
                            @endforeach
                            
                            @if($accountType->user_accounts_count > 3)
                                <div class="text-center">
                                    <a href="{{ route('account-types.show', $accountType) }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        Lihat {{ $accountType->user_accounts_count - 3 }} akun lainnya
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-gray-400 dark:text-gray-500 mb-1">
                                <i data-lucide="wallet" class="h-8 w-8 mx-auto"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada akun</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">yang menggunakan jenis ini</p>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-2">
                    <div class="flex gap-2">
                        <a href="{{ route('account-types.edit', $accountType) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 hover:text-indigo-800 text-sm font-medium rounded-lg border border-indigo-200 hover:border-indigo-300 transition-all duration-150">
                            <i data-lucide="edit" class="mr-1.5 h-4 w-4"></i>
                            Edit
                        </a>
                        <a href="{{ route('account-types.show', $accountType) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 hover:text-gray-800 text-sm font-medium rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-150">
                            <i data-lucide="eye" class="mr-1.5 h-4 w-4"></i>
                            Detail
                        </a>
                    </div>
                    @if($accountType->user_accounts_count == 0)
                        <form action="{{ route('account-types.destroy', $accountType) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 hover:text-red-800 text-sm font-medium rounded-lg border border-red-200 hover:border-red-300 transition-all duration-150" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis akun ini?')">
                                <i data-lucide="trash-2" class="mr-1.5 h-4 w-4"></i>
                                Hapus Jenis Akun
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-16 px-6">
                    <div class="mx-auto w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="layers" class="h-16 w-16 text-blue-500 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Belum ada jenis akun</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">Mulai dengan membuat jenis akun pertama Anda untuk mengorganisir keuangan dengan lebih baik.</p>
                    <a href="{{ route('account-types.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i data-lucide="plus" class="mr-2 h-5 w-5"></i>
                        Buat Jenis Akun Pertama
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($accountTypes->hasPages())
        <div class="bg-white dark:bg-gray-800 px-6 py-4 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm">
            {{ $accountTypes->links() }}
        </div>
    @endif
</div>
@endsection
