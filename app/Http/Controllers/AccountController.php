<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->with('accountType')
            ->withSum('transactions', 'amount')
            ->get();
            
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $accountTypes = AccountType::where('user_id', Auth::id())
                                  ->where('is_active', true)
                                  ->orderBy('name')
                                  ->get();
        
        return view('accounts.create', compact('accountTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'description' => 'nullable|string'
        ]);

        // Verify the account type belongs to the authenticated user
        $accountType = AccountType::where('id', $request->account_type_id)
                                 ->where('user_id', Auth::id())
                                 ->first();
        
        if (!$accountType) {
            return back()->withErrors(['account_type_id' => 'Jenis akun tidak valid.']);
        }

        Account::create([
            'user_id' => Auth::id(),
            'name' => $request->get('name'),
            'account_type_id' => $request->get('account_type_id'),
            'type' => 'bank', // Keep for backward compatibility or set a default
            'balance' => 0, // Set default balance to 0
            'description' => $request->get('description')
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil ditambahkan!');
    }

    public function show(Account $account)
    {
        if ($account->getAttribute('user_id') !== Auth::id()) {
            abort(403);
        }
        
        $transactions = Transaction::where('account_id', $account->getKey())
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);
            
        // Calculate statistics for this account
        $totalIncome = $account->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $account->transactions()->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;
        
        return view('accounts.show', compact('account', 'transactions', 'totalIncome', 'totalExpense', 'netBalance'));
    }

    public function edit(Account $account)
    {
        if ($account->getAttribute('user_id') !== Auth::id()) {
            abort(403);
        }
        
        $accountTypes = AccountType::where('user_id', Auth::id())
                                  ->where('is_active', true)
                                  ->orderBy('name')
                                  ->get();
        
        return view('accounts.edit', compact('account', 'accountTypes'));
    }

    public function update(Request $request, Account $account)
    {
        if ($account->getAttribute('user_id') !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'description' => 'nullable|string'
        ]);

        // Verify the account type belongs to the authenticated user
        $accountType = AccountType::where('id', $request->account_type_id)
                                 ->where('user_id', Auth::id())
                                 ->first();
        
        if (!$accountType) {
            return back()->withErrors(['account_type_id' => 'Jenis akun tidak valid.']);
        }

        $account->update([
            'name' => $request->get('name'),
            'account_type_id' => $request->get('account_type_id'),
            'description' => $request->get('description')
        ]);

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil diperbarui!');
    }

    public function destroy(Account $account)
    {
        if ($account->getAttribute('user_id') !== Auth::id()) {
            abort(403);
        }
        
        // Check if account has transactions
        if ($account->transactions()->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'Tidak dapat menghapus akun yang memiliki transaksi!');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Akun berhasil dihapus!');
    }
}
