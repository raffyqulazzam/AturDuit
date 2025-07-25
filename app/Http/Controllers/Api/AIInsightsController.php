<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AIInsightsController extends Controller
{
    public function getSpendingInsights(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = $user->getKey();
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        // Analisis spending pattern
        $insights = [
            'spending_trends' => $this->analyzeSpendingTrends($userId),
            'category_analysis' => $this->analyzeCategorySpending($userId),
            'budget_recommendations' => $this->generateBudgetRecommendations($userId),
            'savings_predictions' => $this->predictSavings($userId),
            'financial_health_score' => $this->calculateFinancialHealthScore($userId),
            'recommendations' => $this->generateRecommendations($userId)
        ];

        return response()->json([
            'success' => true,
            'data' => $insights
        ]);
    }

    private function analyzeSpendingTrends($userId)
    {
        // Analisis tren pengeluaran 6 bulan terakhir
        $monthlySpending = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $spending = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');
            
            $monthlySpending[] = [
                'month' => $date->format('M Y'),
                'amount' => (float) $spending
            ];
        }

        // Calculate trend
        $amounts = array_column($monthlySpending, 'amount');
        $trend = $this->calculateTrend($amounts);
        
        return [
            'monthly_data' => $monthlySpending,
            'trend' => $trend,
            'average_monthly' => array_sum($amounts) / max(count($amounts), 1),
            'highest_month' => count($amounts) > 0 ? max($amounts) : 0,
            'lowest_month' => count($amounts) > 0 ? min($amounts) : 0
        ];
    }

    private function analyzeCategorySpending($userId)
    {
        // Analisis per kategori bulan ini vs bulan lalu
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $categories = Category::where('type', 'expense')->get();
        $analysis = [];

        foreach ($categories as $category) {
            $currentSpending = Transaction::where('user_id', $userId)
                ->where('category_id', $category->getKey())
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $currentMonth->month)
                ->whereYear('transaction_date', $currentMonth->year)
                ->sum('amount');

            $previousSpending = Transaction::where('user_id', $userId)
                ->where('category_id', $category->getKey())
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $previousMonth->month)
                ->whereYear('transaction_date', $previousMonth->year)
                ->sum('amount');

            $change = $previousSpending > 0 ? (($currentSpending - $previousSpending) / $previousSpending) * 100 : 0;

            $analysis[] = [
                'category' => $category->getAttribute('name'),
                'current_spending' => (float) $currentSpending,
                'previous_spending' => (float) $previousSpending,
                'change_percentage' => round($change, 1),
                'trend' => $change > 10 ? 'increasing' : ($change < -10 ? 'decreasing' : 'stable')
            ];
        }

        return $analysis;
    }

    private function generateBudgetRecommendations($userId)
    {
        // Rekomendasi budget berdasarkan historical data
        $categories = Category::where('type', 'expense')->get();
        $recommendations = [];

        foreach ($categories as $category) {
            $avgSpending = Transaction::where('user_id', $userId)
                ->where('category_id', $category->getKey())
                ->where('type', 'expense')
                ->where('transaction_date', '>=', Carbon::now()->subMonths(6))
                ->avg('amount');

            if ($avgSpending > 0) {
                $recommendations[] = [
                    'category' => $category->getAttribute('name'),
                    'recommended_budget' => round($avgSpending * 1.2, 2), // 20% buffer
                    'confidence' => 'medium'
                ];
            }
        }

        return $recommendations;
    }

    private function predictSavings($userId)
    {
        // Prediksi savings berdasarkan tren income vs expense
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $income = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $expense = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $monthlyData[] = (float) ($income - $expense);
        }

        $avgMonthlySavings = count($monthlyData) > 0 ? array_sum($monthlyData) / count($monthlyData) : 0;
        $trend = $this->calculateTrend($monthlyData);

        return [
            'average_monthly_savings' => round($avgMonthlySavings, 2),
            'trend' => round($trend, 1),
            'predicted_next_month' => round($avgMonthlySavings * (1 + ($trend / 100)), 2),
            'predicted_next_6_months' => round($avgMonthlySavings * 6 * (1 + ($trend / 100)), 2)
        ];
    }

    private function calculateFinancialHealthScore($userId)
    {
        $currentMonth = Carbon::now();
        
        // Calculate income vs expense ratio
        $monthlyIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('transaction_date', $currentMonth->month)
            ->whereYear('transaction_date', $currentMonth->year)
            ->sum('amount');

        $monthlyExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $currentMonth->month)
            ->whereYear('transaction_date', $currentMonth->year)
            ->sum('amount');

        $savingsRate = $monthlyIncome > 0 ? (($monthlyIncome - $monthlyExpense) / $monthlyIncome) * 100 : 0;
        
        // Score components
        $scores = [
            'savings_rate' => min(100, max(0, $savingsRate * 2)), // 50% savings rate = 100 points
            'spending_consistency' => $this->calculateSpendingConsistency($userId),
            'budget_adherence' => $this->calculateBudgetAdherence($userId)
        ];

        $totalScore = array_sum($scores) / count($scores);

        return [
            'overall_score' => round($totalScore),
            'components' => $scores,
            'rating' => $this->getHealthRating($totalScore)
        ];
    }

    private function generateRecommendations($userId)
    {
        $recommendations = [];
        
        // Analyze spending patterns untuk recommendations
        $categoryAnalysis = $this->analyzeCategorySpending($userId);
        
        foreach ($categoryAnalysis as $category) {
            if ($category['trend'] === 'increasing' && $category['change_percentage'] > 20) {
                $recommendations[] = [
                    'type' => 'warning',
                    'title' => 'Pengeluaran ' . $category['category'] . ' Meningkat',
                    'description' => 'Pengeluaran untuk kategori ' . $category['category'] . ' meningkat ' . number_format($category['change_percentage'], 1) . '% dari bulan lalu.',
                    'action' => 'Pertimbangkan untuk membuat budget khusus untuk kategori ini.'
                ];
            }
        }

        // Add general recommendations
        $recommendations[] = [
            'type' => 'tip',
            'title' => 'Optimalisasi Pengeluaran',
            'description' => 'Gunakan fitur budget untuk mengontrol pengeluaran bulanan Anda.',
            'action' => 'Set budget untuk 3 kategori pengeluaran terbesar Anda.'
        ];

        return $recommendations;
    }

    private function calculateTrend($data)
    {
        if (count($data) < 2) return 0;
        
        $n = count($data);
        $sumX = $sumY = $sumXY = $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $data[$i];
            $sumXY += $i * $data[$i];
            $sumX2 += $i * $i;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $avgY = $sumY / $n;
        
        return $avgY != 0 ? ($slope / $avgY) * 100 : 0;
    }

    private function calculateSpendingConsistency($userId)
    {
        // Calculate variance in monthly spending
        $monthlySpending = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $spending = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');
            $monthlySpending[] = (float) $spending;
        }

        if (count($monthlySpending) === 0) {
            return 50; // Default score
        }

        $avg = array_sum($monthlySpending) / count($monthlySpending);
        $variance = 0;
        foreach ($monthlySpending as $spending) {
            $variance += pow($spending - $avg, 2);
        }
        $variance = $variance / count($monthlySpending);
        $stdDev = sqrt($variance);
        
        $cv = $avg > 0 ? ($stdDev / $avg) : 1;
        return max(0, 100 - ($cv * 100)); // Lower variance = higher score
    }

    private function calculateBudgetAdherence($user)
    {
        // This would calculate how well user sticks to budgets
        // For now, return a mock score
        return 75;
    }

    private function getHealthRating($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Fair';
        return 'Needs Improvement';
    }
}
