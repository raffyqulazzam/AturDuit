<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create main admin user
        $user = User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Create sample accounts for the user
        $accounts = [
            [
                'user_id' => $user->id,
                'name' => 'BCA Tabungan Utama',
                'type' => 'savings',
                'balance' => 15000000,
                'description' => 'Rekening tabungan utama di Bank BCA'
            ],
            [
                'user_id' => $user->id,
                'name' => 'Dana E-Wallet',
                'type' => 'cash',
                'balance' => 2500000,
                'description' => 'Saldo Dana untuk transaksi digital'
            ],
            [
                'user_id' => $user->id,
                'name' => 'Mandiri Giro',
                'type' => 'checking',
                'balance' => 8500000,
                'description' => 'Rekening giro untuk transaksi bisnis'
            ],
            [
                'user_id' => $user->id,
                'name' => 'Cash Wallet',
                'type' => 'cash',
                'balance' => 1200000,
                'description' => 'Uang tunai harian'
            ]
        ];

        foreach ($accounts as $accountData) {
            Account::create($accountData);
        }

        // Get accounts and categories for transactions
        $userAccounts = Account::where('user_id', $user->id)->get();
        $categories = Category::all();

        // Create sample transactions
        if ($userAccounts->count() > 0 && $categories->count() > 0) {
            $sampleTransactions = [
                // Income transactions
                [
                    'user_id' => $user->id,
                    'account_id' => $userAccounts->first()->id,
                    'category_id' => $categories->where('name', 'Gaji')->first()->id ?? 1,
                    'type' => 'income',
                    'amount' => 8500000,
                    'description' => 'Gaji bulan Juli 2025',
                    'transaction_date' => '2025-07-01',
                ],
                [
                    'user_id' => $user->id,
                    'account_id' => $userAccounts->first()->id,
                    'category_id' => $categories->where('name', 'Bonus')->first()->id ?? 2,
                    'type' => 'income',
                    'amount' => 2000000,
                    'description' => 'Bonus kinerja Q2',
                    'transaction_date' => '2025-07-05',
                ],
                
                // Expense transactions
                [
                    'user_id' => $user->id,
                    'account_id' => $userAccounts->first()->id,
                    'category_id' => $categories->where('name', 'Makanan')->first()->id ?? 6,
                    'type' => 'expense',
                    'amount' => 450000,
                    'description' => 'Groceries dan bahan makanan',
                    'transaction_date' => '2025-07-03',
                ],
                [
                    'user_id' => $user->id,
                    'account_id' => $userAccounts->skip(1)->first()->id ?? $userAccounts->first()->id,
                    'category_id' => $categories->where('name', 'Transportasi')->first()->id ?? 7,
                    'type' => 'expense',
                    'amount' => 150000,
                    'description' => 'Bensin motor',
                    'transaction_date' => '2025-07-04',
                ],
                [
                    'user_id' => $user->id,
                    'account_id' => $userAccounts->first()->id,
                    'category_id' => $categories->where('name', 'Hiburan')->first()->id ?? 10,
                    'type' => 'expense',
                    'amount' => 250000,
                    'description' => 'Nonton bioskop dan makan',
                    'transaction_date' => '2025-07-06',
                ],
                [
                    'user_id' => $user->id,
                    'account_id' => $userAccounts->first()->id,
                    'category_id' => $categories->where('name', 'Belanja')->first()->id ?? 8,
                    'type' => 'expense',
                    'amount' => 750000,
                    'description' => 'Baju dan sepatu',
                    'transaction_date' => '2025-07-07',
                ],
            ];

            foreach ($sampleTransactions as $transaction) {
                Transaction::create($transaction);
            }
        }

        // Create sample savings goals
        $savingsGoals = [
            [
                'user_id' => $user->id,
                'name' => 'Liburan ke Bali',
                'description' => 'Target tabungan untuk liburan keluarga ke Bali tahun ini',
                'target_amount' => 25000000,
                'current_amount' => 12500000,
                'target_date' => '2025-12-31'
            ],
            [
                'user_id' => $user->id,
                'name' => 'Emergency Fund',
                'description' => 'Dana darurat untuk 6 bulan pengeluaran',
                'target_amount' => 50000000,
                'current_amount' => 18000000,
                'target_date' => '2026-06-30'
            ],
            [
                'user_id' => $user->id,
                'name' => 'Laptop Baru',
                'description' => 'Tabungan untuk membeli laptop MacBook Pro',
                'target_amount' => 35000000,
                'current_amount' => 22000000,
                'target_date' => '2025-09-15'
            ]
        ];

        foreach ($savingsGoals as $goalData) {
            SavingsGoal::create($goalData);
        }

        $this->command->info('✓ User admin@gmail.com created successfully with sample data!');
    }
}
