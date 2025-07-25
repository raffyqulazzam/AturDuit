<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())
            ->withSum('transactions', 'amount')
            ->get();
            
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,ewallet,investment',
            'balance' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        Account::create([
            'user_id' => Auth::id(),
            'name' => $request->get('name'),
            'type' => $request->get('type'),
            'balance' => $request->get('balance'),
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
        
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        if ($account->getAttribute('user_id') !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,ewallet,investment',
            'description' => 'nullable|string'
        ]);

        $account->update([
            'name' => $request->get('name'),
            'type' => $request->get('type'),
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
