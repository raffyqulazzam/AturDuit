@extends('layouts.app')

@section('title', 'Detail Transaksi - AturDuit')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('transactions.index') }}" class="hover:text-gray-700">Transaksi</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>Detail Transaksi</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Detail Transaksi</h2>
                    <p class="text-sm text-gray-600">{{ $transaction->description }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('transactions.edit', $transaction) }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                        <i data-lucide="edit-2" class="w-4 h-4 mr-2"></i>
                        Edit
                    </a>
                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-md border border-red-200">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Transaction Info -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Transaksi</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Transaction Type and Amount -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                @if($transaction->type === 'income')
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Pemasukan</p>
                                        <p class="text-xs text-gray-500">Income Transaction</p>
                                    </div>
                                @else
                                    <div class="p-2 bg-red-100 rounded-lg">
                                        <i data-lucide="trending-down" class="w-6 h-6 text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Pengeluaran</p>
                                        <p class="text-xs text-gray-500">Expense Transaction</p>
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Deskripsi</h4>
                            <p class="text-gray-900">{{ $transaction->description }}</p>
                        </div>

                        <!-- Date -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi</h4>
                            <div class="flex items-center space-x-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d F Y') }}</span>
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-500">{{ \Carbon\Carbon::parse($transaction->transaction_date)->diffForHumans() }}</span>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($transaction->notes)
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Catatan</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-gray-700">{{ $transaction->notes }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-500">
                                <div>
                                    <p class="font-medium">Dibuat</p>
                                    <p>{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div>
                                    <p class="font-medium">Terakhir Diupdate</p>
                                    <p>{{ $transaction->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Information -->
            <div class="space-y-6">
                <!-- Category Card -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Kategori</h4>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i data-lucide="tag" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $transaction->category->name }}</p>
                                <p class="text-sm text-gray-500">Kategori transaksi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Card -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Akun</h4>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                @if($transaction->account->type === 'savings')
                                    <i data-lucide="piggy-bank" class="w-5 h-5 text-purple-600"></i>
                                @elseif($transaction->account->type === 'checking')
                                    <i data-lucide="credit-card" class="w-5 h-5 text-purple-600"></i>
                                @else
                                    <i data-lucide="wallet" class="w-5 h-5 text-purple-600"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $transaction->account->name }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($transaction->account->type) }}</p>
                                <p class="text-sm text-gray-500">Saldo: {{ format_idr($transaction->account->balance) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Aksi Cepat</h4>
                    </div>
                    <div class="p-4 space-y-3">
                        <a href="{{ route('transactions.create') }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Tambah Transaksi Baru
                        </a>
                        
                        <a href="{{ route('transactions.index', ['category' => $transaction->category->id]) }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                            <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                            Lihat Kategori Serupa
                        </a>
                        
                        <a href="{{ route('transactions.index', ['account' => $transaction->account->id]) }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                            <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                            Lihat Transaksi Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
