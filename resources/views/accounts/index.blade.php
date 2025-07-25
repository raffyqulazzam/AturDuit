@extends('layouts.app')

@section('title', 'Kelola Akun - AturDuit')
@section('page-title', 'Kelola Akun')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Add Button -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Akun Keuangan</h2>
                <p class="text-gray-600">Kelola semua akun bank, e-wallet, dan kas Anda</p>
            </div>
            <a href="{{ route('accounts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Akun
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($accounts as $account)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                @if($account->type === 'bank') bg-blue-100 text-blue-600
                                @elseif($account->type === 'cash') bg-green-100 text-green-600
                                @elseif($account->type === 'ewallet') bg-purple-100 text-purple-600
                                @else bg-yellow-100 text-yellow-600
                                @endif">
                                @if($account->type === 'bank')
                                    <i data-lucide="building-2" class="w-5 h-5"></i>
                                @elseif($account->type === 'cash')
                                    <i data-lucide="banknote" class="w-5 h-5"></i>
                                @elseif($account->type === 'ewallet')
                                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                                @else
                                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $account->name }}</h3>
                                <p class="text-sm text-gray-500 capitalize">{{ $account->type }}</p>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            <a href="{{ route('accounts.edit', $account) }}" class="text-gray-400 hover:text-blue-600">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-2xl font-bold text-gray-900">
                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                        </p>
                        @if($account->description)
                            <p class="text-sm text-gray-600 mt-1">{{ $account->description }}</p>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            {{ $account->transactions_sum_amount ?? 0 }} transaksi
                        </span>
                        <a href="{{ route('accounts.show', $account) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="wallet" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada akun</h3>
                    <p class="text-gray-600 mb-4">Mulai dengan menambahkan akun pertama Anda</p>
                    <a href="{{ route('accounts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Tambah Akun Pertama
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
