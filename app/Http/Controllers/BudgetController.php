<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Budget::where('user_id', Auth::id())
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return view('budgets.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);
                    if (!$category || $category->user_id !== Auth::id()) {
                        $fail('Kategori yang dipilih tidak valid.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:1',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'description' => 'nullable|string|max:500',
        ]);

        // Get category name for budget name
        $category = Category::find($request->category_id);
        $budgetName = 'Budget ' . $category->name . ' - ' . date('M Y', strtotime($request->period_start));

        // Determine period based on date range
        $startDate = new \DateTime($request->period_start);
        $endDate = new \DateTime($request->period_end);
        $interval = $startDate->diff($endDate);
        
        if ($interval->days <= 7) {
            $period = 'weekly';
        } elseif ($interval->days <= 31) {
            $period = 'monthly';
        } else {
            $period = 'yearly';
        }

        Budget::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'name' => $budgetName,
            'amount' => $request->amount,
            'period' => $period,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'description' => $request->description,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        return view('budgets.show', compact('budget'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::where('user_id', Auth::id())->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = Category::find($value);
                    if (!$category || $category->user_id !== Auth::id()) {
                        $fail('Kategori yang dipilih tidak valid.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:1',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'description' => 'nullable|string|max:500',
        ]);

        // Get category name for budget name
        $category = Category::find($request->category_id);
        $budgetName = 'Budget ' . $category->name . ' - ' . date('M Y', strtotime($request->period_start));

        // Determine period based on date range
        $startDate = new \DateTime($request->period_start);
        $endDate = new \DateTime($request->period_end);
        $interval = $startDate->diff($endDate);
        
        if ($interval->days <= 7) {
            $period = 'weekly';
        } elseif ($interval->days <= 31) {
            $period = 'monthly';
        } else {
            $period = 'yearly';
        }

        $budget->update([
            'category_id' => $request->category_id,
            'name' => $budgetName,
            'amount' => $request->amount,
            'period' => $period,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'description' => $request->description,
        ]);

        return redirect()->route('budgets.index')->with('success', 'Budget berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget berhasil dihapus!');
    }
}
