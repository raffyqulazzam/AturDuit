@extends('layouts.app')

@section('title', 'Transaksi - AturDuit')
@section('page-title', 'Transaksi')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Semua Transaksi</h2>
                <div class="flex items-center space-x-2 mt-1">
                    <p class="text-sm text-gray-600">Kelola dan pantau semua transaksi keuangan Anda</p>
                    @if(request()->hasAny(['type', 'category_id', 'account_id', 'date_from', 'date_to']))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                            <i data-lucide="filter" class="w-3 h-3 mr-1"></i>
                            Filter Aktif
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Transaksi
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-100 overflow-hidden mb-6">
            <!-- Filter Header -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="filter" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Filter Transaksi</h3>
                    </div>
                    @if(request()->hasAny(['type', 'category_id', 'account_id', 'date_from', 'date_to']))
                        <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors duration-200">
                            <i data-lucide="x" class="w-3 h-3 mr-1"></i>
                            Reset Filter
                        </a>
                    @endif
                </div>
            </div>

            <!-- Filter Form -->
            <div class="p-6">
                <form method="GET" action="{{ route('transactions.index') }}" class="space-y-6">
                    <!-- Row 1: Type and Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                <i data-lucide="tag" class="w-4 h-4 inline mr-1"></i>
                                Jenis Transaksi
                            </label>
                            <select id="type" name="type" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 bg-gray-50 hover:bg-white">
                                <option value="">🔍 Semua Jenis</option>
                                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>
                                    💰 Pemasukan
                                </option>
                                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>
                                    💸 Pengeluaran
                                </option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                <i data-lucide="folder" class="w-4 h-4 inline mr-1"></i>
                                Kategori
                            </label>
                            <select id="category_id" name="category_id" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 bg-gray-50 hover:bg-white">
                                <option value="">📁 Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label for="account_id" class="block text-sm font-medium text-gray-700">
                                <i data-lucide="credit-card" class="w-4 h-4 inline mr-1"></i>
                                Akun
                            </label>
                            <select id="account_id" name="account_id" class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 bg-gray-50 hover:bg-white">
                                <option value="">💳 Semua Akun</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 2: Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="date_from" class="block text-sm font-medium text-gray-700">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                Dari Tanggal
                            </label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" 
                                   class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                        
                        <div class="space-y-2">
                            <label for="date_to" class="block text-sm font-medium text-gray-700">
                                <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                                Sampai Tanggal
                            </label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" 
                                   class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all duration-200 bg-gray-50 hover:bg-white">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transaction Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pemasukan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-down" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="activity" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_count'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Transaksi</h3>
            </div>
            
            @if($transactions->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                        <div class="px-6 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4
                                        {{ $transaction->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        <i data-lucide="{{ $transaction->type === 'income' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $transaction->description }}</h4>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                                            <span>{{ $transaction->category->name ?? 'Uncategorized' }}</span>
                                            <span>•</span>
                                            <span>{{ $transaction->account->name ?? 'Unknown' }}</span>
                                            <span>•</span>
                                            <span>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-lg font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </p>
                                        @if($transaction->notes)
                                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($transaction->notes, 30) }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="text-blue-600 hover:text-blue-900">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i data-lucide="credit-card" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada transaksi</h3>
                    <p class="text-gray-500 mb-6">Mulai mencatat transaksi keuangan Anda</p>
                    <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Tambah Transaksi Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    function validateDateRange() {
        if (dateFrom.value && dateTo.value && dateFrom.value > dateTo.value) {
            dateTo.setCustomValidity('Tanggal akhir harus lebih besar atau sama dengan tanggal awal');
            dateTo.classList.add('border-red-500', 'ring-red-500');
        } else {
            dateTo.setCustomValidity('');
            dateTo.classList.remove('border-red-500', 'ring-red-500');
        }
    }
    
    dateFrom.addEventListener('change', validateDateRange);
    dateTo.addEventListener('change', validateDateRange);
    
    // Auto-expand collapsed filter if filters are active
    const hasActiveFilters = {{ request()->hasAny(['type', 'category_id', 'account_id', 'date_from', 'date_to']) ? 'true' : 'false' }};
    
    // Enhanced form interactions
    const form = document.querySelector('form');
    const selects = form.querySelectorAll('select');
    const inputs = form.querySelectorAll('input[type="date"]');
    
    // Add visual feedback for form interactions
    [...selects, ...inputs].forEach(element => {
        element.addEventListener('focus', function() {
            this.parentElement.classList.add('transform', 'scale-105');
        });
        
        element.addEventListener('blur', function() {
            this.parentElement.classList.remove('transform', 'scale-105');
        });
    });
    
    // Show loading state on form submission
    form.addEventListener('submit', function() {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i>Mencari...';
        submitBtn.disabled = true;
        
        // Re-enable after a delay (in case of validation errors)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});
</script>
@endsection
