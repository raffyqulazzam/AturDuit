<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $accounts = Auth::user()->accounts;
        
        // Get system information
        $systemInfo = [
            'total_transactions' => $user->transactions()->count(),
            'total_categories' => $user->transactions()->distinct('category_id')->count('category_id'),
            'total_accounts' => $user->accounts()->count(),
        ];
        
        return view('settings.index', compact('user', 'accounts', 'systemInfo'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->update($request->only(['name', 'email']));

        return redirect()->route('settings.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('settings.index')->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Update application preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|in:IDR,USD,EUR',
            'date_format' => 'required|string|in:Y-m-d,d/m/Y,m/d/Y',
            'timezone' => 'required|string',
        ]);

        // Store preferences in session or user preferences table
        session([
            'currency' => $request->input('currency'),
            'date_format' => $request->input('date_format'),
            'timezone' => $request->input('timezone'),
        ]);

        return redirect()->route('settings.index')->with('success', 'Preferensi berhasil disimpan!');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'budget_alerts' => 'boolean',
            'large_transaction_alerts' => 'boolean',
            'savings_goal_alerts' => 'boolean',
            'low_balance_alerts' => 'boolean',
        ]);

        // Update user notification preferences
        $user->update([
            'budget_alerts' => $request->boolean('budget_alerts'),
            'large_transaction_alerts' => $request->boolean('large_transaction_alerts'),
            'savings_goal_alerts' => $request->boolean('savings_goal_alerts'),
            'low_balance_alerts' => $request->boolean('low_balance_alerts'),
        ]);

        return redirect()->route('settings.index')->with('success', 'Pengaturan notifikasi berhasil disimpan!');
    }

    /**
     * Export user data.
     */
    public function exportData()
    {
        $user = Auth::user();
        
        // Load user data with relationships
        $userData = $user->load(['accounts', 'transactions', 'budgets', 'savingsGoals']);
        
        $filename = 'aturduit_data_' . date('Y-m-d') . '.json';
        
        return response()->json($userData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Show delete account confirmation.
     */
    public function showDeleteAccount()
    {
        return view('settings.delete-account');
    }

    /**
     * Delete user account.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
            'confirmation' => 'required|in:DELETE_MY_ACCOUNT',
        ]);

        $user = Auth::user();
        
        // Delete user data
        $user->accounts()->delete();
        $user->transactions()->delete();
        $user->budgets()->delete();
        $user->savingsGoals()->delete();
        
        // Logout and delete user
        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Akun berhasil dihapus.');
    }
}
