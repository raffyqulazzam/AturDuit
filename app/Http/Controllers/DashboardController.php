<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $userId = $user->getKey();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Basic stats (user-specific)
        $totalBalance = Account::where('user_id', $userId)
            ->sum('balance');
            
        // Current month income and expense (user-specific)
        $monthlyIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');
            
        $monthlyExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');
            
        // Previous month for comparison (user-specific)
        $previousMonthIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $currentMonth - 1)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');
            
        $previousMonthExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth - 1)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');
            
        // Calculate percentage changes
        $incomeChange = $previousMonthIncome > 0 
            ? (($monthlyIncome - $previousMonthIncome) / $previousMonthIncome) * 100 
            : 0;
            
        $expenseChange = $previousMonthExpense > 0 
            ? (($monthlyExpense - $previousMonthExpense) / $previousMonthExpense) * 100 
            : 0;
            
        // Chart data (user-specific)
        $monthlyData = $this->getMonthlyData($userId);
        
        // Top categories by spending this month (user-specific)
        $topExpenseCategories = $this->getTopExpenseCategories($userId, $currentMonth);
        
        // Recent transactions (user-specific)
        $recentTransactions = Transaction::where('user_id', $userId)
            ->with(['category', 'account'])
            ->orderBy('transaction_date', 'desc')
            ->take(5)
            ->get();
            
        // Budget progress (user-specific)
        $budgets = Budget::where('user_id', $userId)
            ->where('period_start', '<=', now())
            ->where('period_end', '>=', now())
            ->with('category')
            ->get();
            
        return view('dashboard', compact(
            'totalBalance',
            'monthlyIncome', 
            'monthlyExpense',
            'incomeChange',
            'expenseChange',
            'monthlyData',
            'topExpenseCategories',
            'recentTransactions',
            'budgets'
        ));
    }
    
    /**
     * Get quick stats for API/AJAX requests
     */
    public function getQuickStats()
    {
        $userId = auth()->id();
        
        // Total balance from all accounts (user-specific)
        $totalBalance = Account::where('user_id', $userId)->sum('balance');
        
        // Current month income and expense (user-specific)
        $currentMonth = now()->format('Y-m');
        $monthlyIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$currentMonth])
            ->sum('amount');
            
        $monthlyExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$currentMonth])
            ->sum('amount');
            
        $netIncome = $monthlyIncome - $monthlyExpense;
        
        return response()->json([
            'total_balance' => $totalBalance,
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'net_income' => $netIncome,
            'formatted' => [
                'total_balance' => 'Rp. ' . number_format($totalBalance, 0, ',', '.'),
                'monthly_income' => 'Rp. ' . number_format($monthlyIncome, 0, ',', '.'),
                'monthly_expense' => 'Rp. ' . number_format($monthlyExpense, 0, ',', '.'),
                'net_income' => ($netIncome >= 0 ? '+' : '') . 'Rp. ' . number_format($netIncome, 0, ',', '.')
            ]
        ]);
    }
    
    private function getMonthlyData($userId)
    {
        $months = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            
            $income = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount');
                
            $expense = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount');
                
            $months[] = [
                'month' => $month->format('M Y'),
                'income' => (float) $income,
                'expense' => (float) $expense
            ];
        }
        
        return $months;
    }
    
    private function getTopExpenseCategories($userId, $currentMonth)
    {
        return Category::where('user_id', $userId) // Filter by user first
            ->withSum(['transactions' => function ($query) use ($userId, $currentMonth) {
                $query->where('type', 'expense')
                      ->where('user_id', $userId)
                      ->whereMonth('transaction_date', $currentMonth)
                      ->whereYear('transaction_date', now()->year);
            }], 'amount')
            ->where('type', 'expense')
            ->orderBy('transactions_sum_amount', 'desc')
            ->take(5)
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'amount' => (float) ($category->transactions_sum_amount ?? 0),
                    'color' => $category->color
                ];
            });
    }
}
