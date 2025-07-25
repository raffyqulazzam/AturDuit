@extends('layouts.app')

@section('title', 'Dashboard - AturDuit')
@section('page-title', 'Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Balance -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="wallet" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Saldo</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalBalance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Income -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pemasukan Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</p>
                        @if($incomeChange > 0)
                            <p class="text-sm text-green-600">+{{ number_format($incomeChange, 1) }}% dari bulan lalu</p>
                        @elseif($incomeChange < 0)
                            <p class="text-sm text-red-600">{{ number_format($incomeChange, 1) }}% dari bulan lalu</p>
                        @else
                            <p class="text-sm text-gray-500">Sama dengan bulan lalu</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Monthly Expense -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-down" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pengeluaran Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</p>
                        @if($expenseChange > 0)
                            <p class="text-sm text-red-600">+{{ number_format($expenseChange, 1) }}% dari bulan lalu</p>
                        @elseif($expenseChange < 0)
                            <p class="text-sm text-green-600">{{ number_format($expenseChange, 1) }}% dari bulan lalu</p>
                        @else
                            <p class="text-sm text-gray-500">Sama dengan bulan lalu</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Tren 6 Bulan Terakhir</h3>
                    <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Lihat Laporan</a>
                </div>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <!-- Top Categories -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Top Kategori Pengeluaran</h3>
                    <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Lihat Detail</a>
                </div>
                <div class="space-y-4">
                    @forelse($topExpenseCategories as $category)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $category['color'] }}"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $category['name'] }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($category['amount'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada data pengeluaran bulan ini</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h3>
                    <a href="{{ route('transactions.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Lihat Semua</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentTransactions as $transaction)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                    {{ $transaction->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    <i data-lucide="{{ $transaction->type === 'income' ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-5 h-5"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $transaction->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->category->name ?? 'Uncategorized' }} • {{ $transaction->account->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada transaksi</p>
                    @endforelse
                </div>
            </div>

            <!-- Budget Progress -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Progress Budget</h3>
                    <a href="{{ route('budgets.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Kelola</a>
                </div>
                <div class="space-y-4">
                    @forelse($budgets as $budget)
                        @php
                            $spent = $budget->category->transactions()
                                ->where('type', 'expense')
                                ->whereBetween('transaction_date', [$budget->period_start, $budget->period_end])
                                ->sum('amount');
                            $percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ $budget->category->name }}</span>
                                <span class="text-sm text-gray-600">Rp {{ number_format($spent, 0, ',', '.') }} / Rp {{ number_format($budget->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                     style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 1) }}% terpakai</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada budget aktif</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        return;
    }

    // Monthly Chart with sample data if no data provided
    let monthlyData = @json($monthlyData ?? []);
    
    // Provide sample data if empty
    if (!monthlyData || monthlyData.length === 0) {
        monthlyData = [
            { month: 'Jan 2025', income: 8000000, expense: 6000000 },
            { month: 'Feb 2025', income: 8500000, expense: 6200000 },
            { month: 'Mar 2025', income: 9000000, expense: 6800000 },
            { month: 'Apr 2025', income: 8200000, expense: 6500000 },
            { month: 'Mei 2025', income: 8800000, expense: 7000000 },
            { month: 'Jun 2025', income: 9200000, expense: 6900000 }
        ];
    }

    const ctx = document.getElementById('monthlyChart');
    if (!ctx) {
        console.error('Chart canvas not found');
        return;
    }

    const chartCtx = ctx.getContext('2d');
    
    new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: monthlyData.map(item => item.income || 0),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                },
                {
                    label: 'Pengeluaran',
                    data: monthlyData.map(item => item.expense || 0),
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#EF4444',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                            }
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                line: {
                    borderJoinStyle: 'round'
                }
            }
        }
    });
});
</script>
@endsection
