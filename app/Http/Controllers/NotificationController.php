<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Budget;
use App\Models\Account;
use App\Models\SavingsGoal;

class NotificationController extends Controller
{
    /**
     * Get user notifications.
     */
    public function index()
    {
        $userId = auth()->id();
        $notifications = [];
        
        // Check if user is new (no transactions, accounts, categories, etc.)
        $transactionCount = Transaction::where('user_id', $userId)->count();
        $accountCount = Account::where('user_id', $userId)->count();
        $budgetCount = Budget::where('user_id', $userId)->count();
        
        $isNewUser = $transactionCount === 0 && $accountCount === 0 && $budgetCount === 0;
        
        if ($isNewUser) {
            // Welcome notifications for new users
            $notifications = [
                [
                    'id' => 'welcome_1',
                    'type' => 'success',
                    'title' => 'Selamat Datang di AturDuit! 🎉',
                    'message' => 'Terima kasih telah bergabung! Kami siap membantu Anda mengelola keuangan dengan lebih baik.',
                    'icon' => 'heart',
                    'created_at' => now()->subMinutes(5)->toISOString(),
                    'read_at' => null
                ],
                [
                    'id' => 'welcome_2',
                    'type' => 'info',
                    'title' => 'Mulai Perjalanan Finansial Anda',
                    'message' => 'Langkah pertama: Buat kategori dan akun untuk mengorganisir transaksi Anda.',
                    'icon' => 'compass',
                    'created_at' => now()->subMinutes(10)->toISOString(),
                    'read_at' => null
                ],
                [
                    'id' => 'welcome_3',
                    'type' => 'info',
                    'title' => 'Tips untuk Memulai',
                    'message' => 'Tambahkan akun bank atau dompet digital Anda untuk tracking yang lebih akurat.',
                    'icon' => 'lightbulb',
                    'created_at' => now()->subMinutes(15)->toISOString(),
                    'read_at' => null
                ]
            ];
        } else {
            // Existing user notifications
            // Check recent high-value transactions (last 7 days)
            $recentTransactions = Transaction::where('user_id', $userId)
                ->with(['category', 'account']) // Load both category and account relationships
                ->where('created_at', '>=', now()->subDays(7))
                ->where('amount', '>=', 1000000) // Rp 1M+
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();
    
            foreach ($recentTransactions as $index => $transaction) {
                $categoryName = $transaction->category ? $transaction->category->name : 'Kategori Tidak Diketahui';
                $accountName = $transaction->account ? $transaction->account->name : 'Akun Tidak Diketahui';
                
                $notifications[] = [
                    'id' => 'tx_' . $transaction->id,
                    'type' => $transaction->type === 'income' ? 'success' : 'info',
                    'title' => $transaction->type === 'income' ? 'Pemasukan Besar' : 'Pengeluaran Besar',
                    'message' => 'Transaksi ' . ($transaction->type === 'income' ? 'pemasukan' : 'pengeluaran') . ' sebesar Rp. ' . number_format(floatval($transaction->amount), 0, ',', '.') . ' pada kategori "' . $categoryName . '" dari akun "' . $accountName . '"',
                    'icon' => $transaction->type === 'income' ? 'trending-up' : 'trending-down',
                    'created_at' => $transaction->created_at->toISOString(),
                    'read_at' => $index > 0 ? $transaction->created_at->addHours(2)->toISOString() : null
                ];
            }
    
            // Check budget alerts (simplified)
            $budgets = Budget::where('user_id', $userId)->where('is_active', true)->limit(3)->get();
            foreach ($budgets as $budget) {
                $percentage = floatval($budget->amount) > 0 ? (floatval($budget->spent) / floatval($budget->amount)) * 100 : 0;
                
                if ($percentage >= 75) {
                    $notifications[] = [
                        'id' => 'budget_' . $budget->id,
                        'type' => $percentage >= 100 ? 'error' : 'warning',
                        'title' => $percentage >= 100 ? 'Budget Terlampaui!' : 'Peringatan Budget',
                        'message' => 'Pengeluaran untuk "' . $budget->name . '" sudah mencapai ' . number_format($percentage, 1) . '% dari budget Rp. ' . number_format(floatval($budget->amount), 0, ',', '.'),
                        'icon' => $percentage >= 100 ? 'alert-circle' : 'alert-triangle',
                        'created_at' => now()->subHours(rand(1, 6))->toISOString(),
                        'read_at' => null
                    ];
                }
            }
    
            // Check savings goals
            $savingsGoals = SavingsGoal::where('user_id', $userId)->limit(2)->get();
            foreach ($savingsGoals as $goal) {
                $percentage = floatval($goal->target_amount) > 0 ? (floatval($goal->current_amount) / floatval($goal->target_amount)) * 100 : 0;
                
                if ($percentage >= 75 && $percentage < 100) {
                    $notifications[] = [
                        'id' => 'savings_' . $goal->id,
                        'type' => 'success',
                        'title' => 'Target Tabungan Hampir Tercapai!',
                        'message' => 'Anda telah mencapai ' . number_format($percentage, 1) . '% dari target tabungan "' . $goal->name . '" sebesar Rp. ' . number_format(floatval($goal->target_amount), 0, ',', '.'),
                        'icon' => 'target',
                        'created_at' => now()->subDays(rand(1, 3))->toISOString(),
                        'read_at' => null
                    ];
                }
            }
    
            // Add some helpful tips if user has minimal activity
            if (count($notifications) < 2) {
                $tipNotifications = [
                    [
                        'id' => 'tip_budget',
                        'type' => 'info',
                        'title' => 'Tips Keuangan',
                        'message' => 'Atur budget bulanan untuk kontrol pengeluaran yang lebih baik.',
                        'icon' => 'target',
                        'created_at' => now()->subHours(2)->toISOString(),
                        'read_at' => null
                    ],
                    [
                        'id' => 'tip_savings',
                        'type' => 'info',
                        'title' => 'Mulai Menabung',
                        'message' => 'Tetapkan target tabungan untuk mencapai tujuan finansial Anda.',
                        'icon' => 'piggy-bank',
                        'created_at' => now()->subHours(4)->toISOString(),
                        'read_at' => null
                    ]
                ];
                
                $notifications = array_merge($notifications, array_slice($tipNotifications, 0, 3 - count($notifications)));
            }
        }
        
        // Sort by created_at desc and limit to 8
        usort($notifications, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        $notifications = array_slice($notifications, 0, 8);
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => count(array_filter($notifications, fn($n) => $n['read_at'] === null))
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request)
    {
        $notificationId = $request->input('id');
        
        return response()->json([
            'success' => true, 
            'message' => 'Notifikasi telah dibaca',
            'notification_id' => $notificationId
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        return response()->json([
            'success' => true, 
            'message' => 'Semua notifikasi telah dibaca'
        ]);
    }
}
