<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\IncomeRecord;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $properties = Property::where('owner_id', $user->id)->get();
        $propertyIds = $properties->pluck('id')->toArray();

        if (empty($propertyIds)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_revenue' => 0,
                    'pending_receivables' => 0,
                    'transactions' => [
                        'data' => [],
                    ],
                    'properties' => [],
                ]
            ]);
        }

        $query = IncomeRecord::whereIn('property_id', $propertyIds)
            ->with(['property', 'accommodation', 'reservation.guest', 'b2bPartner'])
            ->latest('transaction_date')
            ->latest('id');

        if ($request->has('property_id') && $request->input('property_id') !== 'all') {
            $query->where('property_id', $request->input('property_id'));
        }

        if ($request->has('start_date') && $request->input('start_date') !== '') {
            $query->whereDate('transaction_date', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date') && $request->input('end_date') !== '') {
            $query->whereDate('transaction_date', '<=', $request->input('end_date'));
        }

        $totalRevenueQuery = clone $query;
        $totalRevenue = $totalRevenueQuery->sum('paid_amount');

        $pendingQuery = clone $query;
        $pendingReceivables = $pendingQuery->selectRaw('SUM(amount - paid_amount) as pending')->value('pending') ?? 0;

        $transactions = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => (double) $totalRevenue,
                'pending_receivables' => (double) $pendingReceivables,
                'transactions' => $transactions,
                'properties' => $properties->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'accommodations' => $p->accommodations()->get(['id', 'display_name']),
                    ];
                }),
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();
        
        $income = IncomeRecord::with(['property', 'accommodation', 'reservation.guest', 'b2bPartner'])
            ->whereIn('property_id', $propertyIds)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $income
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

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

        $validated['created_by'] = $user->id;

        $income = IncomeRecord::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Income record created successfully',
            'data' => $income
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        $income = IncomeRecord::whereIn('property_id', $propertyIds)->findOrFail($id);

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

        $validated['updated_by'] = $user->id;

        $income->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Income record updated successfully',
            'data' => $income
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        $income = IncomeRecord::whereIn('property_id', $propertyIds)->findOrFail($id);
        $income->delete();

        return response()->json([
            'success' => true,
            'message' => 'Income record deleted successfully'
        ]);
    }
}
