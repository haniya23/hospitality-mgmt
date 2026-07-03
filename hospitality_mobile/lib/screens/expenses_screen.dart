import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/riverpod_providers.dart';
import '../providers/finance_provider.dart';

class ExpensesScreen extends ConsumerStatefulWidget {
  const ExpensesScreen({super.key});

  @override
  ConsumerState<ExpensesScreen> createState() => _ExpensesScreenState();
}

class _ExpensesScreenState extends ConsumerState<ExpensesScreen> {
  static const Color primaryColor = Color(0xFF2E3E2A);
  static const Color textPrimary = Color(0xFF191D19);
  static const Color textSecondary = Color(0xFF5A7251);
  static const Color backgroundColor = Color(0xFFF2F5F0);
  static const Color expenseRed = Color(0xFFEF4444);

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      final exp = ref.read(expenseProvider);
      final fin = ref.read(financeProvider);
      exp.fetchCategories();
      exp.fetchExpenses();
      if (fin.properties.isEmpty) fin.fetchFinanceData();
    });
  }

  void _showExpenseForm([Map<String, dynamic>? expense]) {
    final exp = ref.read(expenseProvider);
    final fin = ref.read(financeProvider);
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: backgroundColor,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (_) => _ExpenseForm(
        expense: expense,
        properties: fin.properties,
        categories: exp.categories,
        onSave: (data) async {
          bool success;
          if (expense != null) {
            success = await exp.updateExpense(expense['id'], data);
          } else {
            success = await exp.createExpense(data);
          }
          if (success && mounted) {
            Navigator.pop(context);
            // Also refresh finance summary
            ref.read(financeProvider).fetchSummary();
            _snack(expense != null ? 'Expense updated' : 'Expense added', Colors.green);
          } else if (mounted) {
            _snack(exp.error ?? 'An error occurred', Colors.red);
          }
        },
        onDelete: expense != null
            ? () async {
                Navigator.pop(context);
                final ok = await exp.deleteExpense(expense['id']);
                if (ok && mounted) {
                  ref.read(financeProvider).fetchSummary();
                  _snack('Expense deleted', Colors.red);
                }
              }
            : null,
      ),
    );
  }

  void _snack(String msg, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(msg), backgroundColor: color),
    );
  }

  Future<void> _selectDateRange() async {
    final exp = ref.read(expenseProvider);
    final picked = await showDateRangePicker(
      context: context,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: const ColorScheme.light(primary: primaryColor, onPrimary: Colors.white),
        ),
        child: child!,
      ),
    );
    if (picked != null) exp.setDateFilter(picked.start, picked.end);
  }

  @override
  Widget build(BuildContext context) {
    final exp = ref.watch(expenseProvider);
    final fin = ref.watch(financeProvider);

    return Scaffold(
      backgroundColor: backgroundColor,
      appBar: AppBar(
        title: Text('Expenses', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: textPrimary)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        actions: [
          if (exp.hasActiveFilters)
            IconButton(
              icon: const Icon(Icons.filter_alt_off_rounded, color: textPrimary),
              onPressed: () => exp.clearFilters(),
            ),
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: textPrimary),
            onPressed: () => exp.fetchExpenses(),
          ),
          IconButton(
            icon: const Icon(Icons.add_circle_rounded, color: expenseRed, size: 28),
            onPressed: () => _showExpenseForm(),
          ),
        ],
      ),
      body: Column(
        children: [
          // Filters bar
          Container(
            margin: const EdgeInsets.fromLTRB(16, 0, 16, 8),
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: primaryColor.withValues(alpha: 0.08)),
            ),
            child: Row(
              children: [
                // Property filter
                Expanded(
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton<String>(
                      value: exp.selectedPropertyId,
                      isExpanded: true,
                      icon: const Icon(Icons.keyboard_arrow_down_rounded, color: textSecondary, size: 18),
                      style: GoogleFonts.outfit(fontSize: 13, color: textPrimary, fontWeight: FontWeight.w600),
                      items: [
                        const DropdownMenuItem(value: 'all', child: Text('All Properties')),
                        ...fin.properties.map((p) => DropdownMenuItem(
                          value: p['id'].toString(), child: Text(p['name'] ?? ''))),
                      ],
                      onChanged: (v) { if (v != null) exp.setPropertyFilter(v); },
                    ),
                  ),
                ),
                Container(width: 1, height: 24, color: Colors.grey.shade200, margin: const EdgeInsets.symmetric(horizontal: 8)),
                // Category filter
                Expanded(
                  child: DropdownButtonHideUnderline(
                    child: DropdownButton<String>(
                      value: exp.selectedCategoryId,
                      isExpanded: true,
                      hint: Text('All Categories', style: GoogleFonts.outfit(fontSize: 13, color: textSecondary)),
                      icon: const Icon(Icons.keyboard_arrow_down_rounded, color: textSecondary, size: 18),
                      style: GoogleFonts.outfit(fontSize: 13, color: textPrimary, fontWeight: FontWeight.w600),
                      items: [
                        const DropdownMenuItem<String>(value: null, child: Text('All Categories')),
                        ...exp.categories.map((c) => DropdownMenuItem<String>(
                          value: c['id'].toString(), child: Text(c['name'] ?? ''))),
                      ],
                      onChanged: (v) => exp.setCategoryFilter(v),
                    ),
                  ),
                ),
                GestureDetector(
                  onTap: _selectDateRange,
                  child: Icon(
                    Icons.calendar_month_rounded,
                    color: exp.startDate != null ? expenseRed : textSecondary,
                    size: 22,
                  ),
                ),
              ],
            ),
          ),

          // List
          Expanded(
            child: exp.isLoading && exp.expenses.isEmpty
                ? const Center(child: CircularProgressIndicator(color: expenseRed))
                : RefreshIndicator(
                    onRefresh: () => exp.fetchExpenses(),
                    color: expenseRed,
                    child: exp.expenses.isEmpty
                        ? ListView(children: [
                            const SizedBox(height: 80),
                            Center(
                              child: Column(
                                children: [
                                  const Icon(Icons.receipt_long_rounded, size: 56, color: Color(0xFFD1DCD0)),
                                  const SizedBox(height: 12),
                                  Text('No expenses found', style: GoogleFonts.outfit(color: textSecondary)),
                                ],
                              ),
                            ),
                          ])
                        : ListView.separated(
                            padding: const EdgeInsets.fromLTRB(16, 4, 16, 80),
                            itemCount: exp.expenses.length,
                            separatorBuilder: (_, __) => const SizedBox(height: 10),
                            itemBuilder: (context, i) {
                              final e = Map<String, dynamic>.from(exp.expenses[i] as Map);
                              return _buildExpenseTile(e);
                            },
                          ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildExpenseTile(Map<String, dynamic> e) {
    final amount = double.tryParse(e['amount']?.toString() ?? '0') ?? 0.0;
    final status = e['payment_status']?.toString() ?? 'paid';
    final dateStr = e['transaction_date']?.toString() ?? '';
    String formattedDate = '';
    try {
      formattedDate = DateFormat('d MMM yyyy').format(DateTime.parse(dateStr.split('T')[0]));
    } catch (_) {
      formattedDate = dateStr;
    }

    final categoryColor = _parseColor(e['category']?['color']);
    final categoryName = e['category']?['name'] ?? 'Other';
    final accommodationName = e['accommodation']?['display_name'];
    final title = e['title'] ?? 'Expense';
    final vendor = e['vendor_name'];

    Color statusColor;
    if (status == 'paid') statusColor = const Color(0xFF10B981);
    else if (status == 'partial') statusColor = const Color(0xFFF59E0B);
    else statusColor = expenseRed;

    return GestureDetector(
      onTap: () => _showExpenseForm(e),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: expenseRed.withValues(alpha: 0.08)),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: categoryColor.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(Icons.receipt_long_rounded, color: categoryColor, size: 22),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title,
                    style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 14, color: textPrimary)),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: categoryColor.withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(categoryName,
                          style: GoogleFonts.outfit(fontSize: 10, color: categoryColor, fontWeight: FontWeight.bold)),
                      ),
                      if (accommodationName != null) ...[
                        const SizedBox(width: 6),
                        Text('• $accommodationName',
                          style: GoogleFonts.outfit(fontSize: 11, color: textSecondary)),
                      ],
                    ],
                  ),
                  if (vendor != null && vendor.toString().isNotEmpty)
                    Text(vendor.toString(),
                      style: GoogleFonts.outfit(fontSize: 11, color: textSecondary, fontStyle: FontStyle.italic)),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text('₹${NumberFormat('#,##,###.##').format(amount)}',
                  style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 15, color: expenseRed)),
                Container(
                  margin: const EdgeInsets.only(top: 2),
                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                  decoration: BoxDecoration(
                    color: statusColor.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(status.toUpperCase(),
                    style: GoogleFonts.outfit(fontSize: 9, color: statusColor, fontWeight: FontWeight.bold)),
                ),
                Text(formattedDate,
                  style: GoogleFonts.outfit(fontSize: 11, color: textSecondary)),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Color _parseColor(String? hex) {
    if (hex == null) return textSecondary;
    try {
      return Color(int.parse('FF${hex.replaceAll('#', '')}', radix: 16));
    } catch (_) {
      return textSecondary;
    }
  }
}

// ============================================================
// EXPENSE FORM (Add / Edit)
// ============================================================
class _ExpenseForm extends StatefulWidget {
  final Map<String, dynamic>? expense;
  final List<dynamic> properties;
  final List<dynamic> categories;
  final Function(Map<String, dynamic>) onSave;
  final VoidCallback? onDelete;

  const _ExpenseForm({
    super.key,
    this.expense,
    required this.properties,
    required this.categories,
    required this.onSave,
    this.onDelete,
  });

  @override
  State<_ExpenseForm> createState() => _ExpenseFormState();
}

class _ExpenseFormState extends State<_ExpenseForm> {
  static const Color primaryColor = Color(0xFF2E3E2A);
  static const Color textPrimary = Color(0xFF191D19);
  static const Color textSecondary = Color(0xFF5A7251);
  static const Color expenseRed = Color(0xFFEF4444);

  final _formKey = GlobalKey<FormState>();
  late TextEditingController _titleCtrl;
  late TextEditingController _amountCtrl;
  late TextEditingController _paidAmountCtrl;
  late TextEditingController _vendorCtrl;
  late TextEditingController _receiptCtrl;
  late TextEditingController _notesCtrl;

  String? _selectedPropertyId;
  String? _selectedAccommodationId;
  String? _selectedCategoryId;
  String _paymentMethod = 'cash';
  String _paymentStatus = 'paid';
  DateTime _selectedDate = DateTime.now();
  bool _isRecurring = false;
  String? _recurringFrequency;

  @override
  void initState() {
    super.initState();
    final e = widget.expense;
    _selectedPropertyId = e?['property_id']?.toString() ??
        (widget.properties.isNotEmpty ? widget.properties.first['id'].toString() : null);
    _selectedAccommodationId = e?['accommodation_id']?.toString();
    _selectedCategoryId = e?['expense_category_id']?.toString() ??
        (widget.categories.isNotEmpty ? widget.categories.first['id'].toString() : null);
    _paymentMethod = e?['payment_method'] ?? 'cash';
    _paymentStatus = e?['payment_status'] ?? 'paid';
    _isRecurring = e?['is_recurring'] == true;
    _recurringFrequency = e?['recurring_frequency'];

    _titleCtrl = TextEditingController(text: e?['title'] ?? '');
    _amountCtrl = TextEditingController(text: e?['amount']?.toString() ?? '');
    _paidAmountCtrl = TextEditingController(text: e?['paid_amount']?.toString() ?? '');
    _vendorCtrl = TextEditingController(text: e?['vendor_name'] ?? '');
    _receiptCtrl = TextEditingController(text: e?['receipt_number'] ?? '');
    _notesCtrl = TextEditingController(text: e?['notes'] ?? '');

    final dateStr = e?['transaction_date'];
    if (dateStr != null) {
      try { _selectedDate = DateTime.parse(dateStr.split('T')[0]); } catch (_) {}
    }
  }

  @override
  void dispose() {
    _titleCtrl.dispose();
    _amountCtrl.dispose();
    _paidAmountCtrl.dispose();
    _vendorCtrl.dispose();
    _receiptCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  List<dynamic> get _accommodations {
    if (_selectedPropertyId == null) return [];
    try {
      final prop = widget.properties.firstWhere(
        (p) => p['id'].toString() == _selectedPropertyId, orElse: () => null);
      return prop?['accommodations'] as List? ?? [];
    } catch (_) { return []; }
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) setState(() => _selectedDate = picked);
  }

  void _submit() {
    if (!_formKey.currentState!.validate()) return;
    double paidAmt = 0;
    if (_paymentStatus == 'paid') {
      paidAmt = double.tryParse(_amountCtrl.text) ?? 0;
    } else if (_paymentStatus == 'partial') {
      paidAmt = double.tryParse(_paidAmountCtrl.text) ?? 0;
    }

    widget.onSave({
      'property_id': int.parse(_selectedPropertyId!),
      'accommodation_id': _selectedAccommodationId != null ? int.parse(_selectedAccommodationId!) : null,
      'expense_category_id': int.parse(_selectedCategoryId!),
      'title': _titleCtrl.text.trim(),
      'amount': double.parse(_amountCtrl.text),
      'paid_amount': paidAmt,
      'payment_method': _paymentMethod,
      'payment_status': _paymentStatus,
      'transaction_date': DateFormat('yyyy-MM-dd').format(_selectedDate),
      'vendor_name': _vendorCtrl.text.trim().isEmpty ? null : _vendorCtrl.text.trim(),
      'receipt_number': _receiptCtrl.text.trim().isEmpty ? null : _receiptCtrl.text.trim(),
      'notes': _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
      'is_recurring': _isRecurring,
      'recurring_frequency': _isRecurring ? _recurringFrequency : null,
    });
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.expense != null;
    return Padding(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
        left: 20, right: 20, top: 20,
      ),
      child: Form(
        key: _formKey,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    isEditing ? 'Edit Expense' : 'Add Expense',
                    style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold, color: textPrimary),
                  ),
                  if (isEditing && widget.onDelete != null)
                    IconButton(
                      icon: const Icon(Icons.delete_forever_rounded, color: Colors.red),
                      onPressed: widget.onDelete,
                    ),
                ],
              ),
              const SizedBox(height: 16),

              // Title
              _buildField(
                label: 'Expense Title *',
                controller: _titleCtrl,
                validator: (v) => v == null || v.isEmpty ? 'Title is required' : null,
              ),
              const SizedBox(height: 12),

              // Property
              _buildDropdown(
                label: 'Property *',
                value: _selectedPropertyId,
                items: widget.properties.map((p) =>
                  DropdownMenuItem<String>(value: p['id'].toString(), child: Text(p['name'] ?? ''))).toList(),
                onChanged: (v) => setState(() {
                  _selectedPropertyId = v;
                  _selectedAccommodationId = null;
                }),
              ),
              const SizedBox(height: 12),

              // Accommodation (optional)
              _buildDropdown(
                label: 'Accommodation (Optional)',
                value: _selectedAccommodationId,
                items: [
                  const DropdownMenuItem<String>(value: null, child: Text('General (No Room)')),
                  ..._accommodations.map((a) =>
                    DropdownMenuItem<String>(value: a['id'].toString(), child: Text(a['display_name'] ?? ''))),
                ],
                onChanged: (v) => setState(() => _selectedAccommodationId = v),
              ),
              const SizedBox(height: 12),

              // Category
              _buildDropdown(
                label: 'Category *',
                value: _selectedCategoryId,
                items: widget.categories.map((c) =>
                  DropdownMenuItem<String>(value: c['id'].toString(), child: Text(c['name'] ?? ''))).toList(),
                onChanged: (v) => setState(() => _selectedCategoryId = v),
                validator: (v) => v == null ? 'Category is required' : null,
              ),
              const SizedBox(height: 12),

              // Amount
              _buildField(
                label: 'Amount *',
                controller: _amountCtrl,
                keyboardType: TextInputType.number,
                validator: (v) {
                  if (v == null || v.isEmpty) return 'Amount required';
                  if (double.tryParse(v) == null || double.parse(v) <= 0) return 'Enter valid amount';
                  return null;
                },
              ),
              const SizedBox(height: 12),

              // Payment Method + Status (side by side)
              Row(
                children: [
                  Expanded(
                    child: _buildDropdown(
                      label: 'Method *',
                      value: _paymentMethod,
                      items: const [
                        DropdownMenuItem(value: 'cash', child: Text('Cash')),
                        DropdownMenuItem(value: 'card', child: Text('Card')),
                        DropdownMenuItem(value: 'upi', child: Text('UPI')),
                        DropdownMenuItem(value: 'bank_transfer', child: Text('Bank Transfer')),
                        DropdownMenuItem(value: 'cheque', child: Text('Cheque')),
                        DropdownMenuItem(value: 'other', child: Text('Other')),
                      ],
                      onChanged: (v) => setState(() => _paymentMethod = v ?? 'cash'),
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: _buildDropdown(
                      label: 'Status *',
                      value: _paymentStatus,
                      items: const [
                        DropdownMenuItem(value: 'paid', child: Text('Paid')),
                        DropdownMenuItem(value: 'partial', child: Text('Partial')),
                        DropdownMenuItem(value: 'unpaid', child: Text('Unpaid')),
                      ],
                      onChanged: (v) => setState(() => _paymentStatus = v ?? 'paid'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Paid amount if partial
              if (_paymentStatus == 'partial') ...[
                _buildField(
                  label: 'Paid Amount *',
                  controller: _paidAmountCtrl,
                  keyboardType: TextInputType.number,
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Paid amount required';
                    final p = double.tryParse(v);
                    final t = double.tryParse(_amountCtrl.text);
                    if (p == null || p < 0) return 'Invalid amount';
                    if (t != null && p >= t) return 'Must be less than total';
                    return null;
                  },
                ),
                const SizedBox(height: 12),
              ],

              // Date
              GestureDetector(
                onTap: _pickDate,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: primaryColor.withValues(alpha: 0.12)),
                  ),
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

              // Vendor
              _buildField(label: 'Vendor Name', controller: _vendorCtrl),
              const SizedBox(height: 12),

              // Receipt
              _buildField(label: 'Receipt/Ref Number', controller: _receiptCtrl),
              const SizedBox(height: 12),

              // Notes
              _buildField(label: 'Notes', controller: _notesCtrl, maxLines: 2),
              const SizedBox(height: 12),

              // Is Recurring
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: primaryColor.withValues(alpha: 0.10)),
                ),
                child: SwitchListTile(
                  title: Text('Recurring Expense',
                    style: GoogleFonts.outfit(fontWeight: FontWeight.w600, color: textPrimary, fontSize: 13)),
                  value: _isRecurring,
                  activeColor: primaryColor,
                  contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 0),
                  onChanged: (v) => setState(() => _isRecurring = v),
                ),
              ),

              if (_isRecurring) ...[
                const SizedBox(height: 12),
                _buildDropdown(
                  label: 'Recurring Frequency',
                  value: _recurringFrequency,
                  items: const [
                    DropdownMenuItem(value: 'daily', child: Text('Daily')),
                    DropdownMenuItem(value: 'weekly', child: Text('Weekly')),
                    DropdownMenuItem(value: 'monthly', child: Text('Monthly')),
                    DropdownMenuItem(value: 'quarterly', child: Text('Quarterly')),
                    DropdownMenuItem(value: 'yearly', child: Text('Yearly')),
                  ],
                  onChanged: (v) => setState(() => _recurringFrequency = v),
                ),
              ],

              const SizedBox(height: 24),

              // Buttons
              Row(
                children: [
                  Expanded(
                    child: TextButton(
                      onPressed: () => Navigator.pop(context),
                      child: Text('Cancel',
                        style: GoogleFonts.outfit(color: textSecondary, fontWeight: FontWeight.bold)),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    flex: 2,
                    child: ElevatedButton(
                      onPressed: _submit,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: expenseRed,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                      child: Text('Save Expense', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
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

  Widget _buildField({
    required String label,
    required TextEditingController controller,
    TextInputType? keyboardType,
    String? Function(String?)? validator,
    int maxLines = 1,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      maxLines: maxLines,
      validator: validator,
      style: GoogleFonts.outfit(color: textPrimary, fontWeight: FontWeight.w600, fontSize: 13),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: GoogleFonts.outfit(color: textSecondary, fontSize: 12),
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryColor, width: 1.5)),
      ),
    );
  }

  Widget _buildDropdown<T>({
    required String label,
    required T value,
    required List<DropdownMenuItem<T>> items,
    required void Function(T?) onChanged,
    String? Function(T?)? validator,
  }) {
    return DropdownButtonFormField<T>(
      value: value,
      items: items,
      onChanged: onChanged,
      validator: validator,
      style: GoogleFonts.outfit(color: textPrimary, fontWeight: FontWeight.w600, fontSize: 13),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: GoogleFonts.outfit(color: textSecondary, fontSize: 12),
        filled: true,
        fillColor: Colors.white,
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: primaryColor.withValues(alpha: 0.12))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: primaryColor, width: 1.5)),
      ),
      dropdownColor: Colors.white,
      isExpanded: true,
    );
  }
}
