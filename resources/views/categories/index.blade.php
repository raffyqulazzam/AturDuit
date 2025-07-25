@extends('layouts.app')

@section('page-title', 'Kategori')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori</h2>
                <p class="text-gray-600 dark:text-gray-400">Kelola kategori transaksi Anda, diurutkan berdasarkan aktivitas</p>
            </div>
            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Kategori
            </a>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories ?? [] as $index => $category)
                @php
                    $totalActivity = ($category->transactions_count ?? 0) + ($category->budgets_count ?? 0);
                    $isTopCategory = $index < 3 && $totalActivity > 0; // Top 3 categories with activity
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 {{ $isTopCategory ? 'ring-2 ring-blue-200 dark:ring-blue-800' : '' }}">
                    @if($isTopCategory)
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                                Paling Aktif
                            </span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: {{ $category->color ?? '#3B82F6' }}20">
                                <i data-lucide="{{ $category->icon ?? 'tag' }}" class="w-5 h-5" style="color: {{ $category->color ?? '#3B82F6' }}"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $category->type === 'income' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200' }}">
                                    {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @php
                                $totalActivity = ($category->transactions_count ?? 0) + ($category->budgets_count ?? 0);
                            @endphp
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $totalActivity }} total aktivitas</span>
                                @if($totalActivity > 10)
                                    <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200">
                                        Tinggi
                                    </span>
                                @elseif($totalActivity > 5)
                                    <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200">
                                        Sedang
                                    </span>
                                @elseif($totalActivity > 0)
                                    <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                        Rendah
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3 text-xs">
                                <span>{{ $category->transactions_count ?? 0 }} transaksi</span>
                                <span>{{ $category->budgets_count ?? 0 }} budget</span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('categories.show', $category) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200" title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('categories.edit', $category) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200" title="Edit Kategori">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            @if($category->transactions_count > 0 || $category->budgets_count > 0)
                                <button type="button" class="text-gray-400 cursor-not-allowed" 
                                        title="Kategori tidak bisa dihapus karena masih memiliki {{ $category->transactions_count > 0 ? $category->transactions_count . ' transaksi' : '' }}{{ $category->transactions_count > 0 && $category->budgets_count > 0 ? ' dan ' : '' }}{{ $category->budgets_count > 0 ? $category->budgets_count . ' budget' : '' }}">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            @else
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200" 
                                            onclick="return confirm('Yakin ingin menghapus kategori \'{{ $category->name }}\'?')" title="Hapus Kategori">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i data-lucide="tag" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada kategori</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai dengan membuat kategori pertama Anda</p>
                    <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Tambah Kategori
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
