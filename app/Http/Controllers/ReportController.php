<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display financial reports.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        // Income vs Expense Report
        $incomeExpenseData = $this->getIncomeExpenseData($period, $year, $month);
        
        // Category Breakdown Report
        $categoryData = $this->getCategoryBreakdown($period, $year, $month);
        
        // Monthly Trends
        $monthlyTrends = $this->getMonthlyTrends($year);
        
        // Account Balance Report
        $accountBalances = $this->getAccountBalances();
        
        // Budget Performance
        $budgetPerformance = $this->getBudgetPerformance($year, $month);
        
        // Additional data for views
        $monthlyAnalysis = $this->getMonthlyAnalysis($year);
        $topExpenseCategories = $this->getTopExpenseCategories($period, $year, $month);
        $budgetAnalysis = $this->getBudgetAnalysisData($year, $month);
        $insights = $this->getFinancialInsights($incomeExpenseData);
        $monthlyData = $this->getChartMonthlyData($year);
        $categoryBreakdown = $this->getCategoryChartData($period, $year, $month);

        return view('reports.index', compact(
            'incomeExpenseData',
            'categoryData', 
            'monthlyTrends',
            'accountBalances',
            'budgetPerformance',
            'monthlyAnalysis',
            'topExpenseCategories',
            'budgetAnalysis',
            'insights',
            'monthlyData',
            'categoryBreakdown',
            'period',
            'year',
            'month'
        ));
    }

    /**
     * Export report data.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $period = $request->get('period', 'monthly');
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));

        $transactions = Transaction::where('user_id', Auth::id())
            ->with(['account', 'category'])
            ->when($period === 'monthly', function($query) use ($year, $month) {
                return $query->whereYear('transaction_date', $year)
                           ->whereMonth('transaction_date', $month);
            })
            ->when($period === 'yearly', function($query) use ($year) {
                return $query->whereYear('transaction_date', $year);
            })
            ->orderBy('transaction_date', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportCSV($transactions, $period, $year, $month);
        }

        return $this->exportPDF($transactions, $period, $year, $month);
    }

    private function getIncomeExpenseData($period, $year, $month)
    {
        $query = Transaction::where('user_id', Auth::id());

        if ($period === 'monthly') {
            $query->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $month);
        } else {
            $query->whereYear('transaction_date', $year);
        }

        $income = $query->clone()->where('type', 'income')->sum('amount');
        $expense = $query->clone()->where('type', 'expense')->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
            'savings_rate' => $income > 0 ? (($income - $expense) / $income) * 100 : 0
        ];
    }

    private function getCategoryBreakdown($period, $year, $month)
    {
        $query = Transaction::where('user_id', Auth::id())
            ->with('category');

        if ($period === 'monthly') {
            $query->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $month);
        } else {
            $query->whereYear('transaction_date', $year);
        }

        $categories = $query->select('category_id', 'type', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id', 'type')
            ->get()
            ->groupBy('type');

        return $categories;
    }

    private function getMonthlyTrends($year)
    {
        $trends = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $income = Transaction::where('user_id', Auth::id())
                ->where('type', 'income')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');

            $expense = Transaction::where('user_id', Auth::id())
                ->where('type', 'expense')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');

            $trends[] = [
                'month' => Carbon::create($year, $month)->format('M'),
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense
            ];
        }

        return $trends;
    }

    private function getAccountBalances()
    {
        return Account::where('user_id', Auth::id())
            ->select('name', 'type', 'balance')
            ->get();
    }

    private function getBudgetPerformance($year, $month)
    {
        return Budget::where('user_id', Auth::id())
            ->whereMonth('period_start', '<=', $month)
            ->whereMonth('period_end', '>=', $month)
            ->whereYear('period_start', $year)
            ->with('category')
            ->get()
            ->map(function($budget) use ($year, $month) {
                $spent = Transaction::where('user_id', Auth::id())
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month)
                    ->sum('amount');

                $budget->actual_spent = $spent;
                $budget->remaining = $budget->amount - $spent;
                $budget->percentage = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;

                return $budget;
            });
    }

    private function exportCSV($transactions, $period, $year, $month)
    {
        $filename = "aturduit_report_{$period}_{$year}_{$month}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Tanggal', 'Jenis', 'Kategori', 'Akun', 'Jumlah', 'Deskripsi']);
            
            // Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date->format('Y-m-d'),
                    ucfirst($transaction->type),
                    $transaction->category->name ?? '',
                    $transaction->account->name ?? '',
                    $transaction->amount,
                    $transaction->description
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
    private function getMonthlyAnalysis($year)
    {
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $income = Transaction::where('user_id', Auth::id())
                ->where('type', 'income')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');
                
            $expense = Transaction::where('user_id', Auth::id())
                ->where('type', 'expense')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');
                
            $net = $income - $expense;
            $savings_rate = $income > 0 ? (($income - $expense) / $income) * 100 : 0;
            
            $monthlyData[] = [
                'month' => date('M Y', mktime(0, 0, 0, $month, 1, $year)),
                'income' => $income,
                'expense' => $expense,
                'net' => $net,
                'savings_rate' => $savings_rate
            ];
        }
        
        return collect($monthlyData)->filter(function($month) {
            return $month['income'] > 0 || $month['expense'] > 0;
        });
    }
    
    private function getTopExpenseCategories($period, $year, $month)
    {
        $query = Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->with('category');

        if ($period === 'monthly') {
            $query->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $month);
        } else {
            $query->whereYear('transaction_date', $year);
        }

        return $query->select('category_id', DB::raw('SUM(amount) as amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('category_id')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->category->name ?? 'Uncategorized',
                    'amount' => $item->amount,
                    'count' => $item->count
                ];
            });
    }
    
    private function getBudgetAnalysisData($year, $month)
    {
        return collect([]); // Return empty collection for now
    }
    
    private function getFinancialInsights($incomeExpenseData)
    {
        $insights = [];
        
        if ($incomeExpenseData['savings_rate'] >= 20) {
            $insights[] = [
                'type' => 'green',
                'icon' => 'trending-up',
                'title' => 'Excellent Savings',
                'message' => 'Your savings rate is excellent! Keep up the good work.'
            ];
        } elseif ($incomeExpenseData['savings_rate'] >= 10) {
            $insights[] = [
                'type' => 'yellow',
                'icon' => 'alert-circle',
                'title' => 'Good Progress',
                'message' => 'You\'re saving well, try to reach 20% savings rate.'
            ];
        } else {
            $insights[] = [
                'type' => 'red',
                'icon' => 'alert-triangle',
                'title' => 'Improve Savings',
                'message' => 'Consider increasing your savings rate to at least 10%.'
            ];
        }
        
        return $insights;
    }
    
    private function getChartMonthlyData($year)
    {
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $income = Transaction::where('user_id', Auth::id())
                ->where('type', 'income')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');
                
            $expense = Transaction::where('user_id', Auth::id())
                ->where('type', 'expense')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->sum('amount');
                
            $monthlyData[] = [
                'month' => date('M', mktime(0, 0, 0, $month, 1, $year)),
                'income' => floatval($income),
                'expense' => floatval($expense)
            ];
        }
        
        return $monthlyData;
    }
    
    private function getCategoryChartData($period, $year, $month)
    {
        $query = Transaction::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->with('category');

        if ($period === 'monthly') {
            $query->whereYear('transaction_date', $year)
                  ->whereMonth('transaction_date', $month);
        } else {
            $query->whereYear('transaction_date', $year);
        }

        $data = $query->select('category_id', DB::raw('SUM(amount) as amount'))
            ->groupBy('category_id')
            ->orderBy('amount', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->category->name ?? 'Uncategorized',
                    'amount' => floatval($item->amount)
                ];
            });

        // Add fallback data if empty
        if ($data->isEmpty()) {
            return collect([
                ['name' => 'Belum ada data', 'amount' => 0]
            ]);
        }

        return $data;
    }

    private function exportPDF($transactions, $period, $year, $month)
    {
        // For now, return JSON. In production, you'd use a PDF library like DomPDF
        $filename = "aturduit_report_{$period}_{$year}_{$month}.json";
        
        return response()->json($transactions, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
