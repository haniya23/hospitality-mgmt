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

  void _showTransactionForm([Map<String, dynamic>? transaction]) {
    final finance = ref.read(financeProvider);
    final isEditing = transaction != null;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: backgroundColor,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (context) => _TransactionForm(
        transaction: transaction,
        financeProperties: finance.properties,
        onSave: (data) async {
          bool success;
          if (isEditing) {
            success = await finance.updateIncomeRecord(transaction['id'], data);
          } else {
            success = await finance.createIncomeRecord(data);
          }
          if (success && mounted) {
            Navigator.pop(context);
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(isEditing ? 'Transaction updated' : 'Transaction created'),
                backgroundColor: Colors.green,
              ),
            );
          } else if (mounted) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(finance.error ?? 'An error occurred'),
                backgroundColor: Colors.red,
              ),
            );
          }
        },
        onDelete: isEditing
            ? () async {
                final confirm = await showDialog<bool>(
                  context: context,
                  builder: (context) => AlertDialog(
                    title: Text('Delete Transaction', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
                    content: const Text('Are you sure you want to delete this transaction record?'),
                    actions: [
                      TextButton(
                        onPressed: () => Navigator.pop(context, false),
                        child: Text('Cancel', style: GoogleFonts.outfit(color: textSecondary)),
                      ),
                      TextButton(
                        onPressed: () => Navigator.pop(context, true),
                        child: Text('Delete', style: GoogleFonts.outfit(color: Colors.red, fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ),
                );

                if (confirm == true) {
                  final success = await finance.deleteIncomeRecord(transaction['id']);
                  if (success && mounted) {
                    Navigator.pop(context); // Close bottom sheet
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Transaction deleted'), backgroundColor: Colors.red),
                    );
                  }
                }
              }
            : null,
      ),
    );
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
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Recent Transactions',
                        style: GoogleFonts.outfit(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: textPrimary,
                        ),
                      ),
                      if (finance.properties.isNotEmpty)
                        IconButton(
                          icon: const Icon(Icons.add_circle_rounded, color: primaryColor, size: 28),
                          onPressed: () => _showTransactionForm(),
                          tooltip: 'Add Transaction',
                        ),
                    ],
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
                        final tx = Map<String, dynamic>.from(finance.transactions[index] as Map);
                        return GestureDetector(
                          onTap: () => _showTransactionForm(tx),
                          child: _buildTransactionTile(tx),
                        );
                      },
                    ),
                  const SizedBox(height: 80),
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

class _TransactionForm extends StatefulWidget {
  final Map<String, dynamic>? transaction;
  final List<dynamic> financeProperties;
  final Function(Map<String, dynamic>) onSave;
  final VoidCallback? onDelete;

  const _TransactionForm({
    super.key,
    this.transaction,
    required this.financeProperties,
    required this.onSave,
    this.onDelete,
  });

  @override
  State<_TransactionForm> createState() => _TransactionFormState();
}

class _TransactionFormState extends State<_TransactionForm> {
  final _formKey = GlobalKey<FormState>();

  String? _selectedPropertyId;
  String? _selectedAccommodationId;
  String? _selectedIncomeType;
  String? _selectedPaymentStatus;
  
  late TextEditingController _amountController;
  late TextEditingController _paidAmountController;
  late TextEditingController _referenceController;
  late TextEditingController _notesController;
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
    
    _amountController = TextEditingController(text: tx?['amount']?.toString() ?? '');
    _paidAmountController = TextEditingController(text: tx?['paid_amount']?.toString() ?? '');
    _referenceController = TextEditingController(text: tx?['reference_number'] ?? '');
    _notesController = TextEditingController(text: tx?['notes'] ?? '');
    
    final dateStr = tx?['transaction_date'];
    _selectedDate = dateStr != null ? DateTime.parse(dateStr) : DateTime.now();
  }

  @override
  void dispose() {
    _amountController.dispose();
    _paidAmountController.dispose();
    _referenceController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      setState(() {
        _selectedDate = picked;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.transaction != null;
    final selectedProp = widget.financeProperties.firstWhere(
      (p) => p['id'].toString() == _selectedPropertyId,
      orElse: () => null,
    );
    final accommodations = selectedProp != null ? selectedProp['accommodations'] as List : [];

    return Padding(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
        left: 24,
        right: 24,
        top: 24,
      ),
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
                  Text(
                    isEditing ? 'Edit Transaction' : 'Add Transaction',
                    style: GoogleFonts.outfit(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      color: const Color(0xFF191D19),
                    ),
                  ),
                  if (isEditing && widget.onDelete != null)
                    IconButton(
                      icon: const Icon(Icons.delete_forever_rounded, color: Colors.red),
                      onPressed: widget.onDelete,
                    ),
                ],
              ),
              const SizedBox(height: 20),

              // Property Dropdown
              _buildDropdown(
                label: 'Property *',
                value: _selectedPropertyId,
                items: widget.financeProperties.map((p) {
                  return DropdownMenuItem<String>(
                    value: p['id'].toString(),
                    child: Text(p['name'] ?? ''),
                  );
                }).toList(),
                onChanged: (val) {
                  setState(() {
                    _selectedPropertyId = val;
                    _selectedAccommodationId = null;
                  });
                },
              ),
              const SizedBox(height: 16),

              // Accommodation Dropdown
              _buildDropdown(
                label: 'Accommodation / Room',
                value: _selectedAccommodationId,
                items: [
                  const DropdownMenuItem<String>(value: null, child: Text('General (No room)')),
                  ...accommodations.map((a) {
                    return DropdownMenuItem<String>(
                      value: a['id'].toString(),
                      child: Text(a['display_name'] ?? ''),
                    );
                  }),
                ],
                onChanged: (val) => setState(() => _selectedAccommodationId = val),
              ),
              const SizedBox(height: 16),

              // Income Type
              _buildDropdown(
                label: 'Income Type *',
                value: _selectedIncomeType,
                items: const [
                  DropdownMenuItem(value: 'booking', child: Text('Booking Revenue')),
                  DropdownMenuItem(value: 'rental', child: Text('Rental Income')),
                  DropdownMenuItem(value: 'service', child: Text('Service Charge')),
                  DropdownMenuItem(value: 'deposit', child: Text('Security Deposit')),
                  DropdownMenuItem(value: 'penalty', child: Text('Penalty/Late Fee')),
                  DropdownMenuItem(value: 'commission', child: Text('Commission')),
                  DropdownMenuItem(value: 'other', child: Text('Other Income')),
                ],
                onChanged: (val) => setState(() => _selectedIncomeType = val),
              ),
              const SizedBox(height: 16),

              // Amount
              _buildTextField(
                label: 'Amount *',
                controller: _amountController,
                keyboardType: TextInputType.number,
                validator: (val) {
                  if (val == null || val.isEmpty) return 'Amount is required';
                  if (double.tryParse(val) == null || double.parse(val) <= 0) {
                    return 'Please enter a valid amount';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Payment Status
              _buildDropdown(
                label: 'Payment Status *',
                value: _selectedPaymentStatus,
                items: const [
                  DropdownMenuItem(value: 'paid', child: Text('Fully Paid')),
                  DropdownMenuItem(value: 'partial', child: Text('Partially Paid')),
                  DropdownMenuItem(value: 'unpaid', child: Text('Unpaid')),
                ],
                onChanged: (val) => setState(() => _selectedPaymentStatus = val),
              ),
              const SizedBox(height: 16),

              // Paid Amount (for partial payment status)
              if (_selectedPaymentStatus == 'partial') ...[
                _buildTextField(
                  label: 'Paid Amount *',
                  controller: _paidAmountController,
                  keyboardType: TextInputType.number,
                  validator: (val) {
                    if (val == null || val.isEmpty) return 'Paid Amount is required';
                    final parsedPaid = double.tryParse(val);
                    final parsedTotal = double.tryParse(_amountController.text);
                    if (parsedPaid == null || parsedPaid < 0) {
                      return 'Please enter a valid paid amount';
                    }
                    if (parsedTotal != null && parsedPaid >= parsedTotal) {
                      return 'Paid amount must be less than total amount';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),
              ],

              // Transaction Date
              GestureDetector(
                onTap: _pickDate,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFF2E3E2A).withOpacity(0.08)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'Date: ${DateFormat('yyyy-MM-dd').format(_selectedDate)}',
                        style: GoogleFonts.outfit(fontWeight: FontWeight.w600, color: const Color(0xFF191D19)),
                      ),
                      const Icon(Icons.calendar_today_rounded, color: Color(0xFF5A7251), size: 18),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Reference Number
              _buildTextField(
                label: 'Reference Number / TxID',
                controller: _referenceController,
              ),
              const SizedBox(height: 16),

              // Notes
              _buildTextField(
                label: 'Notes',
                controller: _notesController,
                maxLines: 2,
              ),
              const SizedBox(height: 24),

              // Action Buttons
              Row(
                children: [
                  Expanded(
                    child: TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: Text('Cancel', style: GoogleFonts.outfit(color: const Color(0xFF5A7251), fontWeight: FontWeight.bold)),
                    ),
                  ),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        if (_formKey.currentState!.validate()) {
                          final data = {
                            'property_id': int.parse(_selectedPropertyId!),
                            'accommodation_id': _selectedAccommodationId != null ? int.parse(_selectedAccommodationId!) : null,
                            'income_type': _selectedIncomeType,
                            'amount': double.parse(_amountController.text),
                            'payment_status': _selectedPaymentStatus,
                            'paid_amount': _selectedPaymentStatus == 'paid'
                                ? double.parse(_amountController.text)
                                : (_selectedPaymentStatus == 'unpaid' ? 0.0 : double.parse(_paidAmountController.text)),
                            'transaction_date': DateFormat('yyyy-MM-dd').format(_selectedDate),
                            'reference_number': _referenceController.text.trim(),
                            'notes': _notesController.text.trim(),
                          };
                          widget.onSave(data);
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF2E3E2A),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                      child: Text('Save', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
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

  Widget _buildDropdown({
    required String label,
    required dynamic value,
    required List<DropdownMenuItem<dynamic>> items,
    required ValueChanged<dynamic> onChanged,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.outfit(fontSize: 12, fontWeight: FontWeight.bold, color: const Color(0xFF5A7251)),
        ),
        const SizedBox(height: 6),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: const Color(0xFF2E3E2A).withOpacity(0.08)),
          ),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<dynamic>(
              value: value,
              isExpanded: true,
              style: GoogleFonts.outfit(color: const Color(0xFF191D19), fontWeight: FontWeight.w600),
              icon: const Icon(Icons.arrow_drop_down, color: Color(0xFF5A7251)),
              items: items,
              onChanged: onChanged,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildTextField({
    required String label,
    required TextEditingController controller,
    TextInputType? keyboardType,
    int maxLines = 1,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.outfit(fontSize: 12, fontWeight: FontWeight.bold, color: const Color(0xFF5A7251)),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          keyboardType: keyboardType,
          maxLines: maxLines,
          validator: validator,
          style: GoogleFonts.outfit(color: const Color(0xFF191D19), fontWeight: FontWeight.w600),
          decoration: InputDecoration(
            filled: true,
            fillColor: Colors.white,
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.08)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.08)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: Color(0xFF2E3E2A), width: 1.5),
            ),
          ),
        ),
      ],
    );
  }
}
