@extends('layouts.app')

@section('title', 'Tambah Budget - AturDuit')
@section('page-title', 'Tambah Budget')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('budgets.index') }}" class="hover:text-gray-700">Budget</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>Tambah Budget</span>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Buat Budget Baru</h2>
            <p class="text-sm text-gray-600">Atur budget untuk kategori pengeluaran dalam periode tertentu</p>
        </div>

        <form action="{{ route('budgets.store') }}" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200">
            @csrf
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Budget</h3>
            </div>
            
            <div class="px-6 py-6 space-y-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required 
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category_id') border-red-300 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Budget <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="amount" name="amount" min="0" step="1000" required
                               value="{{ old('amount') }}"
                               placeholder="0"
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('amount') border-red-300 @enderror">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Period -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="period_start" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="period_start" name="period_start" required
                               value="{{ old('period_start', now()->format('Y-m-d')) }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('period_start') border-red-300 @enderror">
                        @error('period_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="period_end" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Berakhir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="period_end" name="period_end" required
                               value="{{ old('period_end', now()->endOfMonth()->format('Y-m-d')) }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('period_end') border-red-300 @enderror">
                        @error('period_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Quick Period Buttons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Periode Cepat
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setThisMonth()" 
                                class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Bulan Ini
                        </button>
                        <button type="button" onclick="setNextMonth()" 
                                class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Bulan Depan
                        </button>
                        <button type="button" onclick="setThisQuarter()" 
                                class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Kuartal Ini
                        </button>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Catatan atau deskripsi budget..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('budgets.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Simpan Budget
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function setThisMonth() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    document.getElementById('period_start').value = firstDay.toISOString().split('T')[0];
    document.getElementById('period_end').value = lastDay.toISOString().split('T')[0];
}

function setNextMonth() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth() + 1, 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 2, 0);
    
    document.getElementById('period_start').value = firstDay.toISOString().split('T')[0];
    document.getElementById('period_end').value = lastDay.toISOString().split('T')[0];
}

function setThisQuarter() {
    const now = new Date();
    const quarter = Math.floor(now.getMonth() / 3);
    const firstDay = new Date(now.getFullYear(), quarter * 3, 1);
    const lastDay = new Date(now.getFullYear(), quarter * 3 + 3, 0);
    
    document.getElementById('period_start').value = firstDay.toISOString().split('T')[0];
    document.getElementById('period_end').value = lastDay.toISOString().split('T')[0];
}

// Validate dates
document.getElementById('period_start').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateInput = document.getElementById('period_end');
    const endDate = new Date(endDateInput.value);
    
    if (endDate <= startDate) {
        const newEndDate = new Date(startDate);
        newEndDate.setMonth(newEndDate.getMonth() + 1);
        newEndDate.setDate(0); // Last day of the month
        endDateInput.value = newEndDate.toISOString().split('T')[0];
    }
});

document.getElementById('period_end').addEventListener('change', function() {
    const endDate = new Date(this.value);
    const startDateInput = document.getElementById('period_start');
    const startDate = new Date(startDateInput.value);
    
    if (startDate >= endDate) {
        const newStartDate = new Date(endDate);
        newStartDate.setDate(1); // First day of the month
        startDateInput.value = newStartDate.toISOString().split('T')[0];
    }
});
</script>
@endsection
