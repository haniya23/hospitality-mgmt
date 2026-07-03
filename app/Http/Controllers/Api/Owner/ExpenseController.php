<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRecord;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        if (empty($propertyIds)) {
            return response()->json(['success' => true, 'data' => ['data' => []]]);
        }

        $query = ExpenseRecord::whereIn('property_id', $propertyIds)
            ->with(['property', 'accommodation', 'category'])
            ->latest('transaction_date')
            ->latest('id');

        if ($request->filled('property_id') && $request->input('property_id') !== 'all') {
            $query->where('property_id', $request->input('property_id'));
        }
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->input('category_id'));
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->input('end_date'));
        }

        $expenses = $query->paginate(20);

        return response()->json(['success' => true, 'data' => $expenses]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id'          => ['required', Rule::in($propertyIds)],
            'accommodation_id'     => ['nullable', 'exists:property_accommodations,id'],
            'expense_category_id'  => ['required', 'exists:expense_categories,id'],
            'title'                => ['required', 'string', 'max:255'],
            'amount'               => ['required', 'numeric', 'min:0.01'],
            'transaction_date'     => ['required', 'date'],
            'payment_method'       => ['required', Rule::in(['cash', 'card', 'bank_transfer', 'upi', 'cheque', 'other'])],
            'payment_status'       => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount'          => ['nullable', 'numeric', 'min:0'],
            'notes'                => ['nullable', 'string'],
            'vendor_name'          => ['nullable', 'string', 'max:255'],
            'receipt_number'       => ['nullable', 'string', 'max:255'],
            'is_recurring'         => ['nullable', 'boolean'],
            'recurring_frequency'  => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])],
            'recurring_end_date'   => ['nullable', 'date'],
        ]);

        if ($validated['payment_status'] === 'paid') {
            $validated['paid_amount'] = $validated['amount'];
        } elseif ($validated['payment_status'] === 'unpaid') {
            $validated['paid_amount'] = 0;
        }

        $validated['is_recurring'] = (bool) ($validated['is_recurring'] ?? false);
        $validated['created_by'] = $user->id;

        $expense = ExpenseRecord::create($validated);
        $expense->load(['property', 'accommodation', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Expense recorded successfully',
            'data'    => $expense,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        $expense = ExpenseRecord::whereIn('property_id', $propertyIds)->findOrFail($id);

        $validated = $request->validate([
            'property_id'          => ['required', Rule::in($propertyIds)],
            'accommodation_id'     => ['nullable', 'exists:property_accommodations,id'],
            'expense_category_id'  => ['required', 'exists:expense_categories,id'],
            'title'                => ['required', 'string', 'max:255'],
            'amount'               => ['required', 'numeric', 'min:0.01'],
            'transaction_date'     => ['required', 'date'],
            'payment_method'       => ['required', Rule::in(['cash', 'card', 'bank_transfer', 'upi', 'cheque', 'other'])],
            'payment_status'       => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount'          => ['nullable', 'numeric', 'min:0'],
            'notes'                => ['nullable', 'string'],
            'vendor_name'          => ['nullable', 'string', 'max:255'],
            'receipt_number'       => ['nullable', 'string', 'max:255'],
            'is_recurring'         => ['nullable', 'boolean'],
            'recurring_frequency'  => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])],
            'recurring_end_date'   => ['nullable', 'date'],
        ]);

        if ($validated['payment_status'] === 'paid') {
            $validated['paid_amount'] = $validated['amount'];
        } elseif ($validated['payment_status'] === 'unpaid') {
            $validated['paid_amount'] = 0;
        }

        $validated['is_recurring'] = (bool) ($validated['is_recurring'] ?? false);
        $validated['updated_by'] = $user->id;

        $expense->update($validated);
        $expense->load(['property', 'accommodation', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data'    => $expense,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        $expense = ExpenseRecord::whereIn('property_id', $propertyIds)->findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
        ]);
    }

    public function categories()
    {
        $categories = ExpenseCategory::active()->orderBy('name')->get(['id', 'name', 'slug', 'color']);
        return response()->json(['success' => true, 'data' => $categories]);
    }
}
