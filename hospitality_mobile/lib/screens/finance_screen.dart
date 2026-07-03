import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/riverpod_providers.dart';
import 'expenses_screen.dart';

// ============================================================
// FINANCE HOME (Dashboard / Summary)
// ============================================================
class FinanceScreen extends ConsumerStatefulWidget {
  const FinanceScreen({super.key});

  @override
  ConsumerState<FinanceScreen> createState() => _FinanceScreenState();
}

class _FinanceScreenState extends ConsumerState<FinanceScreen>
    with SingleTickerProviderStateMixin {
  // ── Theme ──────────────────────────────────────────────────
  static const Color primaryColor = Color(0xFF2E3E2A);
  static const Color textPrimary = Color(0xFF191D19);
  static const Color textSecondary = Color(0xFF5A7251);
  static const Color backgroundColor = Color(0xFFF2F5F0);
  static const Color incomeGreen = Color(0xFF10B981);
  static const Color expenseRed = Color(0xFFEF4444);
  static const Color profitBlue = Color(0xFF3B82F6);
  static const Color marginPurple = Color(0xFF8B5CF6);

  late TabController _tabController;
  int _touchedPieIndex = -1;
  int _selectedPieIndex = -1;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    Future.microtask(() {
      ref.read(financeProvider).fetchSummary();
      ref.read(financeProvider).fetchFinanceData();
      ref.read(expenseProvider).fetchCategories();
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  // ── Helpers ────────────────────────────────────────────────
  Color _parseHex(String? hex) {
    if (hex == null) return textSecondary;
    try { return Color(int.parse('FF${hex.replaceAll('#', '')}', radix: 16)); }
    catch (_) { return textSecondary; }
  }

  String _fmt(double v) => '₹${NumberFormat('#,##,###.##').format(v)}';
  String _fmtDate(String? d) {
    if (d == null) return '';
    try { return DateFormat('d MMM').format(DateTime.parse(d)); } catch (_) { return d; }
  }

  // ── Period selector ────────────────────────────────────────
  Widget _periodSelector() {
    final fin = ref.read(financeProvider);
    return Row(
      children: ['day', 'week', 'month'].map((p) {
        final selected = fin.period == p;
        return GestureDetector(
          onTap: () => fin.setPeriod(p),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
            margin: const EdgeInsets.only(right: 6),
            decoration: BoxDecoration(
              color: selected ? primaryColor : Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: selected ? primaryColor : Colors.grey.shade300),
            ),
            child: Text(
              p[0].toUpperCase() + p.substring(1),
              style: GoogleFonts.outfit(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: selected ? Colors.white : textSecondary,
              ),
            ),
          ),
        );
      }).toList(),
    );
  }

  // ── KPI Card ───────────────────────────────────────────────
  Widget _kpiCard(String title, double value, IconData icon, Color color,
      {bool isPercent = false}) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [color, color.withValues(alpha: 0.75)],
          begin: Alignment.topLeft, end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(color: color.withValues(alpha: 0.3), blurRadius: 12, offset: const Offset(0, 5)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.25),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: Colors.white, size: 18),
          ),
          const Spacer(),
          Text(title,
            style: GoogleFonts.outfit(color: Colors.white.withValues(alpha: 0.85), fontSize: 11, fontWeight: FontWeight.w600)),
          const SizedBox(height: 2),
          FittedBox(
            fit: BoxFit.scaleDown,
            alignment: Alignment.centerLeft,
            child: Text(
              isPercent ? '${value.toStringAsFixed(1)}%' : _fmt(value),
              style: GoogleFonts.outfit(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900),
            ),
          ),
        ],
      ),
    );
  }

  // ── Accommodation Performance ──────────────────────────────
  Widget _accommodationTable(List<dynamic> rows) {
    if (rows.isEmpty) {
      return Center(child: Padding(
        padding: const EdgeInsets.all(20),
        child: Text('No accommodation data', style: GoogleFonts.outfit(color: textSecondary)),
      ));
    }
    return Column(
      children: [
        // Header
        Padding(
          padding: const EdgeInsets.symmetric(vertical: 8),
          child: Row(
            children: ['Room', 'Income', 'Expenses', 'Profit', 'Share'].map((h) =>
              Expanded(
                child: Text(h, textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(fontSize: 10, fontWeight: FontWeight.bold, color: textSecondary)),
              )).toList(),
          ),
        ),
        const Divider(height: 1),
        ...rows.map((acc) {
          final income = (acc['income'] as num).toDouble();
          final expenses = (acc['expenses'] as num).toDouble();
          final profit = (acc['net_contribution'] as num).toDouble();
          final share = (acc['share'] as num?)?.toDouble() ?? 0.0;
          return Column(
            children: [
              Padding(
                padding: const EdgeInsets.symmetric(vertical: 10),
                child: Row(
                  children: [
                    Expanded(child: Row(children: [
                      Container(width: 28, height: 28, decoration: BoxDecoration(
                        color: primaryColor.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(8)),
                        child: const Icon(Icons.bed_rounded, color: primaryColor, size: 16)),
                      const SizedBox(width: 6),
                      Expanded(child: Text(acc['name']?.toString() ?? '',
                        style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 11, color: textPrimary),
                        overflow: TextOverflow.ellipsis)),
                    ])),
                    Expanded(child: Text(_fmt(income), textAlign: TextAlign.center,
                      style: GoogleFonts.outfit(fontSize: 11, color: incomeGreen, fontWeight: FontWeight.bold))),
                    Expanded(child: Text(_fmt(expenses), textAlign: TextAlign.center,
                      style: GoogleFonts.outfit(fontSize: 11, color: expenseRed, fontWeight: FontWeight.bold))),
                    Expanded(child: Text(_fmt(profit), textAlign: TextAlign.center,
                      style: GoogleFonts.outfit(fontSize: 11,
                        color: profit >= 0 ? profitBlue : expenseRed, fontWeight: FontWeight.bold))),
                    Expanded(child: Container(
                      alignment: Alignment.center,
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                      decoration: BoxDecoration(
                        color: marginPurple.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text('${share.toStringAsFixed(0)}%',
                        style: GoogleFonts.outfit(fontSize: 10, color: marginPurple, fontWeight: FontWeight.bold)),
                    )),
                  ],
                ),
              ),
              Divider(height: 1, color: Colors.grey.shade100),
            ],
          );
        }),
      ],
    );
  }

  // ── Pie Chart ──────────────────────────────────────────────
  Widget _incomeByTypePie(List<dynamic> data) {
    if (data.isEmpty) {
      return Center(child: Padding(
        padding: const EdgeInsets.all(20),
        child: Text('No income data for this period', style: GoogleFonts.outfit(color: textSecondary)),
      ));
    }
    final colors = [incomeGreen, profitBlue, marginPurple, const Color(0xFFF59E0B),
      expenseRed, const Color(0xFF06B6D4), const Color(0xFFEC4899)];
    final total = data.fold<double>(0, (s, e) => s + (e['total'] as num).toDouble());

    return Row(
      children: [
        Expanded(
          child: SizedBox(
            height: 160,
            child: PieChart(
              PieChartData(
                pieTouchData: PieTouchData(
                  touchCallback: (event, response) {
                    setState(() {
                      if (!event.isInterestedForInteractions || response == null || response.touchedSection == null) {
                        _touchedPieIndex = -1;
                        return;
                      }
                      _touchedPieIndex = response.touchedSection!.touchedSectionIndex;
                    });
                  },
                ),
                sections: List.generate(data.length, (i) {
                  final isTouched = i == _touchedPieIndex;
                  final val = (data[i]['total'] as num).toDouble();
                  return PieChartSectionData(
                    value: val,
                    color: colors[i % colors.length],
                    radius: isTouched ? 60 : 48,
                    title: isTouched ? '${(val / total * 100).toStringAsFixed(0)}%' : '',
                    titleStyle: GoogleFonts.outfit(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white),
                  );
                }),
                centerSpaceRadius: 36,
                sectionsSpace: 2,
              ),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: List.generate(data.length, (i) {
            final d = data[i];
            final pct = total > 0 ? ((d['total'] as num).toDouble() / total * 100) : 0.0;
            return Padding(
              padding: const EdgeInsets.only(bottom: 6),
              child: Row(
                children: [
                  Container(width: 10, height: 10,
                    decoration: BoxDecoration(color: colors[i % colors.length], shape: BoxShape.circle)),
                  const SizedBox(width: 6),
                  Text(
                    '${_incomeTypeLabel(d['type']?.toString())} ${pct.toStringAsFixed(0)}%',
                    style: GoogleFonts.outfit(fontSize: 11, color: textPrimary, fontWeight: FontWeight.w600),
                  ),
                ],
              ),
            );
          }),
        ),
      ],
    );
  }

  String _incomeTypeLabel(String? t) => switch (t) {
    'booking' => 'Booking',
    'rental' => 'Rental',
    'service' => 'Service',
    'deposit' => 'Deposit',
    'penalty' => 'Penalty',
    'commission' => 'Commission',
    _ => 'Other',
  };

  // ── Recent Transactions ────────────────────────────────────
  Widget _recentIncomeTile(Map item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: incomeGreen.withValues(alpha: 0.07),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: incomeGreen.withValues(alpha: 0.15)),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(_incomeTypeLabel(item['income_type']?.toString()),
                  style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 13, color: textPrimary)),
                Text('${item['accommodation'] ?? 'General'} • ${_fmtDate(item['transaction_date']?.toString())}',
                  style: GoogleFonts.outfit(fontSize: 11, color: textSecondary)),
              ],
            ),
          ),
          Text(_fmt((item['amount'] as num).toDouble()),
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 13, color: incomeGreen)),
        ],
      ),
    );
  }

  Widget _recentExpenseTile(Map item) {
    final catColor = _parseHex(item['category_color']?.toString());
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
      decoration: BoxDecoration(
        color: expenseRed.withValues(alpha: 0.05),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: expenseRed.withValues(alpha: 0.12)),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item['title']?.toString() ?? 'Expense',
                  style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 13, color: textPrimary)),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 1),
                      decoration: BoxDecoration(
                        color: catColor.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(item['category']?.toString() ?? 'Other',
                        style: GoogleFonts.outfit(fontSize: 9, color: catColor, fontWeight: FontWeight.bold)),
                    ),
                    const SizedBox(width: 4),
                    Text(_fmtDate(item['transaction_date']?.toString()),
                      style: GoogleFonts.outfit(fontSize: 11, color: textSecondary)),
                  ],
                ),
              ],
            ),
          ),
          Text(_fmt((item['amount'] as num).toDouble()),
            style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 13, color: expenseRed)),
        ],
      ),
    );
  }

  // ── Card wrapper ───────────────────────────────────────────
  Widget _card({required String title, IconData? icon, Color? iconColor, required Widget child}) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: primaryColor.withValues(alpha: 0.04), blurRadius: 12, offset: const Offset(0, 4))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: Row(
              children: [
                if (icon != null) ...[
                  Container(
                    padding: const EdgeInsets.all(7),
                    decoration: BoxDecoration(
                      color: (iconColor ?? primaryColor).withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(icon, color: iconColor ?? primaryColor, size: 17),
                  ),
                  const SizedBox(width: 10),
                ],
                Text(title, style: GoogleFonts.outfit(
                  fontSize: 15, fontWeight: FontWeight.bold, color: iconColor ?? primaryColor)),
              ],
            ),
          ),
          Padding(padding: const EdgeInsets.fromLTRB(16, 0, 16, 16), child: child),
        ],
      ),
    );
  }

  // ── BUILD ──────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final fin = ref.watch(financeProvider);
    final exp = ref.watch(expenseProvider);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        backgroundColor: backgroundColor,
        elevation: 0,
        scrolledUnderElevation: 0,
        title: Text('Finance', style: GoogleFonts.outfit(
          fontWeight: FontWeight.bold, color: textPrimary, fontSize: 20)),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: textPrimary),
            onPressed: () {
              fin.fetchSummary();
              fin.fetchFinanceData();
            },
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          labelColor: primaryColor,
          unselectedLabelColor: textSecondary,
          indicatorColor: primaryColor,
          indicatorWeight: 2.5,
          labelStyle: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 13),
          tabs: const [Tab(text: 'Dashboard'), Tab(text: 'Transactions')],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildDashboardTab(fin),
          _buildTransactionsTab(fin, exp),
        ],
      ),
      floatingActionButton: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          FloatingActionButton.small(
            heroTag: 'add_expense',
            onPressed: () => Navigator.push(
              context, MaterialPageRoute(builder: (_) => const ExpensesScreen())),
            backgroundColor: expenseRed,
            tooltip: 'Manage Expenses',
            child: const Icon(Icons.receipt_long_rounded, color: Colors.white, size: 18),
          ),
          const SizedBox(height: 8),
          FloatingActionButton(
            heroTag: 'add_income',
            onPressed: () => _showIncomeForm(fin),
            backgroundColor: primaryColor,
            tooltip: 'Add Income',
            child: const Icon(Icons.add_rounded, color: Colors.white),
          ),
        ],
      ),
    );
  }

  // ── DASHBOARD TAB ──────────────────────────────────────────
  Widget _buildDashboardTab(dynamic fin) {
    return fin.isSummaryLoading && fin.summaryData == null
        ? const Center(child: CircularProgressIndicator(color: primaryColor))
        : RefreshIndicator(
            onRefresh: () => fin.fetchSummary(),
            color: primaryColor,
            child: ListView(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
              children: [
                // Period + Property filter row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Consumer(builder: (ctx, ref, _) {
                      final f = ref.watch(financeProvider);
                      return Text(
                        f.periodLabel.isNotEmpty ? f.periodLabel : 'This Month',
                        style: GoogleFonts.outfit(fontSize: 13, color: textSecondary, fontWeight: FontWeight.w600),
                      );
                    }),
                    Consumer(builder: (ctx, ref, _) => _periodSelector()),
                  ],
                ),
                const SizedBox(height: 12),

                // Property dropdown
                if (fin.summaryProperties.isNotEmpty) ...[
                  DropdownButtonHideUnderline(
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: primaryColor.withValues(alpha: 0.1)),
                      ),
                      child: Consumer(builder: (ctx, ref, _) {
                        final f = ref.watch(financeProvider);
                        return DropdownButton<String>(
                          value: f.selectedPropertyId,
                          isExpanded: true,
                          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: textSecondary),
                          style: GoogleFonts.outfit(fontWeight: FontWeight.w600, color: textPrimary, fontSize: 13),
                          items: [
                            const DropdownMenuItem(value: 'all', child: Text('All Properties')),
                            ...f.summaryProperties.map((p) => DropdownMenuItem(
                              value: p['id'].toString(), child: Text(p['name'] ?? ''))),
                          ],
                          onChanged: (v) { if (v != null) f.setSummaryProperty(v); },
                        );
                      }),
                    ),
                  ),
                  const SizedBox(height: 14),
                ],

                // 4 KPI Cards
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                  childAspectRatio: 1.35,
                  children: [
                    _kpiCard('Total Income', fin.summaryRevenue, Icons.account_balance_wallet_rounded, incomeGreen),
                    _kpiCard('Total Expenses', fin.summaryExpenses, Icons.payments_rounded, expenseRed),
                    _kpiCard('Net Profit', fin.summaryNetProfit, Icons.trending_up_rounded, profitBlue),
                    _kpiCard('Profit Margin', fin.summaryProfitMargin, Icons.pie_chart_rounded, marginPurple, isPercent: true),
                  ],
                ),
                const SizedBox(height: 16),

                // Accommodation Performance
                _card(
                  title: 'Accommodation Performance',
                  icon: Icons.home_work_rounded,
                  iconColor: marginPurple,
                  child: _accommodationTable(fin.accommodationPerformance),
                ),
                const SizedBox(height: 16),

                // Income by Type Pie Chart
                if (fin.incomeByType.isNotEmpty)
                  _card(
                    title: 'Income by Type',
                    icon: Icons.donut_large_rounded,
                    iconColor: incomeGreen,
                    child: _incomeByTypePie(fin.incomeByType),
                  ),
                if (fin.incomeByType.isNotEmpty) const SizedBox(height: 16),

                // Recent Income + Recent Expenses
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: _card(
                        title: 'Recent Income',
                        icon: Icons.arrow_downward_rounded,
                        iconColor: incomeGreen,
                        child: fin.recentIncome.isEmpty
                            ? Text('No income records', style: GoogleFonts.outfit(color: textSecondary, fontSize: 12))
                            : Column(
                                children: fin.recentIncome.map((i) => _recentIncomeTile(i as Map)).toList()),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _card(
                        title: 'Recent Expenses',
                        icon: Icons.arrow_upward_rounded,
                        iconColor: expenseRed,
                        child: fin.recentExpenses.isEmpty
                            ? Text('No expense records', style: GoogleFonts.outfit(color: textSecondary, fontSize: 12))
                            : Column(
                                children: fin.recentExpenses.map((e) => _recentExpenseTile(e as Map)).toList()),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Quick Actions
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [BoxShadow(color: primaryColor.withValues(alpha: 0.04), blurRadius: 10)],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Quick Actions', style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold, fontSize: 15, color: textPrimary)),
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          Expanded(child: _quickAction(
                            '+ Income', incomeGreen, Icons.add_rounded, true,
                            () => _showIncomeForm(ref.read(financeProvider)))),
                          const SizedBox(width: 8),
                          Expanded(child: _quickAction(
                            '+ Expense', expenseRed, Icons.remove_rounded, true,
                            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ExpensesScreen())))),
                          const SizedBox(width: 8),
                          Expanded(child: _quickAction(
                            'All Income', primaryColor, Icons.list_rounded, false,
                            () => _tabController.animateTo(1))),
                          const SizedBox(width: 8),
                          Expanded(child: _quickAction(
                            'Expenses', expenseRed, Icons.receipt_long_rounded, false,
                            () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ExpensesScreen())))),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
  }

  Widget _quickAction(String label, Color color, IconData icon, bool filled, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10),
        decoration: BoxDecoration(
          color: filled ? color : Colors.transparent,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: color),
        ),
        child: Column(
          children: [
            Icon(icon, color: filled ? Colors.white : color, size: 16),
            const SizedBox(height: 4),
            Text(label,
              textAlign: TextAlign.center,
              style: GoogleFonts.outfit(
                fontSize: 9.5,
                fontWeight: FontWeight.bold,
                color: filled ? Colors.white : color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ── TRANSACTIONS TAB (Income list) ─────────────────────────
  Widget _buildTransactionsTab(dynamic fin, dynamic exp) {
    return Column(
      children: [
        // Filter bar
        Container(
          margin: const EdgeInsets.fromLTRB(16, 8, 16, 4),
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: primaryColor.withValues(alpha: 0.08)),
          ),
          child: Row(
            children: [
              const Icon(Icons.apartment_rounded, color: textSecondary, size: 18),
              const SizedBox(width: 8),
              Expanded(
                child: DropdownButtonHideUnderline(
                  child: Consumer(builder: (ctx, ref, _) {
                    final f = ref.watch(financeProvider);
                    return DropdownButton<String>(
                      value: f.selectedPropertyId,
                      isExpanded: true,
                      icon: const Icon(Icons.keyboard_arrow_down_rounded, color: textSecondary, size: 18),
                      style: GoogleFonts.outfit(fontSize: 13, color: textPrimary, fontWeight: FontWeight.w600),
                      items: [
                        const DropdownMenuItem(value: 'all', child: Text('All Properties')),
                        ...f.properties.map((p) => DropdownMenuItem(value: p['id'].toString(), child: Text(p['name'] ?? ''))),
                      ],
                      onChanged: (v) { if (v != null) f.setPropertyFilter(v); },
                    );
                  }),
                ),
              ),
              Consumer(builder: (ctx, ref, _) {
                final f = ref.watch(financeProvider);
                return GestureDetector(
                  onTap: _selectDateRange,
                  child: Icon(Icons.calendar_month_rounded,
                    color: f.startDate != null ? primaryColor : textSecondary, size: 20),
                );
              }),
              if (fin.startDate != null || fin.selectedPropertyId != 'all') ...[
                const SizedBox(width: 6),
                GestureDetector(
                  onTap: () => ref.read(financeProvider).clearFilters(),
                  child: const Icon(Icons.filter_alt_off_rounded, color: textSecondary, size: 18),
                ),
              ],
            ],
          ),
        ),

        // KPI mini bar for filtered range
        Consumer(builder: (ctx, ref, _) {
          final f = ref.watch(financeProvider);
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: Row(
              children: [
                _miniKpi('Revenue', f.totalRevenue, incomeGreen),
                _miniKpi('Expenses', f.totalExpenses, expenseRed),
                _miniKpi('Profit', f.netProfit, profitBlue),
                _miniKpi('Outstanding', f.pendingReceivables, const Color(0xFFF59E0B)),
              ],
            ),
          );
        }),

        // List
        Expanded(
          child: Consumer(builder: (ctx, ref, _) {
            final f = ref.watch(financeProvider);
            if (f.isLoading && f.financeData == null) {
              return const Center(child: CircularProgressIndicator(color: primaryColor));
            }
            if (f.transactions.isEmpty) {
              return RefreshIndicator(
                onRefresh: () => f.fetchFinanceData(),
                child: ListView(children: [
                  const SizedBox(height: 60),
                  Center(child: Column(
                    children: [
                      const Icon(Icons.account_balance_rounded, size: 56, color: Color(0xFFD1DCD0)),
                      const SizedBox(height: 12),
                      Text('No income transactions', style: GoogleFonts.outfit(color: textSecondary)),
                    ],
                  )),
                ]),
              );
            }
            return RefreshIndicator(
              onRefresh: () => f.fetchFinanceData(),
              color: primaryColor,
              child: ListView.separated(
                padding: const EdgeInsets.fromLTRB(16, 4, 16, 100),
                itemCount: f.transactions.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (_, i) {
                  final tx = Map<String, dynamic>.from(f.transactions[i] as Map);
                  return GestureDetector(
                    onTap: () => _showIncomeForm(f, tx),
                    child: _buildTransactionTile(tx),
                  );
                },
              ),
            );
          }),
        ),
      ],
    );
  }

  Widget _miniKpi(String label, double value, Color color) {
    return Expanded(
      child: Container(
        margin: const EdgeInsets.only(right: 6),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.08),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: GoogleFonts.outfit(fontSize: 9, color: color, fontWeight: FontWeight.bold)),
            FittedBox(
              fit: BoxFit.scaleDown,
              alignment: Alignment.centerLeft,
              child: Text(_fmt(value),
                style: GoogleFonts.outfit(fontSize: 11, color: textPrimary, fontWeight: FontWeight.w800)),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTransactionTile(Map<String, dynamic> tx) {
    final amount = double.tryParse(tx['amount']?.toString() ?? '0') ?? 0.0;
    final paid = double.tryParse(tx['paid_amount']?.toString() ?? '0') ?? 0.0;
    final status = tx['payment_status']?.toString() ?? 'unpaid';
    final dateStr = tx['transaction_date']?.toString() ?? '';

    String fmtDate = '';
    try { fmtDate = DateFormat('d MMM').format(DateTime.parse(dateStr.split('T')[0])); } catch (_) { fmtDate = dateStr; }

    Color statusColor = status == 'paid' ? incomeGreen : status == 'partial' ? const Color(0xFFF59E0B) : expenseRed;
    final guest = tx['reservation']?['guest']?['name'] ?? 'Manual Entry';
    final accName = tx['accommodation']?['display_name'] ?? 'General';
    final propName = tx['property']?['name'] ?? '';

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: incomeGreen.withValues(alpha: 0.1)),
      ),
      child: Row(
        children: [
          Container(
            width: 42, height: 42,
            decoration: BoxDecoration(
              color: statusColor.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              status == 'paid' ? Icons.check_circle_rounded
                  : status == 'partial' ? Icons.remove_circle_rounded
                  : Icons.cancel_rounded,
              color: statusColor, size: 20,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(guest, style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 14, color: textPrimary)),
                Text(
                  propName.isNotEmpty ? '$propName · $accName' : accName,
                  style: GoogleFonts.outfit(fontSize: 11, color: textSecondary),
                  overflow: TextOverflow.ellipsis,
                ),
                if ((tx['notes'] ?? '').toString().isNotEmpty)
                  Text(tx['notes'].toString(), maxLines: 1, overflow: TextOverflow.ellipsis,
                    style: GoogleFonts.outfit(fontSize: 10, color: textSecondary, fontStyle: FontStyle.italic)),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(_fmt(paid), style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 14, color: incomeGreen)),
              if (amount > paid)
                Text('Bal: ${_fmt(amount - paid)}',
                  style: GoogleFonts.outfit(fontSize: 9, color: expenseRed, fontWeight: FontWeight.bold)),
              Text(fmtDate, style: GoogleFonts.outfit(fontSize: 10, color: textSecondary)),
            ],
          ),
        ],
      ),
    );
  }

  // ── Date range picker for transactions ─────────────────────
  Future<void> _selectDateRange() async {
    final f = ref.read(financeProvider);
    final picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: const ColorScheme.light(primary: primaryColor, onPrimary: Colors.white),
        ),
        child: child!,
      ),
    );
    if (picked != null) f.setDateFilter(picked.start, picked.end);
  }

  // ── Income form ────────────────────────────────────────────
  void _showIncomeForm(dynamic fin, [Map<String, dynamic>? tx]) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: const Color(0xFFF2F5F0),
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (_) => _IncomeForm(
        transaction: tx,
        financeProperties: fin.properties,
        onSave: (data) async {
          bool ok;
          if (tx != null) {
            ok = await fin.updateIncomeRecord(tx['id'], data);
          } else {
            ok = await fin.createIncomeRecord(data);
          }
          if (ok && mounted) {
            Navigator.pop(context);
            ScaffoldMessenger.of(context).showSnackBar(SnackBar(
              content: Text(tx != null ? 'Income updated' : 'Income added'), backgroundColor: Colors.green));
          } else if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(SnackBar(
              content: Text(fin.error ?? 'Error'), backgroundColor: Colors.red));
          }
        },
        onDelete: tx != null
            ? () async {
                final confirm = await showDialog<bool>(
                  context: context,
                  builder: (_) => AlertDialog(
                    title: Text('Delete?', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
                    content: const Text('This income record will be deleted.'),
                    actions: [
                      TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('Cancel')),
                      TextButton(
                        onPressed: () => Navigator.pop(context, true),
                        child: Text('Delete', style: GoogleFonts.outfit(color: Colors.red, fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                );
                if (confirm == true) {
                  final ok = await fin.deleteIncomeRecord(tx['id']);
                  if (ok && mounted) {
                    Navigator.pop(context);
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Deleted'), backgroundColor: Colors.red));
                  }
                }
              }
            : null,
      ),
    );
  }
}

// ============================================================
// INCOME FORM (keep existing logic, moved here)
// ============================================================
class _IncomeForm extends StatefulWidget {
  final Map<String, dynamic>? transaction;
  final List<dynamic> financeProperties;
  final Function(Map<String, dynamic>) onSave;
  final VoidCallback? onDelete;

  const _IncomeForm({
    super.key,
    this.transaction,
    required this.financeProperties,
    required this.onSave,
    this.onDelete,
  });

  @override
  State<_IncomeForm> createState() => _IncomeFormState();
}

class _IncomeFormState extends State<_IncomeForm> {
  static const Color primaryColor = Color(0xFF2E3E2A);
  static const Color textPrimary = Color(0xFF191D19);
  static const Color textSecondary = Color(0xFF5A7251);
  static const Color incomeGreen = Color(0xFF10B981);

  final _formKey = GlobalKey<FormState>();
  String? _selectedPropertyId;
  String? _selectedAccommodationId;
  String? _selectedIncomeType;
  String? _selectedPaymentStatus;
  late TextEditingController _amountCtrl;
  late TextEditingController _paidAmountCtrl;
  late TextEditingController _referenceCtrl;
  late TextEditingController _notesCtrl;
  late DateTime _selectedDate;

  @override
  void initState() {
    super.initState();
    final tx = widget.transaction;
    _selectedPropertyId = tx?['property_id']?.toString() ??
        (widget.financeProperties.isNotEmpty ? widget.financeProperties.first['id'].toString() : null);
    _selectedAccommodationId = tx?['accommodation_id']?.toString();
    _selectedIncomeType = tx?['income_type'] ?? 'booking';
    _selectedPaymentStatus = tx?['payment_status'] ?? 'paid';
    _amountCtrl = TextEditingController(text: tx?['amount']?.toString() ?? '');
    _paidAmountCtrl = TextEditingController(text: tx?['paid_amount']?.toString() ?? '');
    _referenceCtrl = TextEditingController(text: tx?['reference_number'] ?? '');
    _notesCtrl = TextEditingController(text: tx?['notes'] ?? '');
    final d = tx?['transaction_date'];
    _selectedDate = d != null ? DateTime.parse(d.toString().split('T')[0]) : DateTime.now();
  }

  @override
  void dispose() {
    _amountCtrl.dispose(); _paidAmountCtrl.dispose();
    _referenceCtrl.dispose(); _notesCtrl.dispose();
    super.dispose();
  }

  List<dynamic> get _accommodations {
    if (_selectedPropertyId == null) return [];
    try {
      final p = widget.financeProperties.firstWhere(
        (p) => p['id'].toString() == _selectedPropertyId, orElse: () => null);
      return p?['accommodations'] as List? ?? [];
    } catch (_) { return []; }
  }

  Widget _field(String label, TextEditingController ctrl,
      {TextInputType? type, String? Function(String?)? validator, int maxLines = 1}) =>
    TextFormField(
      controller: ctrl, keyboardType: type, maxLines: maxLines, validator: validator,
      style: GoogleFonts.outfit(color: textPrimary, fontWeight: FontWeight.w600, fontSize: 13),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: GoogleFonts.outfit(color: textSecondary, fontSize: 12),
        filled: true, fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryColor, width: 1.5)),
      ),
    );

  Widget _drop<T>(String label, T? value, List<DropdownMenuItem<T>> items, void Function(T?) onChanged) =>
    DropdownButtonFormField<T>(
      value: value, items: items, onChanged: onChanged,
      style: GoogleFonts.outfit(color: textPrimary, fontWeight: FontWeight.w600, fontSize: 13),
      decoration: InputDecoration(
        labelText: label, labelStyle: GoogleFonts.outfit(color: textSecondary, fontSize: 12),
        filled: true, fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryColor, width: 1.5)),
      ),
      dropdownColor: Colors.white, isExpanded: true,
    );

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.transaction != null;
    return Padding(
      padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom, left: 20, right: 20, top: 20),
      child: Form(
        key: _formKey,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(isEditing ? 'Edit Income' : 'Add Income',
                    style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: textPrimary)),
                  if (isEditing && widget.onDelete != null)
                    IconButton(icon: const Icon(Icons.delete_forever_rounded, color: Colors.red), onPressed: widget.onDelete),
                ],
              ),
              const SizedBox(height: 16),

              _drop('Property *', _selectedPropertyId,
                widget.financeProperties.map((p) => DropdownMenuItem<String>(
                  value: p['id'].toString(), child: Text(p['name'] ?? ''))).toList(),
                (v) => setState(() { _selectedPropertyId = v; _selectedAccommodationId = null; })),
              const SizedBox(height: 12),

              _drop('Accommodation', _selectedAccommodationId, [
                const DropdownMenuItem<String>(value: null, child: Text('General')),
                ..._accommodations.map((a) => DropdownMenuItem<String>(
                  value: a['id'].toString(), child: Text(a['display_name'] ?? ''))),
              ], (v) => setState(() => _selectedAccommodationId = v)),
              const SizedBox(height: 12),

              _drop('Income Type *', _selectedIncomeType, const [
                DropdownMenuItem(value: 'booking', child: Text('Booking Revenue')),
                DropdownMenuItem(value: 'rental', child: Text('Rental Income')),
                DropdownMenuItem(value: 'service', child: Text('Service Charge')),
                DropdownMenuItem(value: 'deposit', child: Text('Security Deposit')),
                DropdownMenuItem(value: 'penalty', child: Text('Penalty / Late Fee')),
                DropdownMenuItem(value: 'commission', child: Text('Commission')),
                DropdownMenuItem(value: 'other', child: Text('Other')),
              ], (v) => setState(() => _selectedIncomeType = v)),
              const SizedBox(height: 12),

              _field('Amount *', _amountCtrl, type: TextInputType.number,
                validator: (v) => (v == null || v.isEmpty || double.tryParse(v) == null) ? 'Enter valid amount' : null),
              const SizedBox(height: 12),

              _drop('Payment Status *', _selectedPaymentStatus, const [
                DropdownMenuItem(value: 'paid', child: Text('Fully Paid')),
                DropdownMenuItem(value: 'partial', child: Text('Partially Paid')),
                DropdownMenuItem(value: 'unpaid', child: Text('Unpaid')),
              ], (v) => setState(() => _selectedPaymentStatus = v)),
              const SizedBox(height: 12),

              if (_selectedPaymentStatus == 'partial') ...[
                _field('Paid Amount *', _paidAmountCtrl, type: TextInputType.number,
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Required';
                    final p = double.tryParse(v); final t = double.tryParse(_amountCtrl.text);
                    if (p == null || p < 0) return 'Invalid';
                    if (t != null && p >= t) return 'Must be less than total';
                    return null;
                  }),
                const SizedBox(height: 12),
              ],

              GestureDetector(
                onTap: () async {
                  final picked = await showDatePicker(
                    context: context, initialDate: _selectedDate,
                    firstDate: DateTime(2020), lastDate: DateTime.now().add(const Duration(days: 365)));
                  if (picked != null) setState(() => _selectedDate = picked);
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: primaryColor.withValues(alpha: 0.12))),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Date: ${DateFormat('dd MMM yyyy').format(_selectedDate)}',
                        style: GoogleFonts.outfit(fontWeight: FontWeight.w600, color: textPrimary, fontSize: 13)),
                      const Icon(Icons.calendar_today_rounded, color: Color(0xFF5A7251), size: 18),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 12),

              _field('Reference Number', _referenceCtrl),
              const SizedBox(height: 12),
              _field('Notes', _notesCtrl, maxLines: 2),
              const SizedBox(height: 24),

              Row(
                children: [
                  Expanded(child: TextButton(onPressed: () => Navigator.pop(context),
                    child: Text('Cancel', style: GoogleFonts.outfit(color: textSecondary, fontWeight: FontWeight.bold)))),
                  const SizedBox(width: 8),
                  Expanded(
                    flex: 2,
                    child: ElevatedButton(
                      onPressed: () {
                        if (_formKey.currentState!.validate()) {
                          widget.onSave({
                            'property_id': int.parse(_selectedPropertyId!),
                            'accommodation_id': _selectedAccommodationId != null ? int.parse(_selectedAccommodationId!) : null,
                            'income_type': _selectedIncomeType,
                            'amount': double.parse(_amountCtrl.text),
                            'payment_status': _selectedPaymentStatus,
                            'paid_amount': _selectedPaymentStatus == 'paid'
                                ? double.parse(_amountCtrl.text)
                                : (_selectedPaymentStatus == 'unpaid' ? 0.0
                                    : double.tryParse(_paidAmountCtrl.text) ?? 0.0),
                            'transaction_date': DateFormat('yyyy-MM-dd').format(_selectedDate),
                            'reference_number': _referenceCtrl.text.trim().isEmpty ? null : _referenceCtrl.text.trim(),
                            'notes': _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
                          });
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: incomeGreen,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                      child: Text('Save Income', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }
}
