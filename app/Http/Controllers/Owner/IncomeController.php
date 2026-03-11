<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\IncomeRecord;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    /**
     * Display a listing of income records.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $properties = $user->properties;
        $propertyIds = $properties->pluck('id')->toArray();

        $query = IncomeRecord::whereIn('property_id', $propertyIds)
            ->with(['property', 'accommodation', 'b2bPartner', 'creator'])
            ->latest('transaction_date');

        // Apply filters
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('income_type')) {
            $query->where('income_type', $request->income_type);
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

        $incomes = $query->paginate(20);

        return view('owner.income.index', compact('incomes', 'properties'));
    }

    /**
     * Show the form for creating a new income record.
     */
    public function create()
    {
        $user = auth()->user();
        $properties = $user->properties()->with('accommodations')->get();

        $incomeTypes = [
            'booking' => 'Booking Revenue',
            'rental' => 'Rental Income',
            'service' => 'Service Charge',
            'deposit' => 'Security Deposit',
            'penalty' => 'Penalty/Late Fee',
            'commission' => 'Commission',
            'other' => 'Other Income',
        ];

        return view('owner.income.create', compact('properties', 'incomeTypes'));
    }

    /**
     * Store a newly created income record.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $propertyIds = $user->properties->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($propertyIds)],
            'accommodation_id' => ['nullable', 'exists:property_accommodations,id'],
            'b2b_partner_id' => ['nullable', 'exists:b2b_partners,id'],
            'income_type' => ['required', Rule::in(['booking', 'rental', 'service', 'deposit', 'penalty', 'commission', 'other'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'payment_status' => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Calculate paid amount based on status
        if ($validated['payment_status'] === 'paid') {
            $validated['paid_amount'] = $validated['amount'];
        } elseif ($validated['payment_status'] === 'unpaid') {
            $validated['paid_amount'] = 0;
        }

        $validated['created_by'] = auth()->id();

        IncomeRecord::create($validated);

        return redirect()->route('owner.income.index')
            ->with('success', 'Income record created successfully.');
    }

    /**
     * Display the specified income record.
     */
    public function show(IncomeRecord $income)
    {
        $this->authorize('view', $income->property);

        $income->load(['property', 'accommodation', 'b2bPartner', 'reservation', 'adjustments.adjustedBy', 'creator']);

        return view('owner.income.show', compact('income'));
    }

    /**
     * Show the form for editing the specified income record.
     */
    public function edit(IncomeRecord $income)
    {
        $this->authorize('update', $income->property);

        $user = auth()->user();
        $properties = $user->properties()->with('accommodations')->get();

        $incomeTypes = [
            'booking' => 'Booking Revenue',
            'rental' => 'Rental Income',
            'service' => 'Service Charge',
            'deposit' => 'Security Deposit',
            'penalty' => 'Penalty/Late Fee',
            'commission' => 'Commission',
            'other' => 'Other Income',
        ];

        return view('owner.income.edit', compact('income', 'properties', 'incomeTypes'));
    }

    /**
     * Update the specified income record.
     */
    public function update(Request $request, IncomeRecord $income)
    {
        $this->authorize('update', $income->property);

        $user = auth()->user();
        $propertyIds = $user->properties->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id' => ['required', Rule::in($propertyIds)],
            'accommodation_id' => ['nullable', 'exists:property_accommodations,id'],
            'b2b_partner_id' => ['nullable', 'exists:b2b_partners,id'],
            'income_type' => ['required', Rule::in(['booking', 'rental', 'service', 'deposit', 'penalty', 'commission', 'other'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'payment_status' => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['payment_status'] === 'paid') {
            $validated['paid_amount'] = $validated['amount'];
        } elseif ($validated['payment_status'] === 'unpaid') {
            $validated['paid_amount'] = 0;
        }

        $validated['updated_by'] = auth()->id();

        $income->update($validated);

        return redirect()->route('owner.income.show', $income)
            ->with('success', 'Income record updated successfully.');
    }

    /**
     * Remove the specified income record.
     */
    public function destroy(IncomeRecord $income)
    {
        $this->authorize('delete', $income->property);

        $income->delete();

        return redirect()->route('owner.income.index')
            ->with('success', 'Income record deleted successfully.');
    }
}
