import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/riverpod_providers.dart';

class FinanceScreen extends ConsumerStatefulWidget {
  const FinanceScreen({super.key});

  @override
  ConsumerState<FinanceScreen> createState() => _FinanceScreenState();
}

class _FinanceScreenState extends ConsumerState<FinanceScreen> {
  static const Color primaryColor = Color(0xFF2E3E2A); // Deep organic green
  static const Color textPrimary = Color(0xFF191D19); // Charcoal
  static const Color textSecondary = Color(0xFF5A7251); // Soft green
  static const Color backgroundColor = Color(0xFFF2F5F0); // Warm cream

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      ref.read(financeProvider).fetchFinanceData();
    });
  }

  Future<void> _selectDateRange() async {
    final provider = ref.read(financeProvider);
    final initialRange = provider.startDate != null && provider.endDate != null
        ? DateTimeRange(start: provider.startDate!, end: provider.endDate!)
        : null;

    final picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      initialDateRange: initialRange,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: primaryColor,
              onPrimary: Colors.white,
              surface: backgroundColor,
              onSurface: textPrimary,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      provider.setDateFilter(picked.start, picked.end);
    }
  }

  @override
  Widget build(BuildContext context) {
    final finance = ref.watch(financeProvider);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        title: Text(
          'Finance & Revenue',
          style: GoogleFonts.outfit(
            fontWeight: FontWeight.bold,
            color: textPrimary,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        actions: [
          if (finance.selectedPropertyId != 'all' || finance.startDate != null)
            IconButton(
              icon: const Icon(Icons.filter_alt_off_rounded, color: textPrimary),
              onPressed: () => finance.clearFilters(),
              tooltip: 'Clear Filters',
            ),
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: textPrimary),
            onPressed: () => finance.fetchFinanceData(),
          ),
        ],
      ),
      body: finance.isLoading && finance.financeData == null
          ? const Center(child: CircularProgressIndicator(color: primaryColor))
          : RefreshIndicator(
              onRefresh: () => finance.fetchFinanceData(),
              color: primaryColor,
              child: ListView(
                padding: const EdgeInsets.all(20),
                children: [
                  // KPI Cards
                  Row(
                    children: [
                      Expanded(
                        child: _buildKpiCard(
                          title: 'Total Revenue',
                          value: '₹${NumberFormat('#,##,###.##').format(finance.totalRevenue)}',
                          icon: Icons.account_balance_wallet_rounded,
                          color: const Color(0xFF10B981), // Emerald Green
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: _buildKpiCard(
                          title: 'Receivables',
                          value: '₹${NumberFormat('#,##,###.##').format(finance.pendingReceivables)}',
                          icon: Icons.pending_actions_rounded,
                          color: const Color(0xFFF59E0B), // Amber/Orange
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Filters Header
                  Text(
                    'Filters',
                    style: GoogleFonts.outfit(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: textPrimary,
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Filters Container
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: primaryColor.withOpacity(0.08), width: 1.5),
                    ),
                    child: Column(
                      children: [
                        // Property Filter Dropdown
                        Row(
                          children: [
                            const Icon(Icons.apartment_rounded, color: textSecondary, size: 20),
                            const SizedBox(width: 12),
                            Expanded(
                              child: DropdownButtonHideUnderline(
                                child: DropdownButton<String>(
                                  value: finance.selectedPropertyId,
                                  isExpanded: true,
                                  icon: const Icon(Icons.keyboard_arrow_down_rounded, color: textSecondary),
                                  style: GoogleFonts.outfit(
                                    fontWeight: FontWeight.w600,
                                    color: textPrimary,
                                    fontSize: 14,
                                  ),
                                  items: [
                                    const DropdownMenuItem(
                                      value: 'all',
                                      child: Text('All Properties'),
                                    ),
                                    ...finance.properties.map((p) {
                                      return DropdownMenuItem(
                                        value: p['id'].toString(),
                                        child: Text(p['name'] ?? 'Property'),
                                      );
                                    }),
                                  ],
                                  onChanged: (val) {
                                    if (val != null) {
                                      finance.setPropertyFilter(val);
                                    }
                                  },
                                ),
                              ),
                            ),
                          ],
                        ),
                        const Divider(height: 20, thickness: 1),
                        
                        // Date Filter Button
                        GestureDetector(
                          onTap: _selectDateRange,
                          child: Row(
                            children: [
                              const Icon(Icons.calendar_month_rounded, color: textSecondary, size: 20),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Text(
                                  finance.startDate != null && finance.endDate != null
                                      ? '${DateFormat('d MMM yyyy').format(finance.startDate!)} - ${DateFormat('d MMM yyyy').format(finance.endDate!)}'
                                      : 'All Dates / Select Date Range',
                                  style: GoogleFonts.outfit(
                                    fontWeight: FontWeight.w600,
                                    color: finance.startDate != null ? textPrimary : textSecondary,
                                    fontSize: 14,
                                  ),
                                ),
                              ),
                              const Icon(Icons.chevron_right_rounded, color: textSecondary),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 32),

                  // Recent Transactions Header
                  Text(
                    'Recent Transactions',
                    style: GoogleFonts.outfit(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: textPrimary,
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Transactions List
                  if (finance.isLoading)
                    const Center(
                      child: Padding(
                        padding: EdgeInsets.all(24.0),
                        child: CircularProgressIndicator(color: primaryColor),
                      ),
                    )
                  else if (finance.transactions.isEmpty)
                    _buildEmptyState('No transactions found matching filters')
                  else
                    ListView.separated(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: finance.transactions.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        final tx = finance.transactions[index];
                        return _buildTransactionTile(tx);
                      },
                    ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
    );
  }

  Widget _buildKpiCard({
    required String title,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: primaryColor.withOpacity(0.08), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: primaryColor.withOpacity(0.02),
            blurRadius: 15,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 22),
          ),
          const SizedBox(height: 16),
          Text(
            title,
            style: GoogleFonts.outfit(
              color: textSecondary,
              fontSize: 13,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 4),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              value,
              style: GoogleFonts.outfit(
                color: textPrimary,
                fontSize: 20,
                fontWeight: FontWeight.w800,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTransactionTile(Map<String, dynamic> tx) {
    final amount = double.tryParse(tx['amount']?.toString() ?? '0') ?? 0.0;
    final paidAmount = double.tryParse(tx['paid_amount']?.toString() ?? '0') ?? 0.0;
    final dateStr = tx['transaction_date'] ?? '';
    final notes = tx['notes'] ?? '';
    final paymentStatus = tx['payment_status']?.toString().toLowerCase() ?? 'unpaid';

    String formattedDate = '';
    if (dateStr.isNotEmpty) {
      try {
        final parsed = DateTime.parse(dateStr.split('T')[0]);
        formattedDate = DateFormat('d MMM yyyy').format(parsed);
      } catch (_) {
        formattedDate = dateStr;
      }
    }

    Color statusColor;
    IconData statusIcon;
    if (paymentStatus == 'paid') {
      statusColor = const Color(0xFF10B981);
      statusIcon = Icons.check_circle_rounded;
    } else if (paymentStatus == 'partial') {
      statusColor = const Color(0xFFF59E0B);
      statusIcon = Icons.remove_circle_rounded;
    } else {
      statusColor = const Color(0xFFEF4444);
      statusIcon = Icons.cancel_rounded;
    }

    final guestName = tx['reservation']?['guest']?['name'] ?? 'Other Income';
    final accommodationName = tx['accommodation']?['display_name'] ?? 'General';
    final propertyName = tx['property']?['name'] ?? '';

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: primaryColor.withOpacity(0.05)),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(statusIcon, color: statusColor, size: 20),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  guestName,
                  style: GoogleFonts.outfit(
                    fontWeight: FontWeight.bold,
                    fontSize: 15,
                    color: textPrimary,
                  ),
                ),
                Text(
                  propertyName.isNotEmpty ? '$propertyName • $accommodationName' : accommodationName,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.outfit(
                    fontSize: 12,
                    color: textSecondary,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                if (notes.isNotEmpty)
                  Padding(
                    padding: const EdgeInsets.only(top: 4),
                    child: Text(
                      notes,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: GoogleFonts.outfit(
                        fontSize: 11,
                        color: textSecondary.withOpacity(0.8),
                        fontStyle: FontStyle.italic,
                      ),
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                '₹${NumberFormat('#,##,###.##').format(paidAmount)}',
                style: GoogleFonts.outfit(
                  fontWeight: FontWeight.bold,
                  fontSize: 15,
                  color: textPrimary,
                ),
              ),
              if (amount > paidAmount)
                Text(
                  'Bal: ₹${(amount - paidAmount).toStringAsFixed(2)}',
                  style: GoogleFonts.outfit(
                    fontSize: 10,
                    color: const Color(0xFFEF4444),
                    fontWeight: FontWeight.bold,
                  ),
                ),
              Text(
                formattedDate,
                style: GoogleFonts.outfit(
                  fontSize: 11,
                  color: textSecondary,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState(String message) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32.0),
        child: Column(
          children: [
            const Icon(Icons.account_balance_rounded, size: 48, color: Color(0xFFD1DCD0)),
            const SizedBox(height: 12),
            Text(
              message,
              textAlign: TextAlign.center,
              style: GoogleFonts.outfit(color: textSecondary, fontSize: 14),
            ),
          ],
        ),
      ),
    );
  }
}
