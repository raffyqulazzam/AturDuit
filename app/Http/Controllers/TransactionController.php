<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id())
            ->with(['category', 'account']);
        
        // Apply filters
        if ($request->filled('type') && $request->get('type') !== 'all') {
            $query->where('type', $request->get('type'));
        }
        
        if ($request->filled('category_id') && $request->get('category_id') !== 'all') {
            $query->where('category_id', $request->get('category_id'));
        }
        
        if ($request->filled('account_id') && $request->get('account_id') !== 'all') {
            $query->where('account_id', $request->get('account_id'));
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->get('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->get('date_to'));
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->paginate(15);
        
        // Calculate summary
        $allTransactions = Transaction::where('user_id', Auth::id());
        
        // Apply same filters to summary
        if ($request->filled('type') && $request->get('type') !== 'all') {
            $allTransactions->where('type', $request->get('type'));
        }
        
        if ($request->filled('category_id') && $request->get('category_id') !== 'all') {
            $allTransactions->where('category_id', $request->get('category_id'));
        }
        
        if ($request->filled('account_id') && $request->get('account_id') !== 'all') {
            $allTransactions->where('account_id', $request->get('account_id'));
        }
        
        if ($request->filled('date_from')) {
            $allTransactions->whereDate('transaction_date', '>=', $request->get('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $allTransactions->whereDate('transaction_date', '<=', $request->get('date_to'));
        }
        
        $summary = [
            'total_income' => $allTransactions->clone()->where('type', 'income')->sum('amount'),
            'total_expense' => $allTransactions->clone()->where('type', 'expense')->sum('amount'),
            'total_count' => $allTransactions->count()
        ];
        
        $categories = Category::where('user_id', Auth::id())->orderBy('name')->get();
        $accounts = Account::where('user_id', Auth::id())->get();
        
        return view('transactions.index', compact('transactions', 'categories', 'accounts', 'summary'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->orderBy('name')->get();
        $accounts = Account::where('user_id', Auth::id())->where('is_active', true)->get();
        
        return view('transactions.create', compact('categories', 'accounts'));
    }

    public function store(StoreTransactionRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'account_id' => $request->get('account_id'),
                'category_id' => $request->get('category_id'),
                'amount' => $request->get('amount'),
                'type' => $request->get('type'),
                'description' => $request->get('description'),
                'transaction_date' => $request->get('transaction_date'),
            ]);
            
            // Update account balance
            $account = Account::find($request->get('account_id'));
            if ($request->get('type') === 'income') {
                $account->increment('balance', (float) $request->get('amount'));
            } else {
                $account->decrement('balance', (float) $request->get('amount'));
            }
            
            // Log the transaction
            \Log::info('Transaction created', [
                'user_id' => Auth::id(),
                'transaction_id' => $transaction->getKey(),
                'amount' => $request->get('amount'),
                'type' => $request->get('type')
            ]);
            
            DB::commit();
            
            return redirect()->route('dashboard')
                ->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->getAttribute('user_id') !== Auth::id()) {
            abort(403);
        }
        
        return view('transactions.show', compact('transaction'));
    }
    
    public function edit(Transaction $transaction)
    {
        if ($transaction->getAttribute('user_id') !== Auth::id()) {
            abort(403);
        }
        
        $categories = Category::where('user_id', Auth::id())->orderBy('name')->get();
        $accounts = Account::where('user_id', Auth::id())->get();
        
        return view('transactions.edit', compact('transaction', 'categories', 'accounts'));
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction)
    {
        // Ensure user owns this transaction
        if ($transaction->getAttribute('user_id') !== Auth::id()) {
            abort(403, 'Unauthorized access to transaction');
        }

        try {
            DB::beginTransaction();

            $oldAmount = (float) $transaction->getAttribute('amount');
            $oldType = $transaction->getAttribute('type');
            $oldAccountId = $transaction->getAttribute('account_id');

            // Reverse old transaction effect on account balance
            $oldAccount = Account::find($oldAccountId);
            if ($oldType === 'income') {
                $oldAccount->decrement('balance', $oldAmount);
            } else {
                $oldAccount->increment('balance', $oldAmount);
            }

            // Update transaction
            $transaction->update($request->validated());

            // Apply new transaction effect on account balance
            $newAccount = Account::find($request->get('account_id'));
            if ($request->get('type') === 'income') {
                $newAccount->increment('balance', (float) $request->get('amount'));
            } else {
                $newAccount->decrement('balance', (float) $request->get('amount'));
            }

            DB::commit();

            Log::info('Transaction updated', [
                'user_id' => Auth::id(),
                'transaction_id' => $transaction->getKey(),
                'old_amount' => $oldAmount,
                'new_amount' => $request->get('amount')
            ]);

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Transaction update failed', [
                'user_id' => Auth::id(),
                'transaction_id' => $transaction->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal mengupdate transaksi. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function destroy(Transaction $transaction)
    {
        // Ensure user owns this transaction
        if ($transaction->getAttribute('user_id') !== Auth::id()) {
            abort(403, 'Unauthorized access to transaction');
        }

        try {
            DB::beginTransaction();

            // Reverse transaction effect on account balance
            $account = Account::find($transaction->getAttribute('account_id'));
            if ($transaction->getAttribute('type') === 'income') {
                $account->decrement('balance', (float) $transaction->getAttribute('amount'));
            } else {
                $account->increment('balance', (float) $transaction->getAttribute('amount'));
            }

            $transaction->delete();

            DB::commit();

            Log::info('Transaction deleted', [
                'user_id' => Auth::id(),
                'transaction_id' => $transaction->getKey(),
                'amount' => $transaction->getAttribute('amount')
            ]);

            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Transaction deletion failed', [
                'user_id' => Auth::id(),
                'transaction_id' => $transaction->getKey(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menghapus transaksi. Silakan coba lagi.');
        }
    }
}
