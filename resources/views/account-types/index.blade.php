@extends('layouts.app')

@section('title', 'Jenis Akun')

@section('content')
<div class="space-y-8 px-6 sm:px-8 lg:px-12 xl:px-16 py-8 sm:py-10 lg:py-12">
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($accountTypes as $accountType)
            <div class="group bg-white dark:bg-gray-800 shadow-sm hover:shadow-xl rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden">
                <!-- Color accent -->
                <div class="absolute top-0 left-0 w-full h-1" style="background: linear-gradient(90deg, {{ $accountType->color }}, {{ $accountType->color }}cc)"></div>
                
                <!-- Header Section -->
                <div class="p-6 pb-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center flex-1 min-w-0">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md group-hover:shadow-lg group-hover:scale-105 transition-all duration-200" style="background: linear-gradient(135deg, {{ $accountType->color }}, {{ $accountType->color }}dd)">
                                    <i data-lucide="{{ $accountType->icon }}" class="h-6 w-6"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate mb-1">{{ $accountType->name }}</h3>
                                @if($accountType->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">{{ $accountType->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0 ml-3">
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
                </div>

                <!-- Accounts List Section -->
                <div class="px-6 pb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Akun yang Menggunakan</h4>
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 rounded-full border border-blue-200 dark:border-blue-700">
                            {{ $accountType->user_accounts_count ?? 0 }}
                        </span>
                    </div>
                    
                    @if($accountType->userAccounts && $accountType->userAccounts->count() > 0)
                        <div class="space-y-2">
                            @foreach($accountType->userAccounts as $account)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div class="flex items-center justify-center w-8 h-8 rounded-lg shadow-sm mr-3 flex-shrink-0" style="background: linear-gradient(135deg, {{ $accountType->color }}, {{ $accountType->color }}dd)">
                                            <i data-lucide="{{ $accountType->icon }}" class="h-4 w-4 text-white"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $account->name }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                                {{ format_idr($account->balance) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-3">
                                        @if($account->is_active)
                                            <span class="flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full">
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                            </span>
                                        @else
                                            <span class="flex items-center justify-center w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full">
                                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($accountType->user_accounts_count > 3)
                                <div class="text-center pt-2">
                                    <a href="{{ route('account-types.show', $accountType) }}" class="inline-flex items-center text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-150">
                                        <span>Lihat {{ $accountType->user_accounts_count - 3 }} akun lainnya</span>
                                        <i data-lucide="arrow-right" class="ml-1 h-3 w-3"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3" style="background: linear-gradient(135deg, {{ $accountType->color }}20, {{ $accountType->color }}40)">
                                <i data-lucide="wallet" class="h-6 w-6" style="color: {{ $accountType->color }}"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Belum ada akun</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">yang menggunakan jenis ini</p>
                        </div>
                    @endif
                </div>

                <!-- Actions Section -->
                <div class="px-6 pb-6 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex gap-2 mb-3">
                        <a href="{{ route('account-types.edit', $accountType) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/50 dark:hover:bg-blue-900/70 text-blue-700 hover:text-blue-800 dark:text-blue-300 dark:hover:text-blue-200 text-sm font-medium rounded-lg border border-blue-200 dark:border-blue-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-150 group">
                            <i data-lucide="edit" class="mr-1.5 h-4 w-4 group-hover:scale-110 transition-transform duration-150"></i>
                            Edit
                        </a>
                        <a href="{{ route('account-types.show', $accountType) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-200 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-150 group">
                            <i data-lucide="eye" class="mr-1.5 h-4 w-4 group-hover:scale-110 transition-transform duration-150"></i>
                            Detail
                        </a>
                    </div>
                    @if($accountType->user_accounts_count == 0)
                        <form action="{{ route('account-types.destroy', $accountType) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2.5 bg-red-50 hover:bg-red-100 dark:bg-red-900/50 dark:hover:bg-red-900/70 text-red-700 hover:text-red-800 dark:text-red-300 dark:hover:text-red-200 text-sm font-medium rounded-lg border border-red-200 dark:border-red-700 hover:border-red-300 dark:hover:border-red-600 transition-all duration-150 group" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis akun ini?')">
                                <i data-lucide="trash-2" class="mr-1.5 h-4 w-4 group-hover:scale-110 transition-transform duration-150"></i>
                                Hapus Jenis Akun
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-20 px-6">
                    <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-blue-100 dark:border-blue-800">
                        <i data-lucide="layers" class="h-12 w-12 text-blue-500 dark:text-blue-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Belum ada jenis akun</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">Mulai dengan membuat jenis akun pertama Anda untuk mengorganisir keuangan dengan lebih baik.</p>
                    <a href="{{ route('account-types.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-xl font-semibold text-sm text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
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
