@extends('layouts.app')

@section('title', 'Edit Budget - AturDuit')
@section('page-title', 'Edit Budget')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('budgets.index') }}" class="hover:text-gray-700">Budget</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span>Edit Budget</span>
            </div>
            <h2 class="text-lg font-medium text-gray-900">Edit Budget</h2>
            <p class="text-sm text-gray-600">Ubah pengaturan budget untuk kategori {{ $budget->category->name }}</p>
        </div>

        <form action="{{ route('budgets.update', $budget) }}" method="POST" class="bg-white shadow-sm rounded-lg border border-gray-200">
            @csrf
            @method('PUT')
            
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
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ ($budget->category_id == $category->id || old('category_id') == $category->id) ? 'selected' : '' }}>
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
                               value="{{ old('amount', $budget->amount) }}"
                               placeholder="0"
                               class="block w-full pl-12 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                               value="{{ old('period_start', $budget->period_start) }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('period_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="period_end" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Berakhir <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="period_end" name="period_end" required
                               value="{{ old('period_end', $budget->period_end) }}"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('period_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Catatan atau deskripsi budget..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $budget->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Budget Progress -->
                @php
                    $spent = $budget->category->transactions()
                        ->where('type', 'expense')
                        ->whereBetween('transaction_date', [$budget->period_start, $budget->period_end])
                        ->sum('amount');
                    $percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
                    $remaining = $budget->amount - $spent;
                @endphp
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Progress Budget Saat Ini</h4>
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
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('budgets.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300">
                    <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                    Update Budget
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
