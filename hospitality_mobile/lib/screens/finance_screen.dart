import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/riverpod_providers.dart';
import '../providers/finance_provider.dart';
import 'expenses_screen.dart';

class FinanceScreen extends ConsumerStatefulWidget {
  const FinanceScreen({super.key});

  @override
  ConsumerState<FinanceScreen> createState() => _FinanceScreenState();
}

class _FinanceScreenState extends ConsumerState<FinanceScreen>
    with SingleTickerProviderStateMixin {
  static const Color primaryColor = Color(0xFF2E3E2A);
  static const Color textPrimary = Color(0xFF191D19);
  static const Color textSecondary = Color(0xFF5A7251);
  static const Color backgroundColor = Color(0xFFF2F5F0);
  static const Color incomeGreen = Color(0xFF10B981);
  static const Color expenseRed = Color(0xFFEF4444);
  static const Color profitBlue = Color(0xFF3B82F6);
  static const Color marginPurple = Color(0xFF8B5CF6);

  late TabController _tabController;

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

  String _fmt(double v) => '₹${NumberFormat('#,##,###.##').format(v)}';

  String _fmtDate(String? d) {
    if (d == null) return '';
    try {
      return DateFormat('d MMM').format(DateTime.parse(d.split('T')[0]));
    } catch (_) {
      return d;
    }
  }

  Color _parseHex(String? hex) {
    if (hex == null) return textSecondary;
    try {
      return Color(int.parse('FF${hex.replaceAll('#', '')}', radix: 16));
    } catch (_) {
      return textSecondary;
    }
  }

  String _incomeTypeLabel(String? t) {
    switch (t) {
      case 'booking': return 'Booking';
      case 'rental': return 'Rental';
      case 'service': return 'Service';
      case 'deposit': return 'Deposit';
      case 'penalty': return 'Penalty';
      case 'commission': return 'Commission';
      default: return 'Other';
    }
  }

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
        title: Text('Finance',
            style: GoogleFonts.outfit(
                fontWeight: FontWeight.bold,
                color: textPrimary,
                fontSize: 20)),
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
          labelStyle:
              GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 13),
          tabs: const [Tab(text: 'Dashboard'), Tab(text: 'Transactions')],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: [
          _buildDashboard(fin),
          _buildTransactions(fin),
        ],
      ),
      floatingActionButton: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          FloatingActionButton.small(
            heroTag: 'fab_expenses',
            onPressed: () => Navigator.push(context,
                MaterialPageRoute(builder: (_) => const ExpensesScreen())),
            backgroundColor: expenseRed,
            tooltip: 'Manage Expenses',
            child: const Icon(Icons.receipt_long_rounded,
                color: Colors.white, size: 18),
          ),
          const SizedBox(height: 8),
          FloatingActionButton(
            heroTag: 'fab_income',
            onPressed: () => _showIncomeForm(fin),
            backgroundColor: primaryColor,
            tooltip: 'Add Income',
            child: const Icon(Icons.add_rounded, color: Colors.white),
          ),
        ],
      ),
    );
  }

  // ──────────────────────────────────────────────────────────
  // DASHBOARD TAB
  // ──────────────────────────────────────────────────────────
  Widget _buildDashboard(FinanceProvider fin) {
    if (fin.isSummaryLoading && fin.summaryData == null) {
      return const Center(child: CircularProgressIndicator(color: primaryColor));
    }

    return RefreshIndicator(
      onRefresh: () => fin.fetchSummary(),
      color: primaryColor,
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
        children: [
          // Period + label
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                fin.periodLabel.isNotEmpty ? fin.periodLabel : 'This Month',
                style: GoogleFonts.outfit(
                    fontSize: 13,
                    color: textSecondary,
                    fontWeight: FontWeight.w600),
              ),
              _periodSelector(fin),
            ],
          ),
          const SizedBox(height: 12),

          // Property filter
          if (fin.summaryProperties.isNotEmpty)
            _propertyDropdown(fin),
          const SizedBox(height: 14),

          // 4 KPI cards
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            childAspectRatio: 1.35,
            children: [
              _kpiCard('Total Income', fin.summaryRevenue,
                  Icons.account_balance_wallet_rounded, incomeGreen),
              _kpiCard('Total Expenses', fin.summaryExpenses,
                  Icons.payments_rounded, expenseRed),
              _kpiCard('Net Profit', fin.summaryNetProfit,
                  Icons.trending_up_rounded, profitBlue),
              _kpiCard('Profit Margin', fin.summaryProfitMargin,
                  Icons.pie_chart_rounded, marginPurple,
                  isPercent: true),
            ],
          ),
          const SizedBox(height: 16),

          // Accommodation Performance
          _sectionCard(
            title: 'Accommodation Performance',
            icon: Icons.home_work_rounded,
            iconColor: marginPurple,
            child: _buildAccommodationTable(fin.accommodationPerformance),
          ),
          const SizedBox(height: 16),

          // Income by Type (visual bars)
          if (fin.incomeByType.isNotEmpty) ...[
            _sectionCard(
              title: 'Income by Type',
              icon: Icons.donut_large_rounded,
              iconColor: incomeGreen,
              child: _buildIncomeTypeBreakdown(fin.incomeByType),
            ),
            const SizedBox(height: 16),
          ],

          // Recent Income + Expenses
          _sectionCard(
            title: 'Recent Income',
            icon: Icons.arrow_downward_rounded,
            iconColor: incomeGreen,
            child: _buildRecentIncomeList(fin.recentIncome),
          ),
          const SizedBox(height: 12),
          _sectionCard(
            title: 'Recent Expenses',
            icon: Icons.arrow_upward_rounded,
            iconColor: expenseRed,
            child: _buildRecentExpenseList(fin.recentExpenses),
          ),
          const SizedBox(height: 16),

          // Quick Actions
          _buildQuickActions(fin),
        ],
      ),
    );
  }

  Widget _periodSelector(FinanceProvider fin) {
    return Row(
      children: <Widget>[
        for (final p in ['day', 'week', 'month'])
          GestureDetector(
            onTap: () => fin.setPeriod(p),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              padding:
                  const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
              margin: const EdgeInsets.only(right: 6),
              decoration: BoxDecoration(
                color: fin.period == p ? primaryColor : Colors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: fin.period == p
                      ? primaryColor
                      : Colors.grey.shade300,
                ),
              ),
              child: Text(
                p[0].toUpperCase() + p.substring(1),
                style: GoogleFonts.outfit(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: fin.period == p ? Colors.white : textSecondary,
                ),
              ),
            ),
          ),
      ],
    );
  }

  Widget _propertyDropdown(FinanceProvider fin) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: primaryColor.withOpacity(0.1)),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: fin.selectedPropertyId,
          isExpanded: true,
          icon: const Icon(Icons.keyboard_arrow_down_rounded,
              color: textSecondary),
          style: GoogleFonts.outfit(
              fontWeight: FontWeight.w600,
              color: textPrimary,
              fontSize: 13),
          items: <DropdownMenuItem<String>>[
            const DropdownMenuItem<String>(
                value: 'all', child: Text('All Properties')),
            for (final p in fin.summaryProperties)
              DropdownMenuItem<String>(
                value: (p['id'] as Object).toString(),
                child: Text((p['name'] ?? '') as String),
              ),
          ],
          onChanged: (v) {
            if (v != null) fin.setSummaryProperty(v);
          },
        ),
      ),
    );
  }

  Widget _kpiCard(String title, double value, IconData icon, Color color,
      {bool isPercent = false}) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [color, color.withOpacity(0.75)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
              color: color.withOpacity(0.28),
              blurRadius: 12,
              offset: const Offset(0, 5)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.25),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: Colors.white, size: 18),
          ),
          const Spacer(),
          Text(title,
              style: GoogleFonts.outfit(
                  color: Colors.white.withOpacity(0.85),
                  fontSize: 11,
                  fontWeight: FontWeight.w600)),
          const SizedBox(height: 2),
          FittedBox(
            fit: BoxFit.scaleDown,
            alignment: Alignment.centerLeft,
            child: Text(
              isPercent
                  ? '${value.toStringAsFixed(1)}%'
                  : _fmt(value),
              style: GoogleFonts.outfit(
                  color: Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.w900),
            ),
          ),
        ],
      ),
    );
  }

  Widget _sectionCard({
    required String title,
    required Widget child,
    IconData? icon,
    Color? iconColor,
  }) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
              color: primaryColor.withOpacity(0.04),
              blurRadius: 12,
              offset: const Offset(0, 4)),
        ],
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
                      color: (iconColor ?? primaryColor).withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(icon,
                        color: iconColor ?? primaryColor, size: 16),
                  ),
                  const SizedBox(width: 8),
                ],
                Text(title,
                    style: GoogleFonts.outfit(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: iconColor ?? primaryColor)),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: child,
          ),
        ],
      ),
    );
  }

  Widget _buildAccommodationTable(List<dynamic> rows) {
    if (rows.isEmpty) {
      return Text('No accommodation data',
          style: GoogleFonts.outfit(color: textSecondary, fontSize: 12));
    }
    final headers = <Widget>[
      for (final h in ['Room', 'Income', 'Expenses', 'Profit', 'Share'])
        Expanded(
          child: Text(h,
              textAlign: TextAlign.center,
              style: GoogleFonts.outfit(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  color: textSecondary)),
        ),
    ];

    final rowWidgets = <Widget>[];
    for (final acc in rows) {
      final m = acc as Map;
      final income = (m['income'] as num).toDouble();
      final expenses = (m['expenses'] as num).toDouble();
      final profit = (m['net_contribution'] as num).toDouble();
      final share = (m['share'] as num?)?.toDouble() ?? 0.0;
      rowWidgets.add(Padding(
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: Row(
          children: [
            Expanded(
              child: Text(
                (m['name'] ?? '') as String,
                style: GoogleFonts.outfit(
                    fontWeight: FontWeight.bold,
                    fontSize: 11,
                    color: textPrimary),
                overflow: TextOverflow.ellipsis,
              ),
            ),
            Expanded(
              child: Text(_fmt(income),
                  textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(
                      fontSize: 10,
                      color: incomeGreen,
                      fontWeight: FontWeight.bold)),
            ),
            Expanded(
              child: Text(_fmt(expenses),
                  textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(
                      fontSize: 10,
                      color: expenseRed,
                      fontWeight: FontWeight.bold)),
            ),
            Expanded(
              child: Text(_fmt(profit),
                  textAlign: TextAlign.center,
                  style: GoogleFonts.outfit(
                      fontSize: 10,
                      color: profit >= 0 ? profitBlue : expenseRed,
                      fontWeight: FontWeight.bold)),
            ),
            Expanded(
              child: Container(
                alignment: Alignment.center,
                padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 2),
                decoration: BoxDecoration(
                  color: marginPurple.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  '${share.toStringAsFixed(0)}%',
                  style: GoogleFonts.outfit(
                      fontSize: 10,
                      color: marginPurple,
                      fontWeight: FontWeight.bold),
                ),
              ),
            ),
          ],
        ),
      ));
      rowWidgets.add(Divider(height: 1, color: Colors.grey.shade100));
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: <Widget>[
        Row(children: headers),
        const Divider(height: 1),
        ...rowWidgets,
      ],
    );
  }

  Widget _buildIncomeTypeBreakdown(List<dynamic> data) {
    final total = data.fold<double>(
        0, (s, e) => s + ((e as Map)['total'] as num).toDouble());
    final colors = <Color>[
      incomeGreen, profitBlue, marginPurple,
      const Color(0xFFF59E0B), expenseRed, const Color(0xFF06B6D4),
    ];

    final items = <Widget>[];
    for (int i = 0; i < data.length; i++) {
      final d = data[i] as Map;
      final val = (d['total'] as num).toDouble();
      final pct = total > 0 ? val / total : 0.0;
      final color = colors[i % colors.length];
      items.add(Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(children: [
                Container(
                    width: 8,
                    height: 8,
                    decoration: BoxDecoration(
                        color: color, shape: BoxShape.circle)),
                const SizedBox(width: 6),
                Text(_incomeTypeLabel(d['type']?.toString()),
                    style: GoogleFonts.outfit(
                        fontSize: 12,
                        color: textPrimary,
                        fontWeight: FontWeight.w600)),
              ]),
              Text(
                '${(pct * 100).toStringAsFixed(0)}% · ${_fmt(val)}',
                style: GoogleFonts.outfit(
                    fontSize: 11,
                    color: textSecondary,
                    fontWeight: FontWeight.w600),
              ),
            ],
          ),
          const SizedBox(height: 6),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: pct,
              backgroundColor: color.withOpacity(0.1),
              valueColor: AlwaysStoppedAnimation<Color>(color),
              minHeight: 6,
            ),
          ),
          const SizedBox(height: 10),
        ],
      ));
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: items,
    );
  }

  Widget _buildRecentIncomeList(List<dynamic> list) {
    if (list.isEmpty) {
      return Text('No income records',
          style: GoogleFonts.outfit(color: textSecondary, fontSize: 12));
    }
    final tiles = <Widget>[];
    for (final item in list) {
      final m = item as Map;
      final amount = (m['amount'] as num).toDouble();
      tiles.add(Container(
        margin: const EdgeInsets.only(bottom: 8),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        decoration: BoxDecoration(
          color: incomeGreen.withOpacity(0.07),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: incomeGreen.withOpacity(0.15)),
        ),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    _incomeTypeLabel(m['income_type']?.toString()),
                    style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                        color: textPrimary),
                  ),
                  Text(
                    '${m['accommodation'] ?? 'General'} · ${_fmtDate(m['transaction_date']?.toString())}',
                    style: GoogleFonts.outfit(
                        fontSize: 11, color: textSecondary),
                  ),
                ],
              ),
            ),
            Text(_fmt(amount),
                style: GoogleFonts.outfit(
                    fontWeight: FontWeight.bold,
                    fontSize: 13,
                    color: incomeGreen)),
          ],
        ),
      ));
    }
    return Column(
        crossAxisAlignment: CrossAxisAlignment.start, children: tiles);
  }

  Widget _buildRecentExpenseList(List<dynamic> list) {
    if (list.isEmpty) {
      return Text('No expense records',
          style: GoogleFonts.outfit(color: textSecondary, fontSize: 12));
    }
    final tiles = <Widget>[];
    for (final item in list) {
      final m = item as Map;
      final amount = (m['amount'] as num).toDouble();
      final catColor = _parseHex(m['category_color']?.toString());
      tiles.add(Container(
        margin: const EdgeInsets.only(bottom: 8),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
        decoration: BoxDecoration(
          color: expenseRed.withOpacity(0.05),
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: expenseRed.withOpacity(0.12)),
        ),
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    (m['title'] ?? 'Expense') as String,
                    style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                        color: textPrimary),
                  ),
                  Row(children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 5, vertical: 1),
                      decoration: BoxDecoration(
                        color: catColor.withOpacity(0.12),
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(
                        (m['category'] ?? 'Other') as String,
                        style: GoogleFonts.outfit(
                            fontSize: 9,
                            color: catColor,
                            fontWeight: FontWeight.bold),
                      ),
                    ),
                    const SizedBox(width: 4),
                    Text(
                      _fmtDate(m['transaction_date']?.toString()),
                      style: GoogleFonts.outfit(
                          fontSize: 11, color: textSecondary),
                    ),
                  ]),
                ],
              ),
            ),
            Text(_fmt(amount),
                style: GoogleFonts.outfit(
                    fontWeight: FontWeight.bold,
                    fontSize: 13,
                    color: expenseRed)),
          ],
        ),
      ));
    }
    return Column(
        crossAxisAlignment: CrossAxisAlignment.start, children: tiles);
  }

  Widget _buildQuickActions(FinanceProvider fin) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
              color: primaryColor.withOpacity(0.04), blurRadius: 10),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('Quick Actions',
              style: GoogleFonts.outfit(
                  fontWeight: FontWeight.bold,
                  fontSize: 15,
                  color: textPrimary)),
          const SizedBox(height: 12),
          Row(
            children: [
              _quickAction('+ Income', incomeGreen,
                  Icons.add_rounded, true, () => _showIncomeForm(fin)),
              const SizedBox(width: 8),
              _quickAction(
                  '+ Expense',
                  expenseRed,
                  Icons.remove_rounded,
                  true,
                  () => Navigator.push(context,
                      MaterialPageRoute(
                          builder: (_) => const ExpensesScreen()))),
              const SizedBox(width: 8),
              _quickAction(
                  'All Income',
                  primaryColor,
                  Icons.list_rounded,
                  false,
                  () => _tabController.animateTo(1)),
              const SizedBox(width: 8),
              _quickAction(
                  'Expenses',
                  expenseRed,
                  Icons.receipt_long_rounded,
                  false,
                  () => Navigator.push(context,
                      MaterialPageRoute(
                          builder: (_) => const ExpensesScreen()))),
            ],
          ),
        ],
      ),
    );
  }

  Widget _quickAction(
      String label, Color color, IconData icon, bool filled, VoidCallback onTap) {
    return Expanded(
      child: GestureDetector(
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
                      color: filled ? Colors.white : color)),
            ],
          ),
        ),
      ),
    );
  }

  // ──────────────────────────────────────────────────────────
  // TRANSACTIONS TAB
  // ──────────────────────────────────────────────────────────
  Widget _buildTransactions(FinanceProvider fin) {
    return Column(
      children: [
        // Filter bar
        Container(
          margin: const EdgeInsets.fromLTRB(16, 8, 16, 4),
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: primaryColor.withOpacity(0.08)),
          ),
          child: Row(
            children: [
              const Icon(Icons.apartment_rounded, color: textSecondary, size: 18),
              const SizedBox(width: 8),
              Expanded(
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: fin.selectedPropertyId,
                    isExpanded: true,
                    icon: const Icon(Icons.keyboard_arrow_down_rounded,
                        color: textSecondary, size: 18),
                    style: GoogleFonts.outfit(
                        fontSize: 13,
                        color: textPrimary,
                        fontWeight: FontWeight.w600),
                    items: <DropdownMenuItem<String>>[
                      const DropdownMenuItem<String>(
                          value: 'all', child: Text('All Properties')),
                      for (final p in fin.properties)
                        DropdownMenuItem<String>(
                          value: (p['id'] as Object).toString(),
                          child: Text((p['name'] ?? '') as String),
                        ),
                    ],
                    onChanged: (v) {
                      if (v != null) fin.setPropertyFilter(v);
                    },
                  ),
                ),
              ),
              GestureDetector(
                onTap: () => _selectDateRange(fin),
                child: Icon(Icons.calendar_month_rounded,
                    color: fin.startDate != null
                        ? primaryColor
                        : textSecondary,
                    size: 20),
              ),
              if (fin.startDate != null ||
                  fin.selectedPropertyId != 'all') ...[
                const SizedBox(width: 6),
                GestureDetector(
                  onTap: fin.clearFilters,
                  child: const Icon(Icons.filter_alt_off_rounded,
                      color: textSecondary, size: 18),
                ),
              ],
            ],
          ),
        ),

        // Mini KPI strip
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
          child: Row(
            children: [
              _miniKpi('Revenue', fin.totalRevenue, incomeGreen),
              _miniKpi('Expenses', fin.totalExpenses, expenseRed),
              _miniKpi('Profit', fin.netProfit, profitBlue),
              _miniKpi('Outstanding', fin.pendingReceivables,
                  const Color(0xFFF59E0B)),
            ],
          ),
        ),

        // List
        Expanded(
          child: fin.isLoading && fin.financeData == null
              ? const Center(
                  child: CircularProgressIndicator(color: primaryColor))
              : RefreshIndicator(
                  onRefresh: () => fin.fetchFinanceData(),
                  color: primaryColor,
                  child: fin.transactions.isEmpty
                      ? ListView(children: [
                          const SizedBox(height: 60),
                          Center(
                            child: Column(children: [
                              const Icon(Icons.account_balance_rounded,
                                  size: 56, color: Color(0xFFD1DCD0)),
                              const SizedBox(height: 12),
                              Text('No income transactions',
                                  style: GoogleFonts.outfit(
                                      color: textSecondary)),
                            ]),
                          ),
                        ])
                      : ListView.separated(
                          padding: const EdgeInsets.fromLTRB(16, 4, 16, 100),
                          itemCount: fin.transactions.length,
                          separatorBuilder: (_, __) =>
                              const SizedBox(height: 10),
                          itemBuilder: (_, i) {
                            final tx = Map<String, dynamic>.from(
                                fin.transactions[i] as Map);
                            return GestureDetector(
                              onTap: () => _showIncomeForm(fin, tx),
                              child: _buildTransactionTile(tx),
                            );
                          },
                        ),
                ),
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
          color: color.withOpacity(0.08),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label,
                style: GoogleFonts.outfit(
                    fontSize: 9, color: color, fontWeight: FontWeight.bold)),
            FittedBox(
              fit: BoxFit.scaleDown,
              alignment: Alignment.centerLeft,
              child: Text(_fmt(value),
                  style: GoogleFonts.outfit(
                      fontSize: 11,
                      color: textPrimary,
                      fontWeight: FontWeight.w800)),
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
    try {
      fmtDate = DateFormat('d MMM')
          .format(DateTime.parse(dateStr.split('T')[0]));
    } catch (_) {
      fmtDate = dateStr;
    }

    final statusColor = status == 'paid'
        ? incomeGreen
        : status == 'partial'
            ? const Color(0xFFF59E0B)
            : expenseRed;
    final guest = tx['reservation']?['guest']?['name'] ?? 'Manual Entry';
    final accName = tx['accommodation']?['display_name'] ?? 'General';
    final propName = tx['property']?['name'] ?? '';

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: incomeGreen.withOpacity(0.1)),
      ),
      child: Row(
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              status == 'paid'
                  ? Icons.check_circle_rounded
                  : status == 'partial'
                      ? Icons.remove_circle_rounded
                      : Icons.cancel_rounded,
              color: statusColor,
              size: 20,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(guest,
                    style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                        color: textPrimary)),
                Text(
                  propName.isNotEmpty ? '$propName · $accName' : accName,
                  style: GoogleFonts.outfit(
                      fontSize: 11, color: textSecondary),
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(_fmt(paid),
                  style: GoogleFonts.outfit(
                      fontWeight: FontWeight.bold,
                      fontSize: 14,
                      color: incomeGreen)),
              if (amount > paid)
                Text('Bal: ${_fmt(amount - paid)}',
                    style: GoogleFonts.outfit(
                        fontSize: 9,
                        color: expenseRed,
                        fontWeight: FontWeight.bold)),
              Text(fmtDate,
                  style: GoogleFonts.outfit(
                      fontSize: 10, color: textSecondary)),
            ],
          ),
        ],
      ),
    );
  }

  Future<void> _selectDateRange(FinanceProvider fin) async {
    final picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (ctx, child) => Theme(
        data: Theme.of(ctx).copyWith(
          colorScheme: const ColorScheme.light(
              primary: primaryColor, onPrimary: Colors.white),
        ),
        child: child!,
      ),
    );
    if (picked != null) fin.setDateFilter(picked.start, picked.end);
  }

  // ──────────────────────────────────────────────────────────
  // INCOME FORM
  // ──────────────────────────────────────────────────────────
  void _showIncomeForm(FinanceProvider fin, [Map<String, dynamic>? tx]) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: backgroundColor,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (_) => _IncomeForm(
        transaction: tx,
        properties: fin.properties,
        onSave: (data) async {
          final ok = tx != null
              ? await fin.updateIncomeRecord(tx['id'] as int, data)
              : await fin.createIncomeRecord(data);
          if (!mounted) return;
          Navigator.pop(context);
          ScaffoldMessenger.of(context).showSnackBar(SnackBar(
            content:
                Text(ok ? (tx != null ? 'Updated' : 'Saved') : fin.error ?? 'Error'),
            backgroundColor: ok ? Colors.green : Colors.red,
          ));
        },
        onDelete: tx != null
            ? () async {
                Navigator.pop(context);
                final ok =
                    await fin.deleteIncomeRecord(tx['id'] as int);
                if (!mounted) return;
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                  content: Text(ok ? 'Deleted' : fin.error ?? 'Error'),
                  backgroundColor: ok ? Colors.red : Colors.orange,
                ));
              }
            : null,
      ),
    );
  }
}

// ============================================================
// INCOME FORM
// ============================================================
class _IncomeForm extends StatefulWidget {
  final Map<String, dynamic>? transaction;
  final List<dynamic> properties;
  final Function(Map<String, dynamic>) onSave;
  final VoidCallback? onDelete;

  const _IncomeForm({
    super.key,
    this.transaction,
    required this.properties,
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
  String _incomeType = 'booking';
  String _paymentStatus = 'paid';
  late TextEditingController _amountCtrl;
  late TextEditingController _paidCtrl;
  late TextEditingController _refCtrl;
  late TextEditingController _notesCtrl;
  late DateTime _date;

  @override
  void initState() {
    super.initState();
    final tx = widget.transaction;
    _selectedPropertyId = tx?['property_id']?.toString() ??
        (widget.properties.isNotEmpty
            ? (widget.properties.first['id'] as Object).toString()
            : null);
    _selectedAccommodationId = tx?['accommodation_id']?.toString();
    _incomeType = tx?['income_type']?.toString() ?? 'booking';
    _paymentStatus = tx?['payment_status']?.toString() ?? 'paid';
    _amountCtrl = TextEditingController(
        text: tx?['amount']?.toString() ?? '');
    _paidCtrl = TextEditingController(
        text: tx?['paid_amount']?.toString() ?? '');
    _refCtrl = TextEditingController(
        text: tx?['reference_number']?.toString() ?? '');
    _notesCtrl =
        TextEditingController(text: tx?['notes']?.toString() ?? '');
    final d = tx?['transaction_date'];
    _date = d != null
        ? DateTime.parse(d.toString().split('T')[0])
        : DateTime.now();
  }

  @override
  void dispose() {
    _amountCtrl.dispose();
    _paidCtrl.dispose();
    _refCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  List<dynamic> get _accommodations {
    if (_selectedPropertyId == null) return [];
    try {
      final p = widget.properties.firstWhere(
        (p) => (p['id'] as Object).toString() == _selectedPropertyId,
        orElse: () => null,
      );
      return (p?['accommodations'] as List?) ?? [];
    } catch (_) {
      return [];
    }
  }

  InputDecoration _dec(String label) => InputDecoration(
        labelText: label,
        labelStyle: GoogleFonts.outfit(color: textSecondary, fontSize: 12),
        filled: true,
        fillColor: Colors.white,
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide:
                BorderSide(color: primaryColor.withOpacity(0.12))),
        enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide:
                BorderSide(color: primaryColor.withOpacity(0.12))),
        focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide:
                const BorderSide(color: primaryColor, width: 1.5)),
      );

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.transaction != null;
    return Padding(
      padding: EdgeInsets.only(
          bottom: MediaQuery.of(context).viewInsets.bottom,
          left: 20,
          right: 20,
          top: 20),
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
                      style: GoogleFonts.outfit(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: textPrimary)),
                  if (isEditing && widget.onDelete != null)
                    IconButton(
                        icon: const Icon(Icons.delete_forever_rounded,
                            color: Colors.red),
                        onPressed: widget.onDelete),
                ],
              ),
              const SizedBox(height: 16),
              // Property
              DropdownButtonFormField<String>(
                value: _selectedPropertyId,
                decoration: _dec('Property *'),
                style: GoogleFonts.outfit(
                    color: textPrimary,
                    fontWeight: FontWeight.w600,
                    fontSize: 13),
                dropdownColor: Colors.white,
                isExpanded: true,
                items: <DropdownMenuItem<String>>[
                  for (final p in widget.properties)
                    DropdownMenuItem<String>(
                      value: (p['id'] as Object).toString(),
                      child: Text((p['name'] ?? '') as String),
                    ),
                ],
                onChanged: (v) => setState(() {
                  _selectedPropertyId = v;
                  _selectedAccommodationId = null;
                }),
                validator: (v) => v == null ? 'Required' : null,
              ),
              const SizedBox(height: 12),
              // Accommodation
              DropdownButtonFormField<String>(
                value: _selectedAccommodationId,
                decoration: _dec('Accommodation'),
                style: GoogleFonts.outfit(
                    color: textPrimary,
                    fontWeight: FontWeight.w600,
                    fontSize: 13),
                dropdownColor: Colors.white,
                isExpanded: true,
                items: <DropdownMenuItem<String>>[
                  const DropdownMenuItem<String>(
                      value: null, child: Text('General')),
                  for (final a in _accommodations)
                    DropdownMenuItem<String>(
                      value: (a['id'] as Object).toString(),
                      child: Text((a['display_name'] ?? '') as String),
                    ),
                ],
                onChanged: (v) =>
                    setState(() => _selectedAccommodationId = v),
              ),
              const SizedBox(height: 12),
              // Income Type
              DropdownButtonFormField<String>(
                value: _incomeType,
                decoration: _dec('Income Type *'),
                style: GoogleFonts.outfit(
                    color: textPrimary,
                    fontWeight: FontWeight.w600,
                    fontSize: 13),
                dropdownColor: Colors.white,
                isExpanded: true,
                items: const <DropdownMenuItem<String>>[
                  DropdownMenuItem(value: 'booking', child: Text('Booking Revenue')),
                  DropdownMenuItem(value: 'rental', child: Text('Rental Income')),
                  DropdownMenuItem(value: 'service', child: Text('Service Charge')),
                  DropdownMenuItem(value: 'deposit', child: Text('Security Deposit')),
                  DropdownMenuItem(value: 'penalty', child: Text('Penalty')),
                  DropdownMenuItem(value: 'commission', child: Text('Commission')),
                  DropdownMenuItem(value: 'other', child: Text('Other')),
                ],
                onChanged: (v) =>
                    setState(() => _incomeType = v ?? 'booking'),
              ),
              const SizedBox(height: 12),
              // Amount
              TextFormField(
                controller: _amountCtrl,
                keyboardType: TextInputType.number,
                style: GoogleFonts.outfit(
                    color: textPrimary,
                    fontWeight: FontWeight.w600,
                    fontSize: 13),
                decoration: _dec('Amount *'),
                validator: (v) =>
                    (v == null || v.isEmpty || double.tryParse(v) == null)
                        ? 'Enter valid amount'
                        : null,
              ),
              const SizedBox(height: 12),
              // Payment Status
              DropdownButtonFormField<String>(
                value: _paymentStatus,
                decoration: _dec('Payment Status *'),
                style: GoogleFonts.outfit(
                    color: textPrimary,
                    fontWeight: FontWeight.w600,
                    fontSize: 13),
                dropdownColor: Colors.white,
                isExpanded: true,
                items: const <DropdownMenuItem<String>>[
                  DropdownMenuItem(value: 'paid', child: Text('Fully Paid')),
                  DropdownMenuItem(value: 'partial', child: Text('Partially Paid')),
                  DropdownMenuItem(value: 'unpaid', child: Text('Unpaid')),
                ],
                onChanged: (v) =>
                    setState(() => _paymentStatus = v ?? 'paid'),
              ),
              if (_paymentStatus == 'partial') ...[
                const SizedBox(height: 12),
                TextFormField(
                  controller: _paidCtrl,
                  keyboardType: TextInputType.number,
                  style: GoogleFonts.outfit(
                      color: textPrimary,
                      fontWeight: FontWeight.w600,
                      fontSize: 13),
                  decoration: _dec('Paid Amount *'),
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Required';
                    final p = double.tryParse(v);
                    final t = double.tryParse(_amountCtrl.text);
                    if (p == null || p < 0) return 'Invalid';
                    if (t != null && p >= t) return 'Must be less than total';
                    return null;
                  },
                ),
              ],
              const SizedBox(height: 12),
              // Date
              GestureDetector(
                onTap: () async {
                  final picked = await showDatePicker(
                    context: context,
                    initialDate: _date,
                    firstDate: DateTime(2020),
                    lastDate: DateTime.now()
                        .add(const Duration(days: 365)),
                  );
                  if (picked != null) setState(() => _date = picked);
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 14, vertical: 14),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                        color: primaryColor.withOpacity(0.12)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Date: ${DateFormat('dd MMM yyyy').format(_date)}',
                        style: GoogleFonts.outfit(
                            fontWeight: FontWeight.w600,
                            color: textPrimary,
                            fontSize: 13),
                      ),
                      const Icon(Icons.calendar_today_rounded,
                          color: textSecondary, size: 18),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _refCtrl,
                style: GoogleFonts.outfit(
                    color: textPrimary,
                    fontWeight: FontWeight.w600,
                    fontSize: 13),
                decoration: _dec('Reference Number'),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _notesCtrl,
                maxLines: 2,
                style: GoogleFonts.outfit(
                    color: textPrimary,
                    fontWeight: FontWeight.w600,
                    fontSize: 13),
                decoration: _dec('Notes'),
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: Text('Cancel',
                          style: GoogleFonts.outfit(
                              color: textSecondary,
                              fontWeight: FontWeight.bold)),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    flex: 2,
                    child: ElevatedButton(
                      onPressed: () {
                        if (_formKey.currentState!.validate()) {
                          widget.onSave({
                            'property_id':
                                int.parse(_selectedPropertyId!),
                            'accommodation_id':
                                _selectedAccommodationId != null
                                    ? int.parse(
                                        _selectedAccommodationId!)
                                    : null,
                            'income_type': _incomeType,
                            'amount': double.parse(_amountCtrl.text),
                            'payment_status': _paymentStatus,
                            'paid_amount': _paymentStatus == 'paid'
                                ? double.parse(_amountCtrl.text)
                                : _paymentStatus == 'unpaid'
                                    ? 0.0
                                    : double.tryParse(_paidCtrl.text) ??
                                        0.0,
                            'transaction_date': DateFormat('yyyy-MM-dd')
                                .format(_date),
                            'reference_number':
                                _refCtrl.text.trim().isEmpty
                                    ? null
                                    : _refCtrl.text.trim(),
                            'notes': _notesCtrl.text.trim().isEmpty
                                ? null
                                : _notesCtrl.text.trim(),
                          });
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: incomeGreen,
                        foregroundColor: Colors.white,
                        padding:
                            const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14)),
                      ),
                      child: Text('Save Income',
                          style: GoogleFonts.outfit(
                              fontWeight: FontWeight.bold)),
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
