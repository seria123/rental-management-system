<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\Unit;

class ReportController extends Controller
{
    public function index()
    {
        $income = Payment::where('status', 'completed')->whereMonth('payment_date', now()->month)->sum('amount');
        $expenses = Expense::whereMonth('expense_date', now()->month)->sum('amount');
        $outstanding = RentInvoice::where('status', '!=', 'paid')->sum('balance');
        $vacantUnits = Unit::where('status', 'vacant')->count();

        return view('reports.index', compact('income', 'expenses', 'outstanding', 'vacantUnits'));
    }
}
