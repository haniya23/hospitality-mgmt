<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\IncomeRecord;
use App\Models\Property;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Get properties owned by the owner
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

        // 2. Build Query
        $query = IncomeRecord::whereIn('property_id', $propertyIds)
            ->with(['property', 'accommodation', 'reservation.guest'])
            ->latest('transaction_date')
            ->latest('id');

        // Filter: property_id
        if ($request->has('property_id') && $request->input('property_id') !== 'all') {
            $query->where('property_id', $request->input('property_id'));
        }

        // Filter: start_date & end_date
        if ($request->has('start_date') && $request->input('start_date') !== '') {
            $query->whereDate('transaction_date', '>=', $request->input('start_date'));
        }
        if ($request->has('end_date') && $request->input('end_date') !== '') {
            $query->whereDate('transaction_date', '<=', $request->input('end_date'));
        }

        // 3. Calculate Totals based on current filters
        $totalRevenueQuery = clone $query;
        $totalRevenue = $totalRevenueQuery->sum('paid_amount');

        $pendingQuery = clone $query;
        $pendingReceivables = $pendingQuery->selectRaw('SUM(amount - paid_amount) as pending')->value('pending') ?? 0;

        // 4. Paginate results
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
                    ];
                }),
            ]
        ]);
    }
}
