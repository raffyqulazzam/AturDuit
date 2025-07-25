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
        $accountTypes = AccountType::where('user_id', Auth::id())
            ->withCount(['userAccounts'])
            ->orderBy('name')
            ->paginate(12);
            
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
        
        $accountType->loadCount('userAccounts');
        
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