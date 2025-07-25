<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        
        $accountTypes = AccountType::where('user_id', $userId)
            ->orderBy('name')
            ->paginate(12);
        
        // Load counts and accounts manually
        foreach ($accountTypes as $accountType) {
            $accountType->user_accounts_count = $accountType->accounts()
                ->where('user_id', $userId)
                ->count();
                
            $accountType->load(['accounts' => function($query) use ($userId) {
                $query->select('id', 'account_type_id', 'name', 'balance', 'currency', 'is_active')
                      ->where('user_id', $userId)
                      ->where('is_active', true)
                      ->take(3);
            }]);
            
            // Set userAccounts as alias for accounts for blade compatibility
            $accountType->userAccounts = $accountType->accounts;
        }
        
        return view('account-types.index', compact('accountTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('account-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        AccountType::create($validated);

        return redirect()->route('account-types.index')
            ->with('success', 'Jenis akun berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AccountType $accountType)
    {
        if ($accountType->user_id !== Auth::id()) {
            abort(403);
        }
        
        $accountType->loadCount('userAccounts');
        
        return view('account-types.show', compact('accountType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountType $accountType)
    {
        if ($accountType->user_id !== Auth::id()) {
            abort(403);
        }
        
        $accountType->loadCount('userAccounts')
                   ->load(['userAccounts' => function($query) {
                       $query->select('id', 'account_type_id', 'name', 'balance', 'currency', 'is_active')
                             ->where('is_active', true)
                             ->take(5); // Load more for edit page
                   }]);
        
        return view('account-types.edit', compact('accountType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccountType $accountType)
    {
        if ($accountType->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $accountType->update($validated);

        return redirect()->route('account-types.index')
            ->with('success', 'Jenis akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccountType $accountType)
    {
        if ($accountType->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if any accounts are using this account type
        $accountsUsingType = $accountType->userAccounts()->count();
        
        if ($accountsUsingType > 0) {
            return redirect()->route('account-types.index')
                ->with('error', 'Tidak dapat menghapus jenis akun yang sedang digunakan oleh ' . $accountsUsingType . ' akun.');
        }

        $accountType->delete();

        return redirect()->route('account-types.index')
            ->with('success', 'Jenis akun berhasil dihapus.');
    }
}