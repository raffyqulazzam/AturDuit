@extends('layouts.app')

@section('title', 'Laporan - AturDuit')
@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Laporan Keuangan</h2>
                <p class="text-sm text-gray-600">Analisis mendalam tentang keuangan Anda</p>
            </div>
            
            <!-- Period Filter -->
            <div class="flex items-center space-x-2">
                <select id="periodFilter" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="6">6 Bulan Terakhir</option>
                    <option value="12">12 Bulan Terakhir</option>
                    <option value="24">24 Bulan Terakhir</option>
                </select>
                <button onclick="exportReport()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                    Export
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pemasukan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($incomeExpenseData['income'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-down" class="w-6 h-6 text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($incomeExpenseData['expense'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Net Income</p>
                        <p class="text-2xl font-bold {{ $incomeExpenseData['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($incomeExpenseData['net'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="percent" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Savings Rate</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($incomeExpenseData['savings_rate'], 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Income vs Expense Trend -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tren Pemasukan vs Pengeluaran</h3>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Breakdown Kategori</h3>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Analysis -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Analisis Bulanan</h3>
                <div class="space-y-4">
                    @foreach($monthlyAnalysis as $month)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $month['month'] }}</p>
                                <p class="text-sm text-gray-500">
                                    Net: Rp {{ number_format($month['net'], 0, ',', '.') }}
                                    ({{ number_format($month['savings_rate'], 1) }}%)
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-green-600">+Rp {{ number_format($month['income'], 0, ',', '.') }}</p>
                                <p class="text-sm text-red-600">-Rp {{ number_format($month['expense'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Categories -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Kategori Pengeluaran</h3>
                <div class="space-y-4">
                    @foreach($topExpenseCategories as $category)
                        @php
                            $percentage = $incomeExpenseData['expense'] > 0 ? ($category['amount'] / $incomeExpenseData['expense']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ $category['name'] }}</span>
                                <span class="text-sm text-gray-600">{{ number_format($percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-red-500" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Rp {{ number_format($category['amount'], 0, ',', '.') }}</span>
                                <span>{{ $category['count'] }} transaksi</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Budget vs Actual -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Budget vs Realisasi</h3>
            @if($budgetAnalysis->count() > 0)
                <div class="space-y-4">
                    @foreach($budgetAnalysis as $budget)
                        @php
                            $percentage = $budget['budget'] > 0 ? ($budget['actual'] / $budget['budget']) * 100 : 0;
                            $variance = $budget['actual'] - $budget['budget'];
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-gray-900">{{ $budget['category'] }}</h4>
                                <span class="text-sm {{ $variance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $variance > 0 ? '+' : '' }}Rp {{ number_format($variance, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Budget: Rp {{ number_format($budget['budget'], 0, ',', '.') }}</span>
                                    <span class="text-gray-600">Actual: Rp {{ number_format($budget['actual'], 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $percentage > 100 ? 'bg-red-500' : ($percentage > 90 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                         style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>{{ number_format($percentage, 1) }}% terpakai</span>
                                    <span class="{{ $variance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $variance > 0 ? 'Over' : 'Under' }} budget
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i data-lucide="bar-chart" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-gray-500">Belum ada data budget untuk dianalisis</p>
                </div>
            @endif
        </div>

        <!-- Insights -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Insights & Rekomendasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    @foreach($insights as $insight)
                        <div class="flex items-start space-x-3 p-3 bg-{{ $insight['type'] }}-50 rounded-lg border border-{{ $insight['type'] }}-200">
                            <div class="w-6 h-6 bg-{{ $insight['type'] }}-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="{{ $insight['icon'] }}" class="w-4 h-4 text-{{ $insight['type'] }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-{{ $insight['type'] }}-900">{{ $insight['title'] }}</p>
                                <p class="text-sm text-{{ $insight['type'] }}-700">{{ $insight['message'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900">Financial Health</h4>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Savings Rate</span>
                                <span class="font-medium">{{ number_format($incomeExpenseData['savings_rate'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $incomeExpenseData['savings_rate'] >= 20 ? 'bg-green-500' : ($incomeExpenseData['savings_rate'] >= 10 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                     style="width: {{ min($incomeExpenseData['savings_rate'] * 5, 100) }}%"></div>
                            </div>
                        </div>
                        
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-600">
                                @if($incomeExpenseData['savings_rate'] >= 20)
                                    🟢 Excellent savings rate! Keep it up!
                                @elseif($incomeExpenseData['savings_rate'] >= 10)
                                    🟡 Good savings rate, try to increase to 20%
                                @else
                                    🔴 Consider increasing your savings rate
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Charts initializing...');
    
    // Trend Chart
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const trendData = @json($monthlyData ?? []);
        console.log('Trend data:', trendData);
        
        if (trendData && trendData.length > 0) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendData.map(item => item.month || 'N/A'),
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: trendData.map(item => parseFloat(item.income) || 0),
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        },
                        {
                            label: 'Pengeluaran',
                            data: trendData.map(item => parseFloat(item.expense) || 0),
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#EF4444',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
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
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    } else {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                }
            });
            console.log('Trend chart created successfully');
        } else {
            // Show no data message
            trendCtx.parentElement.innerHTML = '<div class="chart-loading"><p>Tidak ada data untuk ditampilkan</p></div>';
            console.log('No trend data available');
        }
    }

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const categoryData = @json($categoryBreakdown ?? []);
        console.log('Category data:', categoryData);
        
        if (categoryData && categoryData.length > 0 && categoryData.some(item => parseFloat(item.amount) > 0)) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.name || 'N/A'),
                    datasets: [{
                        data: categoryData.map(item => parseFloat(item.amount) || 0),
                        backgroundColor: [
                            '#EF4444', '#F97316', '#EAB308', '#22C55E', '#3B82F6',
                            '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B', '#6366F1'
                        ],
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverBorderWidth: 4,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const dataset = data.datasets[0];
                                            const value = dataset.data[i];
                                            const total = dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            
                                            return {
                                                text: `${label} (${percentage}%)`,
                                                fillStyle: dataset.backgroundColor[i],
                                                hidden: isNaN(dataset.data[i]),
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return context.label + ': Rp ' + value.toLocaleString('id-ID') + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
            console.log('Category chart created successfully');
        } else {
            // Show no data message
            categoryCtx.parentElement.innerHTML = '<div class="chart-loading"><p>Tidak ada data kategori untuk ditampilkan</p></div>';
            console.log('No category data available');
        }
    }
});

function exportReport() {
    // Implementation for exporting report
    alert('Fitur export akan segera tersedia');
}

// Period filter change handler
const periodFilter = document.getElementById('periodFilter');
if (periodFilter) {
    periodFilter.addEventListener('change', function() {
        // Reload page with new period parameter
        const period = this.value;
        window.location.href = window.location.pathname + '?period=' + period;
    });
}
</script>
@endsection
