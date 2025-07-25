@extends('layouts.app')

@section('title', 'Tabungan - AturDuit')
@section('page-title', 'Target Tabungan')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Target Tabungan</h2>
                <p class="text-sm text-gray-600">Atur dan pantau target tabungan untuk mencapai tujuan keuangan</p>
            </div>
            <a href="{{ route('savings-goals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Target
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Savings Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="target" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Target</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($savingsGoals->sum('target_amount'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="piggy-bank" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Terkumpul</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($savingsGoals->sum('current_amount'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Target Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $savingsGoals->where('target_date', '>=', now())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trophy" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tercapai</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $savingsGoals->filter(function($goal) {
                                return $goal->current_amount >= $goal->target_amount;
                            })->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Savings Goals List -->
        @if($savingsGoals->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($savingsGoals as $goal)
                    @php
                        $percentage = $goal->target_amount > 0 ? ($goal->current_amount / $goal->target_amount) * 100 : 0;
                        $remaining = $goal->target_amount - $goal->current_amount;
                        $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($goal->target_date), false);
                        $isCompleted = $goal->current_amount >= $goal->target_amount;
                        $isOverdue = $daysLeft < 0 && !$isCompleted;
                    @endphp
                    
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i data-lucide="piggy-bank" class="w-6 h-6 text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $goal->name }}</h3>
                                    @if($goal->description)
                                        <p class="text-sm text-gray-500">{{ $goal->description }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                @if($isCompleted)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                        Tercapai
                                    </span>
                                @elseif($isOverdue)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        Terlambat
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        {{ abs($daysLeft) }} hari
                                    </span>
                                @endif
                                
                                <div class="flex items-center space-x-1">
                                    <a href="{{ route('savings-goals.edit', $goal) }}" class="text-blue-600 hover:text-blue-900">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('savings-goals.destroy', $goal) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus target tabungan ini?')">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Target: {{ \Carbon\Carbon::parse($goal->target_date)->format('d M Y') }}</span>
                                <span class="font-medium text-gray-900">{{ number_format($percentage, 1) }}%</span>
                            </div>
                            
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full {{ $isCompleted ? 'bg-green-500' : ($percentage > 75 ? 'bg-blue-500' : ($percentage > 50 ? 'bg-yellow-500' : 'bg-red-500')) }}" 
                                     style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">
                                    Terkumpul: <span class="font-medium text-gray-900">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                                </span>
                                <span class="text-gray-600">
                                    Target: <span class="font-medium text-gray-900">Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                                </span>
                            </div>
                            
                            @if($remaining > 0)
                                <div class="text-sm text-gray-600">
                                    Sisa: <span class="font-medium text-red-600">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button onclick="openAddSavingModal({{ $goal->id }}, '{{ $goal->name }}')" 
                                    class="w-full inline-flex justify-center items-center px-3 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                                Tambah Tabungan
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-12 text-center">
                <i data-lucide="piggy-bank" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada target tabungan</h3>
                <p class="text-gray-500 mb-6">Mulai tetapkan target tabungan untuk mencapai tujuan keuangan Anda</p>
                <a href="{{ route('savings-goals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Buat Target Pertama
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Add Saving Modal -->
<div id="addSavingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Tabungan</h3>
            <form id="addSavingForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="amount" min="1000" step="1000" required
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <input type="text" name="description" placeholder="Catatan untuk tabungan ini..."
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeAddSavingModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openAddSavingModal(goalId, goalName) {
    document.getElementById('addSavingForm').action = `/savings-goals/${goalId}/add-saving`;
    document.getElementById('addSavingModal').classList.remove('hidden');
}

function closeAddSavingModal() {
    document.getElementById('addSavingModal').classList.add('hidden');
    document.getElementById('addSavingForm').reset();
}

// Close modal when clicking outside
document.getElementById('addSavingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddSavingModal();
    }
});
</script>
@endsection
