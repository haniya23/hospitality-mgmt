<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRecord;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expense records.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $properties = $user->properties;
        $propertyIds = $properties->pluck('id')->toArray();
        $categories = ExpenseCategory::active()->orderBy('name')->get();

        $query = ExpenseRecord::whereIn('property_id', $propertyIds)
            ->with(['property', 'accommodation', 'category', 'creator'])
            ->latest('transaction_date');

        // Apply filters
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        $expenses = $query->paginate(20);

        return view('owner.expense.index', compact('expenses', 'properties', 'categories'));
    }

    /**
     * Show the form for creating a new expense record.
     */
    public function create()
    {
        $user = auth()->user();
        $properties = $user->properties()->with('accommodations')->get();
        $categories = ExpenseCategory::active()->orderBy('name')->get();

        $paymentMethods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'upi' => 'UPI',
            'cheque' => 'Cheque',
            'other' => 'Other',
        ];

        return view('owner.expense.create', compact('properties', 'categories', 'paymentMethods'));
    }

    /**
     * Store a newly created expense record.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $propertyIds = $user->properties->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($propertyIds)],
            'accommodation_id' => ['nullable', 'exists:property_accommodations,id'],
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'bank_transfer', 'upi', 'cheque', 'other'])],
            'payment_status' => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])],
            'recurring_end_date' => ['nullable', 'date', 'after:transaction_date'],
        ]);

        // Calculate paid amount based on status
        if ($validated['payment_status'] === 'paid') {
            $validated['paid_amount'] = $validated['amount'];
        } elseif ($validated['payment_status'] === 'unpaid') {
            $validated['paid_amount'] = 0;
        }

        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['created_by'] = auth()->id();

        ExpenseRecord::create($validated);

        return redirect()->route('owner.expense.index')
            ->with('success', 'Expense record created successfully.');
    }

    /**
     * Display the specified expense record.
     */
    public function show(ExpenseRecord $expense)
    {
        $this->authorize('view', $expense->property);

        $expense->load(['property', 'accommodation', 'category', 'adjustments.adjustedBy', 'creator']);

        return view('owner.expense.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense record.
     */
    public function edit(ExpenseRecord $expense)
    {
        $this->authorize('update', $expense->property);

        $user = auth()->user();
        $properties = $user->properties()->with('accommodations')->get();
        $categories = ExpenseCategory::active()->orderBy('name')->get();

        $paymentMethods = [
            'cash' => 'Cash',
            'card' => 'Card',
            'bank_transfer' => 'Bank Transfer',
            'upi' => 'UPI',
            'cheque' => 'Cheque',
            'other' => 'Other',
        ];

        return view('owner.expense.edit', compact('expense', 'properties', 'categories', 'paymentMethods'));
    }

    /**
     * Update the specified expense record.
     */
    public function update(Request $request, ExpenseRecord $expense)
    {
        $this->authorize('update', $expense->property);

        $user = auth()->user();
        $propertyIds = $user->properties->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($propertyIds)],
            'accommodation_id' => ['nullable', 'exists:property_accommodations,id'],
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(['cash', 'card', 'bank_transfer', 'upi', 'cheque', 'other'])],
            'payment_status' => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
            'is_recurring' => ['boolean'],
            'recurring_frequency' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])],
            'recurring_end_date' => ['nullable', 'date', 'after:transaction_date'],
        ]);

        if ($validated['payment_status'] === 'paid') {
            $validated['paid_amount'] = $validated['amount'];
        } elseif ($validated['payment_status'] === 'unpaid') {
            $validated['paid_amount'] = 0;
        }

        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['updated_by'] = auth()->id();

        $expense->update($validated);

        return redirect()->route('owner.expense.show', $expense)
            ->with('success', 'Expense record updated successfully.');
    }

    /**
     * Remove the specified expense record.
     */
    public function destroy(ExpenseRecord $expense)
    {
        $this->authorize('delete', $expense->property);

        $expense->delete();

        return redirect()->route('owner.expense.index')
            ->with('success', 'Expense record deleted successfully.');
    }
}
