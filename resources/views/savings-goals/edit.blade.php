@extends('layouts.app')

@section('title', 'Edit Target Tabungan - AturDuit')
@section('page-title', 'Edit Target Tabungan')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('savings-goals.index') }}" class="hover:text-gray-700">Target Tabungan</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>Edit Target</span>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Edit Target: {{ $savingsGoal->name }}</h2>
            <p class="text-sm text-gray-600">Ubah pengaturan target tabungan Anda</p>
        </div>

        <form action="{{ route('savings-goals.update', $savingsGoal) }}" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Target</h3>
            </div>
            
            <div class="px-6 py-6 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Target <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name', $savingsGoal->name) }}"
                           placeholder="Contoh: Liburan ke Bali, Dana Darurat, Beli Laptop"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Amount -->
                <div>
                    <label for="target_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Target Jumlah <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="target_amount" name="target_amount" min="10000" step="10000" required
                               value="{{ old('target_amount', $savingsGoal->target_amount) }}"
                               placeholder="0"
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('target_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Amount -->
                <div>
                    <label for="current_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Saat Ini
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="current_amount" name="current_amount" min="0" step="1000"
                               value="{{ old('current_amount', $savingsGoal->current_amount) }}"
                               placeholder="0"
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('current_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Date -->
                <div>
                    <label for="target_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Target Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="target_date" name="target_date" required
                           value="{{ old('target_date', $savingsGoal->target_date) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('target_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Ceritakan tentang tujuan tabungan Anda..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $savingsGoal->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Progress -->
                @php
                    $percentage = $savingsGoal->target_amount > 0 ? ($savingsGoal->current_amount / $savingsGoal->target_amount) * 100 : 0;
                    $remaining = $savingsGoal->target_amount - $savingsGoal->current_amount;
                    $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($savingsGoal->target_date), false);
                @endphp
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Progress Saat Ini</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Terkumpul: Rp {{ number_format($savingsGoal->current_amount, 0, ',', '.') }}</span>
                            <span class="font-medium text-gray-900">{{ number_format($percentage, 1) }}%</span>
                        </div>
                        
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $percentage >= 100 ? 'bg-green-500' : ($percentage > 75 ? 'bg-blue-500' : ($percentage > 50 ? 'bg-yellow-500' : 'bg-red-500')) }}" 
                                 style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>{{ abs($daysLeft) }} hari {{ $daysLeft >= 0 ? 'tersisa' : 'terlambat' }}</span>
                            <span>Target: Rp {{ number_format($savingsGoal->target_amount, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($remaining > 0)
                            <div class="text-sm text-gray-600">
                                Sisa: <span class="font-medium text-red-600">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Savings Calculation -->
                <div id="savingsCalculation" class="bg-blue-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Perhitungan Tabungan</h4>
                    <div class="text-sm text-blue-800 space-y-1">
                        <div>Sisa target: <span id="calcTarget" class="font-medium"></span></div>
                        <div>Waktu: <span id="calcDays" class="font-medium"></span> hari</div>
                        <div>Tabungan per hari: <span id="calcDaily" class="font-medium"></span></div>
                        <div>Tabungan per bulan: <span id="calcMonthly" class="font-medium"></span></div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('savings-goals.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Update Target
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function calculateSavings() {
    const targetAmount = parseFloat(document.getElementById('target_amount').value) || 0;
    const currentAmount = parseFloat(document.getElementById('current_amount').value) || 0;
    const targetDate = new Date(document.getElementById('target_date').value);
    const today = new Date();
    
    if (targetAmount > 0 && targetDate > today) {
        const remaining = Math.max(0, targetAmount - currentAmount);
        const daysLeft = Math.ceil((targetDate - today) / (1000 * 60 * 60 * 24));
        const dailySavings = remaining / daysLeft;
        const monthlySavings = dailySavings * 30;
        
        document.getElementById('calcTarget').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
        document.getElementById('calcDays').textContent = daysLeft;
        document.getElementById('calcDaily').textContent = 'Rp ' + Math.ceil(dailySavings).toLocaleString('id-ID');
        document.getElementById('calcMonthly').textContent = 'Rp ' + Math.ceil(monthlySavings).toLocaleString('id-ID');
        
        document.getElementById('savingsCalculation').style.display = 'block';
    } else {
        document.getElementById('savingsCalculation').style.display = 'none';
    }
}

// Update calculation when inputs change
document.getElementById('target_amount').addEventListener('input', calculateSavings);
document.getElementById('current_amount').addEventListener('input', calculateSavings);
document.getElementById('target_date').addEventListener('change', calculateSavings);

// Initial calculation
calculateSavings();
</script>
@endsection
