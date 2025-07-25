@extends('layouts.app')

@section('title', 'Budget - AturDuit')
@section('page-title', 'Budget')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Kelola Budget</h2>
                <p class="text-sm text-gray-600">Pantau dan atur budget untuk setiap kategori pengeluaran</p>
            </div>
            <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Budget
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Budget Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="target" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Budget</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($budgets->sum('amount'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Budget Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $budgets->where('period_end', '>=', now())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Budget Terlampaui</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $budgets->filter(function($budget) {
                                $spent = $budget->category->transactions()
                                    ->where('type', 'expense')
                                    ->whereBetween('transaction_date', [$budget->period_start, $budget->period_end])
                                    ->sum('amount');
                                return $spent > $budget->amount;
                            })->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget List -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Daftar Budget</h3>
            </div>
            
            @if($budgets->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($budgets as $budget)
                        @php
                            $spent = $budget->category->transactions()
                                ->where('type', 'expense')
                                ->whereBetween('transaction_date', [$budget->period_start, $budget->period_end])
                                ->sum('amount');
                            $percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
                            $remaining = $budget->amount - $spent;
                            $isActive = $budget->period_end >= now();
                        @endphp
                        
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i data-lucide="tag" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $budget->category->name }}</h4>
                                        <p class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($budget->period_start)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($budget->period_end)->format('d M Y') }}
                                            @if($isActive)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                                    Berakhir
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('budgets.edit', $budget) }}" class="text-blue-600 hover:text-blue-900">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus budget ini?')">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Terpakai: Rp {{ number_format($spent, 0, ',', '.') }}</span>
                                    <span class="text-gray-600">Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $percentage > 100 ? 'bg-red-500' : ($percentage > 90 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                         style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>{{ number_format($percentage, 1) }}% terpakai</span>
                                    <span>Target: Rp {{ number_format($budget->amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i data-lucide="target" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada budget</h3>
                    <p class="text-gray-500 mb-6">Mulai atur budget untuk mengontrol pengeluaran Anda</p>
                    <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Buat Budget Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
