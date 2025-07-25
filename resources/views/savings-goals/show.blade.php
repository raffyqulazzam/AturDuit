@extends('layouts.app')

@section('title', 'Detail Target Tabungan - AturDuit')
@section('page-title', 'Detail Target Tabungan')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('savings-goals.index') }}" class="hover:text-gray-700">Target Tabungan</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>{{ $savingsGoal->title }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium text-gray-900">{{ $savingsGoal->title }}</h2>
                    <p class="text-sm text-gray-600">{{ $savingsGoal->description }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('savings-goals.edit', $savingsGoal) }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                        <i data-lucide="edit-2" class="w-4 h-4 mr-2"></i>
                        Edit
                    </a>
                    <form action="{{ route('savings-goals.destroy', $savingsGoal) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus target tabungan ini?')" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-md border border-red-200">
                            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Progress Overview -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Progress Tabungan</h3>
                    </div>
                    <div class="p-6">
                        <!-- Progress Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($savingsGoal->current_amount, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Terkumpul</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">Rp {{ number_format($savingsGoal->target_amount, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Target</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-600">Rp {{ number_format($savingsGoal->target_amount - $savingsGoal->current_amount, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Sisa</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @php
                            $progressPercentage = $savingsGoal->target_amount > 0 ? min(($savingsGoal->current_amount / $savingsGoal->target_amount) * 100, 100) : 0;
                        @endphp
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm text-gray-500">{{ number_format($progressPercentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>

                        <!-- Target Date Info -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-medium text-gray-700">Target Tanggal</p>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($savingsGoal->target_date)->format('d F Y') }}</p>
                                <p class="text-gray-500">{{ \Carbon\Carbon::parse($savingsGoal->target_date)->diffForHumans() }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-700">Status</p>
                                @if($progressPercentage >= 100)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Target Tercapai
                                    </span>
                                @elseif(\Carbon\Carbon::parse($savingsGoal->target_date)->isPast())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i data-lucide="target" class="w-3 h-3 mr-1"></i>
                                        Dalam Progress
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Add Saving Form -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Tambah Tabungan</h4>
                            <form action="{{ route('savings-goals.add-saving', $savingsGoal) }}" method="POST" class="flex items-end space-x-4">
                                @csrf
                                <div class="flex-1">
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" id="amount" name="amount" min="1000" step="1000" required
                                               placeholder="0"
                                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                    <input type="text" id="notes" name="notes" placeholder="Catatan tabungan"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200">
                                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                    Tambah
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Information -->
            <div class="space-y-6">
                <!-- Goal Info Card -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Informasi Target</h4>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Dibuat</p>
                            <p class="text-sm text-gray-600">{{ $savingsGoal->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Terakhir Diupdate</p>
                            <p class="text-sm text-gray-600">{{ $savingsGoal->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                        @if($progressPercentage >= 100)
                        <div>
                            <p class="text-sm font-medium text-gray-700">Target Tercapai</p>
                            <p class="text-sm text-green-600 font-medium">
                                <i data-lucide="trophy" class="w-4 h-4 inline mr-1"></i>
                                Selamat! Target Anda tercapai
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Calculation Helper -->
                @if($progressPercentage < 100)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Perhitungan</h4>
                    </div>
                    <div class="p-4 space-y-3">
                        @php
                            $remaining = $savingsGoal->target_amount - $savingsGoal->current_amount;
                            $daysLeft = max(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($savingsGoal->target_date)), 1);
                            $dailyNeeded = $remaining / $daysLeft;
                            $monthlyNeeded = $remaining / max(ceil($daysLeft / 30), 1);
                        @endphp
                        <div>
                            <p class="text-sm font-medium text-gray-700">Per Hari</p>
                            <p class="text-sm text-gray-900">Rp {{ number_format($dailyNeeded, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Per Bulan</p>
                            <p class="text-sm text-gray-900">Rp {{ number_format($monthlyNeeded, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Sisa Hari</p>
                            <p class="text-sm text-gray-900">{{ $daysLeft }} hari</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900">Aksi Cepat</h4>
                    </div>
                    <div class="p-4 space-y-3">
                        <a href="{{ route('savings-goals.create') }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Target Baru
                        </a>
                        
                        <a href="{{ route('savings-goals.index') }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                            <i data-lucide="list" class="w-4 h-4 mr-2"></i>
                            Semua Target
                        </a>
                        
                        <a href="{{ route('transactions.index') }}" 
                           class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200">
                            <i data-lucide="trending-up" class="w-4 h-4 mr-2"></i>
                            Lihat Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
