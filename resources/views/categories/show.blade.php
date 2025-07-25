@extends('layouts.app')

@section('page-title', 'Detail Kategori')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('categories.index') }}" class="hover:text-gray-700">Kategori</a>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    <span>{{ $category->name }}</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Kategori</h2>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                    <i data-lucide="edit-2" class="w-4 h-4 mr-2"></i>
                    Edit
                </a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        <!-- Category Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background-color: {{ $category->color ?? '#3B82F6' }}20">
                    <i data-lucide="{{ $category->icon ?? 'tag' }}" class="w-8 h-8" style="color: {{ $category->color ?? '#3B82F6' }}"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $category->type === 'income' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200' }}">
                        {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Transaksi</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $category->transactions_count ?? 0 }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Warna</p>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $category->color ?? '#3B82F6' }}"></div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->color ?? '#3B82F6' }}</p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Icon</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->icon ?? 'tag' }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Terbaru</h4>
            </div>
            <div class="p-6">
                @if($category->transactions && $category->transactions->count() > 0)
                    <div class="space-y-3">
                        @foreach($category->transactions->take(5) as $transaction)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $transaction->type === 'income' ? 'bg-green-100 dark:bg-green-900/50' : 'bg-red-100 dark:bg-red-900/50' }}">
                                        <i data-lucide="{{ $transaction->type === 'income' ? 'trending-up' : 'trending-down' }}" class="w-4 h-4 {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <p class="font-semibold {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}{{ format_idr($transaction->amount) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($category->transactions->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('transactions.index', ['category_id' => $category->id]) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 text-sm font-medium">
                                Lihat semua transaksi →
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i data-lucide="credit-card" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum ada transaksi</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Kategori ini belum memiliki transaksi</p>
                        <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Tambah Transaksi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
