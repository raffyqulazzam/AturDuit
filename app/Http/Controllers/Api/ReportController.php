<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = $user->getKey();
        $period = $request->get('period', 'month');
        
        $query = Transaction::where('user_id', $userId);
        
        switch ($period) {
            case 'week':
                $query->whereBetween('transaction_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'year':
                $query->whereYear('transaction_date', now()->year);
                break;
            default: // month
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
                break;
        }
        
        $transactions = $query->with(['category'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function categoryChart(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = $user->getKey();
        $type = $request->get('type', 'expense'); // income or expense
        $period = $request->get('period', 'month'); // month, year
        
        $query = Transaction::where('user_id', $userId)
            ->where('type', $type)
            ->with('category');
            
        if ($period === 'month') {
            $query->whereMonth('transaction_date', now()->month)
                  ->whereYear('transaction_date', now()->year);
        } else {
            $query->whereYear('transaction_date', now()->year);
        }
        
        $data = $query->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name ?? 'Unknown',
                    'amount' => (float) $item->total,
                    'color' => $item->category->color ?? '#6B7280',
                    'percentage' => 0 // Will be calculated on frontend
                ];
            });
            
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function weeklyChart(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = $user->getKey();
        $weeks = $request->get('weeks', 4); // Default 4 weeks
        
        $data = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $income = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
                
            $expense = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
                
            $data[] = [
                'week' => 'Week ' . ($weeks - $i),
                'period' => $startDate->format('M d') . ' - ' . $endDate->format('M d'),
                'income' => (float) $income,
                'expense' => (float) $expense,
                'net' => (float) ($income - $expense)
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function monthlyChart(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = $user->getKey();
        $year = $request->get('year', now()->year);
        
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $income = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $i)
                ->sum('amount');
                
            $expense = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $i)
                ->sum('amount');
                
            $months[] = [
                'month' => date('M', mktime(0, 0, 0, $i, 1)),
                'income' => (float) $income,
                'expense' => (float) $expense,
                'balance' => (float) ($income - $expense)
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $months
        ]);
    }

    public function dailyChart(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = $user->getKey();
        $days = $request->get('days', 30); // Default 30 days
        
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $income = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereDate('transaction_date', $date)
                ->sum('amount');
                
            $expense = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereDate('transaction_date', $date)
                ->sum('amount');
                
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'date_formatted' => $date->format('M d'),
                'income' => (float) $income,
                'expense' => (float) $expense,
                'net' => (float) ($income - $expense)
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function summary(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = $user->getKey();
        $period = $request->get('period', 'month');
        
        $query = Transaction::where('user_id', $userId);
        
        switch ($period) {
            case 'week':
                $query->whereBetween('transaction_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'year':
                $query->whereYear('transaction_date', now()->year);
                break;
            default: // month
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
                break;
        }
        
        $income = $query->clone()->where('type', 'income')->sum('amount');
        $expense = $query->clone()->where('type', 'expense')->sum('amount');
        $transactionCount = $query->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'total_income' => (float) $income,
                'total_expense' => (float) $expense,
                'net_income' => (float) ($income - $expense),
                'transaction_count' => $transactionCount,
                'average_transaction' => $transactionCount > 0 ? (float) (($income + $expense) / $transactionCount) : 0
            ]
        ]);
    }
}
