@extends('layouts.app')

@section('title', 'Detail Akun - AturDuit')
@section('page-title', 'Detail Akun')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('accounts.index') }}" class="hover:text-gray-700">Akun</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>{{ $account->name }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ $account->name }}</h2>
                    <p class="text-sm text-gray-600">{{ ucfirst($account->type) }} Account</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('accounts.edit', $account) }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                        <i data-lucide="edit-2" class="w-4 h-4 mr-2"></i>
                        Edit
                    </a>
                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini? Semua transaksi terkait akan ikut terhapus.')" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-md border border-red-200">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Account Overview -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Akun</h3>
                    </div>
                    <div class="p-6">
                        <!-- Account Type and Balance -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                @if($account->type === 'savings')
                                    <div class="p-3 bg-blue-100 rounded-lg">
                                        <i data-lucide="piggy-bank" class="w-8 h-8 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Akun Tabungan</p>
                                        <p class="text-sm text-gray-500">Savings Account</p>
                                    </div>
                                @elseif($account->type === 'checking')
                                    <div class="p-3 bg-green-100 rounded-lg">
                                        <i data-lucide="credit-card" class="w-8 h-8 text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Akun Giro</p>
                                        <p class="text-sm text-gray-500">Checking Account</p>
                                    </div>
                                @else
                                    <div class="p-3 bg-purple-100 rounded-lg">
                                        <i data-lucide="wallet" class="w-8 h-8 text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Tunai</p>
                                        <p class="text-sm text-gray-500">Cash Account</p>
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Saldo Saat Ini</p>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($account->description)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Deskripsi</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-gray-700">{{ $account->description }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Account Stats -->
                        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $account->transactions()->where('type', 'income')->count() }}</p>
                                <p class="text-sm text-gray-500">Pemasukan</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-red-600">{{ $account->transactions()->where('type', 'expense')->count() }}</p>
                                <p class="text-sm text-gray-500">Pengeluaran</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-600">{{ $account->transactions()->count() }}</p>
                                <p class="text-sm text-gray-500">Total Transaksi</p>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="pt-6 border-t border-gray-200 mt-6">
                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-500">
                                <div>
                                    <p class="font-medium">Akun Dibuat</p>
                                    <p>{{ $account->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div>
                                    <p class="font-medium">Terakhir Diupdate</p>
                                    <p>{{ $account->updated_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                @if($account->transactions()->count() > 0)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Transaksi Terbaru</h3>
                        <a href="{{ route('transactions.index', ['account' => $account->id]) }}" 
                           class="text-sm text-blue-600 hover:text-blue-700">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($account->transactions()->latest()->take(5)->get() as $transaction)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                @if($transaction->type === 'income')
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <i data-lucide="trending-up" class="w-4 h-4 text-green-600"></i>
                                    </div>
                                @else
                                    <div class="p-2 bg-red-100 rounded-lg">
                                        <i data-lucide="trending-down" class="w-4 h-4 text-red-600"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $transaction->description }}</p>
                                    <p class="text-sm text-gray-500">{{ $transaction->category->name }} • {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Side Information -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Aksi Cepat</h4>
                    </div>
                    <div class="p-4 space-y-3">
                        <a href="{{ route('transactions.create', ['account' => $account->id]) }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Tambah Transaksi
                        </a>
                        
                        <a href="{{ route('transactions.index', ['account' => $account->id]) }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                            <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                            Lihat Semua Transaksi
                        </a>
                        
                        <a href="{{ route('accounts.create') }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                            <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i>
                            Buat Akun Baru
                        </a>
                    </div>
                </div>

                <!-- Account Summary -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Ringkasan</h4>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Pemasukan</span>
                            <span class="text-sm font-medium text-green-600">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Pengeluaran</span>
                            <span class="text-sm font-medium text-red-600">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-900">Saldo Saat Ini</span>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Type Info -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Informasi Jenis Akun</h4>
                    </div>
                    <div class="p-4">
                        @if($account->type === 'savings')
                            <div class="flex items-start space-x-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Akun Tabungan</p>
                                    <p class="text-sm text-gray-600">Akun untuk menyimpan dana jangka panjang dan target tabungan.</p>
                                </div>
                            </div>
                        @elseif($account->type === 'checking')
                            <div class="flex items-start space-x-3">
                                <i data-lucide="info" class="w-5 h-5 text-green-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Akun Giro</p>
                                    <p class="text-sm text-gray-600">Akun untuk transaksi harian dan pembayaran rutin.</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-start space-x-3">
                                <i data-lucide="info" class="w-5 h-5 text-purple-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Uang Tunai</p>
                                    <p class="text-sm text-gray-600">Uang tunai untuk transaksi sehari-hari dan kebutuhan mendadak.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
