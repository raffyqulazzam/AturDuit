@extends('layouts.app')

@section('title', 'Tambah Target Tabungan - AturDuit')
@section('page-title', 'Tambah Target Tabungan')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('savings-goals.index') }}" class="hover:text-gray-700">Target Tabungan</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>Tambah Target</span>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Buat Target Tabungan Baru</h2>
            <p class="text-sm text-gray-600">Tetapkan target tabungan untuk mencapai tujuan keuangan Anda</p>
        </div>

        <form action="{{ route('savings-goals.store') }}" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200">
            @csrf
            
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
                           value="{{ old('name') }}"
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
                               value="{{ old('target_amount') }}"
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
                        Jumlah Saat Ini (Opsional)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="current_amount" name="current_amount" min="0" step="1000"
                               value="{{ old('current_amount', 0) }}"
                               placeholder="0"
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Jika Anda sudah memiliki tabungan untuk target ini</p>
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
                           value="{{ old('target_date', now()->addMonths(6)->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('target_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quick Target Buttons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Target Cepat
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <button type="button" onclick="setTarget(3000000, '3 bulan')" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            3 Bulan
                        </button>
                        <button type="button" onclick="setTarget(6000000, '6 bulan')" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            6 Bulan
                        </button>
                        <button type="button" onclick="setTarget(12000000, '1 tahun')" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            1 Tahun
                        </button>
                        <button type="button" onclick="setTarget(24000000, '2 tahun')" 
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            2 Tahun
                        </button>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Ceritakan tentang tujuan tabungan Anda..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Savings Calculation -->
                <div id="savingsCalculation" class="bg-blue-50 rounded-lg p-4 hidden">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Perhitungan Tabungan</h4>
                    <div class="text-sm text-blue-800 space-y-1">
                        <div>Target: <span id="calcTarget" class="font-medium"></span></div>
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
                    Simpan Target
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function setTarget(amount, period) {
    document.getElementById('target_amount').value = amount;
    
    const targetDate = new Date();
    if (period.includes('3 bulan')) {
        targetDate.setMonth(targetDate.getMonth() + 3);
    } else if (period.includes('6 bulan')) {
        targetDate.setMonth(targetDate.getMonth() + 6);
    } else if (period.includes('1 tahun')) {
        targetDate.setFullYear(targetDate.getFullYear() + 1);
    } else if (period.includes('2 tahun')) {
        targetDate.setFullYear(targetDate.getFullYear() + 2);
    }
    
    document.getElementById('target_date').value = targetDate.toISOString().split('T')[0];
    calculateSavings();
}

function calculateSavings() {
    const targetAmount = parseFloat(document.getElementById('target_amount').value) || 0;
    const currentAmount = parseFloat(document.getElementById('current_amount').value) || 0;
    const targetDate = new Date(document.getElementById('target_date').value);
    const today = new Date();
    
    if (targetAmount > 0 && targetDate > today) {
        const remaining = targetAmount - currentAmount;
        const daysLeft = Math.ceil((targetDate - today) / (1000 * 60 * 60 * 24));
        const dailySavings = remaining / daysLeft;
        const monthlySavings = dailySavings * 30;
        
        document.getElementById('calcTarget').textContent = 'Rp ' + remaining.toLocaleString('id-ID');
        document.getElementById('calcDays').textContent = daysLeft;
        document.getElementById('calcDaily').textContent = 'Rp ' + Math.ceil(dailySavings).toLocaleString('id-ID');
        document.getElementById('calcMonthly').textContent = 'Rp ' + Math.ceil(monthlySavings).toLocaleString('id-ID');
        
        document.getElementById('savingsCalculation').classList.remove('hidden');
    } else {
        document.getElementById('savingsCalculation').classList.add('hidden');
    }
}

// Update calculation when inputs change
document.getElementById('target_amount').addEventListener('input', calculateSavings);
document.getElementById('current_amount').addEventListener('input', calculateSavings);
document.getElementById('target_date').addEventListener('change', calculateSavings);

// Set minimum date to today
document.getElementById('target_date').min = new Date().toISOString().split('T')[0];

// Initial calculation
calculateSavings();
</script>
@endsection
