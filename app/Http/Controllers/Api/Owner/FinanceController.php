<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\ExpenseRecord;
use App\Models\IncomeRecord;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    private function getPeriodDates(string $period): array
    {
        return match ($period) {
            'day' => [
                'start' => now()->startOfDay(),
                'end'   => now()->endOfDay(),
                'label' => 'Today',
            ],
            'week' => [
                'start' => now()->startOfWeek(),
                'end'   => now()->endOfWeek(),
                'label' => 'This Week',
            ],
            default => [ // month
                'start' => now()->startOfMonth(),
                'end'   => now()->endOfMonth(),
                'label' => now()->format('F Y'),
            ],
        };
    }

    // -------------------------------------------------------
    // INDEX — Transaction list + KPIs + property filter
    // -------------------------------------------------------

    public function index(Request $request)
    {
        $user = $request->user();
        $properties = Property::where('owner_id', $user->id)->get();
        $propertyIds = $properties->pluck('id')->toArray();

        if (empty($propertyIds)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_revenue'       => 0,
                    'total_expenses'      => 0,
                    'net_profit'          => 0,
                    'profit_margin'       => 0,
                    'pending_receivables' => 0,
                    'transactions'        => ['data' => []],
                    'properties'          => [],
                ]
            ]);
        }

        $incomeQ  = IncomeRecord::whereIn('property_id', $propertyIds)
            ->with(['property', 'accommodation', 'reservation.guest', 'b2bPartner'])
            ->latest('transaction_date')->latest('id');

        $expenseQ = ExpenseRecord::whereIn('property_id', $propertyIds)
            ->with(['property', 'accommodation', 'category'])
            ->latest('transaction_date')->latest('id');

        if ($request->filled('property_id') && $request->input('property_id') !== 'all') {
            $incomeQ->where('property_id', $request->input('property_id'));
            $expenseQ->where('property_id', $request->input('property_id'));
        }
        if ($request->filled('start_date')) {
            $incomeQ->whereDate('transaction_date', '>=', $request->input('start_date'));
            $expenseQ->whereDate('transaction_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $incomeQ->whereDate('transaction_date', '<=', $request->input('end_date'));
            $expenseQ->whereDate('transaction_date', '<=', $request->input('end_date'));
        }

        $totalRevenue  = (double) (clone $incomeQ)->sum('amount');
        $totalExpenses = (double) $expenseQ->sum('amount');
        $netProfit     = $totalRevenue - $totalExpenses;
        $profitMargin  = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;
        $pendingReceivables = (double) (clone $incomeQ)
            ->selectRaw('SUM(amount - paid_amount) as pending')->value('pending') ?? 0;

        $incomeTransactions = (clone $incomeQ)->get()->map(function ($income) {
            return [
                'id' => $income->id,
                'transaction_kind' => 'income',
                'property_id' => $income->property_id,
                'accommodation_id' => $income->accommodation_id,
                'income_type' => $income->income_type,
                'amount' => (double) $income->amount,
                'paid_amount' => (double) $income->paid_amount,
                'payment_status' => $income->payment_status,
                'transaction_date' => $income->transaction_date?->toDateString(),
                'reference_number' => $income->reference_number,
                'notes' => $income->notes,
                'title' => $income->reservation?->guest?->name ?: ucfirst(str_replace('_', ' ', $income->income_type ?? 'income')),
                'subtitle' => $income->property?->name,
                'property' => $income->property ? [
                    'id' => $income->property->id,
                    'name' => $income->property->name,
                ] : null,
                'accommodation' => $income->accommodation ? [
                    'id' => $income->accommodation->id,
                    'display_name' => $income->accommodation->display_name,
                ] : null,
                'reservation' => $income->reservation ? [
                    'id' => $income->reservation->id,
                    'guest' => $income->reservation->guest ? [
                        'name' => $income->reservation->guest->name,
                    ] : null,
                ] : null,
            ];
        });

        $expenseTransactions = (clone $expenseQ)->get()->map(function ($expense) {
            return [
                'id' => $expense->id,
                'transaction_kind' => 'expense',
                'property_id' => $expense->property_id,
                'accommodation_id' => $expense->accommodation_id,
                'amount' => (double) $expense->amount,
                'paid_amount' => (double) $expense->paid_amount,
                'payment_status' => $expense->payment_status,
                'transaction_date' => $expense->transaction_date?->toDateString(),
                'reference_number' => $expense->receipt_number,
                'notes' => $expense->notes,
                'title' => $expense->title,
                'subtitle' => $expense->category?->name ?? 'Expense',
                'category' => $expense->category ? [
                    'name' => $expense->category->name,
                    'color' => $expense->category->color,
                ] : null,
                'property' => $expense->property ? [
                    'id' => $expense->property->id,
                    'name' => $expense->property->name,
                ] : null,
                'accommodation' => $expense->accommodation ? [
                    'id' => $expense->accommodation->id,
                    'display_name' => $expense->accommodation->display_name,
                ] : null,
            ];
        });

        $mergedTransactions = $incomeTransactions
            ->concat($expenseTransactions)
            ->sort(function (array $a, array $b) {
                $dateCompare = strcmp((string) ($b['transaction_date'] ?? ''), (string) ($a['transaction_date'] ?? ''));

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
            })
            ->values();

        $perPage = 20;
        $currentPage = max(1, (int) $request->input('page', 1));
        $transactions = new LengthAwarePaginator(
            $mergedTransactions->forPage($currentPage, $perPage)->values(),
            $mergedTransactions->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue'       => $totalRevenue,
                'total_expenses'      => $totalExpenses,
                'net_profit'          => $netProfit,
                'profit_margin'       => $profitMargin,
                'pending_receivables' => $pendingReceivables,
                'transactions'        => $transactions,
                'properties'          => $properties->map(fn($p) => [
                    'id'             => $p->id,
                    'name'           => $p->name,
                    'accommodations' => $p->accommodations()->with('predefinedType')->get()
                        ->map(fn($a) => ['id' => $a->id, 'display_name' => $a->display_name]),
                ]),
            ]
        ]);
    }

    // -------------------------------------------------------
    // SUMMARY — Period-based dashboard (Day/Week/Month)
    // Returns: KPIs, accommodation table, recent income, recent expenses, income_by_type
    // -------------------------------------------------------

    public function summary(Request $request)
    {
        $user        = $request->user();
        $properties  = Property::where('owner_id', $user->id)->get();
        $propertyIds = $properties->pluck('id')->toArray();

        if (empty($propertyIds)) {
            return response()->json(['success' => true, 'data' => $this->emptySummary()]);
        }

        $period     = $request->input('period', 'month');
        $dates      = $this->getPeriodDates($period);
        $start      = $dates['start'];
        $end        = $dates['end'];
        $propertyId = $request->input('property_id');

        // Apply property filter if set
        $filteredIds = ($propertyId && $propertyId !== 'all')
            ? [$propertyId]
            : $propertyIds;

        // KPIs
        $totalRevenue  = (double) IncomeRecord::whereIn('property_id', $filteredIds)
            ->whereBetween('transaction_date', [$start, $end])->sum('amount');
        $totalExpenses = (double) ExpenseRecord::whereIn('property_id', $filteredIds)
            ->whereBetween('transaction_date', [$start, $end])->sum('amount');
        $netProfit     = $totalRevenue - $totalExpenses;
        $profitMargin  = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        // Income by type (for pie/donut chart)
        $incomeByType = IncomeRecord::whereIn('property_id', $filteredIds)
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('income_type, SUM(amount) as total')
            ->groupBy('income_type')
            ->get()
            ->map(fn($r) => ['type' => $r->income_type, 'total' => (double) $r->total])
            ->values();

        // Accommodation performance table
        $accommodations = PropertyAccommodation::whereIn('property_id', $filteredIds)
            ->with('predefinedType')
            ->get()
            ->map(function ($acc) use ($start, $end) {
                $income   = (double) IncomeRecord::where('accommodation_id', $acc->id)
                    ->whereBetween('transaction_date', [$start, $end])->sum('amount');
                $expenses = (double) ExpenseRecord::where('accommodation_id', $acc->id)
                    ->whereBetween('transaction_date', [$start, $end])->sum('amount');
                return [
                    'id'               => $acc->id,
                    'name'             => $acc->display_name,
                    'income'           => $income,
                    'expenses'         => $expenses,
                    'net_contribution' => round($income - $expenses, 2),
                ];
            })
            ->sortByDesc('net_contribution')
            ->values();

        // Calculate share %
        $totalAccIncome = $accommodations->sum('income');
        $accommodations = $accommodations->map(function ($acc) use ($totalAccIncome) {
            $acc['share'] = $totalAccIncome > 0
                ? round(($acc['income'] / $totalAccIncome) * 100, 1) : 0;
            return $acc;
        });

        // Recent income (last 5)
        $recentIncome = IncomeRecord::whereIn('property_id', $filteredIds)
            ->whereBetween('transaction_date', [$start, $end])
            ->with('accommodation')
            ->latest('transaction_date')->latest('id')
            ->take(5)->get()
            ->map(fn($r) => [
                'id'              => $r->id,
                'income_type'     => $r->income_type,
                'amount'          => (double) $r->amount,
                'paid_amount'     => (double) $r->paid_amount,
                'payment_status'  => $r->payment_status,
                'transaction_date'=> $r->transaction_date?->toDateString(),
                'accommodation'   => $r->accommodation ? $r->accommodation->display_name : 'General',
            ]);

        // Recent expenses (last 5)
        $recentExpenses = ExpenseRecord::whereIn('property_id', $filteredIds)
            ->whereBetween('transaction_date', [$start, $end])
            ->with(['accommodation', 'category'])
            ->latest('transaction_date')->latest('id')
            ->take(5)->get()
            ->map(fn($r) => [
                'id'               => $r->id,
                'title'            => $r->title,
                'amount'           => (double) $r->amount,
                'payment_status'   => $r->payment_status,
                'transaction_date' => $r->transaction_date?->toDateString(),
                'category'         => $r->category?->name ?? 'Other',
                'category_color'   => $r->category?->color ?? '#6B7280',
                'accommodation'    => $r->accommodation ? $r->accommodation->display_name : null,
            ]);

        // Property list for filter dropdown
        $propertyList = $properties->map(fn($p) => [
            'id'   => $p->id,
            'name' => $p->name,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'period'                => $period,
                'period_label'          => $dates['label'],
                'total_revenue'         => $totalRevenue,
                'total_expenses'        => $totalExpenses,
                'net_profit'            => $netProfit,
                'profit_margin'         => $profitMargin,
                'income_by_type'        => $incomeByType,
                'accommodation_performance' => $accommodations,
                'recent_income'         => $recentIncome,
                'recent_expenses'       => $recentExpenses,
                'properties'            => $propertyList,
            ]
        ]);
    }

    private function emptySummary(): array
    {
        return [
            'period'                    => 'month',
            'period_label'              => now()->format('F Y'),
            'total_revenue'             => 0,
            'total_expenses'            => 0,
            'net_profit'                => 0,
            'profit_margin'             => 0,
            'income_by_type'            => [],
            'accommodation_performance' => [],
            'recent_income'             => [],
            'recent_expenses'           => [],
            'properties'                => [],
        ];
    }

    // -------------------------------------------------------
    // INCOME CRUD
    // -------------------------------------------------------

    public function show(Request $request, $id)
    {
        $user        = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();
        $income = IncomeRecord::with(['property', 'accommodation', 'reservation.guest', 'b2bPartner'])
            ->whereIn('property_id', $propertyIds)->findOrFail($id);
        return response()->json(['success' => true, 'data' => $income]);
    }

    public function store(Request $request)
    {
        $user        = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();

        $validated = $request->validate([
            'property_id'     => ['required', Rule::in($propertyIds)],
            'accommodation_id'=> ['nullable', 'exists:property_accommodations,id'],
            'b2b_partner_id'  => ['nullable', 'exists:b2b_partners,id'],
            'income_type'     => ['required', Rule::in(['booking', 'rental', 'service', 'deposit', 'penalty', 'commission', 'other'])],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'transaction_date'=> ['required', 'date'],
            'payment_status'  => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount'     => ['nullable', 'numeric', 'min:0'],
            'reference_number'=> ['nullable', 'string', 'max:255'],
            'notes'           => ['nullable', 'string'],
        ]);

        if ($validated['payment_status'] === 'paid')   $validated['paid_amount'] = $validated['amount'];
        elseif ($validated['payment_status'] === 'unpaid') $validated['paid_amount'] = 0;
        $validated['created_by'] = $user->id;

        $income = IncomeRecord::create($validated);
        return response()->json(['success' => true, 'message' => 'Income recorded', 'data' => $income], 201);
    }

    public function update(Request $request, $id)
    {
        $user        = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();
        $income = IncomeRecord::whereIn('property_id', $propertyIds)->findOrFail($id);

        $validated = $request->validate([
            'property_id'     => ['required', Rule::in($propertyIds)],
            'accommodation_id'=> ['nullable', 'exists:property_accommodations,id'],
            'b2b_partner_id'  => ['nullable', 'exists:b2b_partners,id'],
            'income_type'     => ['required', Rule::in(['booking', 'rental', 'service', 'deposit', 'penalty', 'commission', 'other'])],
            'amount'          => ['required', 'numeric', 'min:0.01'],
            'transaction_date'=> ['required', 'date'],
            'payment_status'  => ['required', Rule::in(['paid', 'unpaid', 'partial'])],
            'paid_amount'     => ['nullable', 'numeric', 'min:0'],
            'reference_number'=> ['nullable', 'string', 'max:255'],
            'notes'           => ['nullable', 'string'],
        ]);

        if ($validated['payment_status'] === 'paid')   $validated['paid_amount'] = $validated['amount'];
        elseif ($validated['payment_status'] === 'unpaid') $validated['paid_amount'] = 0;
        $validated['updated_by'] = $user->id;

        $income->update($validated);
        return response()->json(['success' => true, 'message' => 'Income updated', 'data' => $income]);
    }

    public function destroy(Request $request, $id)
    {
        $user        = $request->user();
        $propertyIds = Property::where('owner_id', $user->id)->pluck('id')->toArray();
        IncomeRecord::whereIn('property_id', $propertyIds)->findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Income deleted']);
    }
}
