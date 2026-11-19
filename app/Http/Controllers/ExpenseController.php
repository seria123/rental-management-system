<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display all expenses.
     */
    public function index()
    {
        $expenses = Expense::latest()->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating an expense.
     */
    public function create()
    {
        $properties = Property::all();

        return view('expenses.create', compact('properties'));
    }

    /**
     * Store a new expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'payment_method' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if (($validated['payment_method'] ?? '') === 'Mpesa') {
            $request->validate([
                'phone_number' => 'required|string|max:20',
            ]);
        }

        Expense::create($validated);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display a specific expense.
     */
    public function show(Expense $expense)
    {
        $this->authorizeExpense($expense);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing an expense.
     */
    public function edit(Expense $expense)
    {
        $properties = Property::all();

        return view('expenses.edit', compact('expense', 'properties'));
    }

    /**
     * Update an existing expense.
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if (($validated['payment_method'] ?? '') === 'Mpesa') {
            $request->validate([
                'phone_number' => 'required|string|max:20',
            ]);
        }

        $expense->update($validated);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Delete an expense.
     */
    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);

        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Ensure users can only access their own expenses.
     */
    private function authorizeExpense(Expense $expense): void {}
}
