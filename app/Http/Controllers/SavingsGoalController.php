<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavingsGoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $savingsGoals = SavingsGoal::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('savings-goals.index', compact('savingsGoals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('savings-goals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date|after:today',
            'priority' => 'required|in:low,medium,high',
        ]);

        SavingsGoal::create($request->only([
            'name', 'description', 'target_amount', 'current_amount', 'target_date', 'priority'
        ]) + ['user_id' => Auth::id()]);

        return redirect()->route('savings-goals.index')->with('success', 'Savings goal berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('savings-goals.show', compact('savingsGoal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('savings-goals.edit', compact('savingsGoal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date|after:today',
            'priority' => 'required|in:low,medium,high',
        ]);

        $savingsGoal->update($request->only([
            'name', 'description', 'target_amount', 'current_amount', 'target_date', 'priority'
        ]));

        return redirect()->route('savings-goals.index')->with('success', 'Savings goal berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SavingsGoal $savingsGoal)
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $savingsGoal->delete();

        return redirect()->route('savings-goals.index')->with('success', 'Savings goal berhasil dihapus!');
    }
}
