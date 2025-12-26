import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/riverpod_providers.dart';
import '../providers/booking_provider.dart';

class CheckOutFormScreen extends ConsumerStatefulWidget {
  final Map<String, dynamic> booking;

  const CheckOutFormScreen({super.key, required this.booking});

  @override
  ConsumerState<CheckOutFormScreen> createState() => _CheckOutFormScreenState();
}

class _CheckOutFormScreenState extends ConsumerState<CheckOutFormScreen> {
  final _formKey = GlobalKey<FormState>();

  // State
  late TextEditingController _guestNameController;
  late TextEditingController _roomNumberController;
  late TextEditingController _checkOutTimeController;
  
  // Services
  final List<String> _servicesUsed = [];
  final Map<String, String> _availableServices = {
    'restaurant': 'Restaurant',
    'spa': 'Spa',
    'minibar': 'Minibar',
    'transport': 'Transport',
    'laundry': 'Laundry',
    'room_service': 'Room Service',
    'parking': 'Parking',
    'wifi': 'WiFi'
  };

  // Charges
  late TextEditingController _lateChargesController;
  late TextEditingController _serviceNotesController;
  late TextEditingController _finalBillController;
  late TextEditingController _depositRefundController;
  
  String? _paymentStatus;
  late TextEditingController _paymentNotesController;

  // Feedback
  int _rating = 0;
  late TextEditingController _feedbackController;

  double _originalTotal = 0.0;
  double _balancePending = 0.0;
  late TextEditingController _amountCollectedController;

  @override
  void initState() {
    super.initState();
    final guest = widget.booking['guest'] ?? {};
    final accommodation = widget.booking['accommodation'] ?? {};
    _originalTotal = double.tryParse(widget.booking['total_amount']?.toString() ?? '0') ?? 0.0;

    _guestNameController = TextEditingController(text: guest['name']);
    _roomNumberController = TextEditingController(text: accommodation['name']); // Or room_number field
    _checkOutTimeController = TextEditingController(
        text: DateFormat('yyyy-MM-dd HH:mm').format(DateTime.now()));

    _balancePending = double.tryParse(widget.booking['balance_pending']?.toString() ?? '0') ?? 0.0;
    _lateChargesController = TextEditingController(text: '0');
    _serviceNotesController = TextEditingController();
    _finalBillController = TextEditingController(text: _originalTotal.toStringAsFixed(2));
    _depositRefundController = TextEditingController(text: '0');
    _amountCollectedController = TextEditingController(text: _balancePending.toStringAsFixed(2));
    
    _paymentStatus = 'completed'; // Default
    _paymentNotesController = TextEditingController();
    _feedbackController = TextEditingController();

    _lateChargesController.addListener(_updateFinalBill);
  }

  void _updateFinalBill() {
    final lateCharges = double.tryParse(_lateChargesController.text) ?? 0.0;
    final finalAmount = _originalTotal + lateCharges;
    _finalBillController.text = finalAmount.toStringAsFixed(2);
  }

  @override
  void dispose() {
    _guestNameController.dispose();
    _roomNumberController.dispose();
    _checkOutTimeController.dispose();
    _lateChargesController.dispose();
    _serviceNotesController.dispose();
    _finalBillController.dispose();
    _depositRefundController.dispose();
    _amountCollectedController.dispose();
    _paymentNotesController.dispose();
    _feedbackController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    final data = {
      'guest_name': _guestNameController.text,
      'room_number': _roomNumberController.text,
      'check_out_time': _checkOutTimeController.text,
      'services_used': _servicesUsed,
      'late_checkout_charges': double.tryParse(_lateChargesController.text) ?? 0,
      'service_notes': _serviceNotesController.text,
      'final_bill': double.tryParse(_finalBillController.text) ?? 0,
      'deposit_refund': double.tryParse(_depositRefundController.text) ?? 0,
      'amount_collected': double.tryParse(_amountCollectedController.text) ?? 0,
      'payment_status': _paymentStatus,
      'payment_notes': _paymentNotesController.text,
      'rating': _rating > 0 ? _rating : null,
      'feedback_comments': _feedbackController.text,
    };

    final result = await ref
        .read(bookingProvider)
        .performCheckOut(widget.booking['id'], data);

    if (result && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Guest Checked Out Successfully!')),
      );
      Navigator.pop(context, true); // Return success
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(ref.read(bookingProvider).error ?? 'Check-out failed'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text('Guest Check-Out', style: GoogleFonts.outfit(color: Colors.black)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildSectionHeader('1. Guest & Room Details'),
              _buildCard([
                _buildTextField('Guest Name *', _guestNameController, required: true),
                const SizedBox(height: 12),
                _buildTextField('Room Number', _roomNumberController),
              ]),
              const SizedBox(height: 24),
              
              _buildSectionHeader('2. Stay Review'),
              _buildCard([
                _buildTextField('Check-out Date & Time *', _checkOutTimeController, readOnly: true),
                const SizedBox(height: 12),
                Text('Services Used', style: GoogleFonts.outfit(fontWeight: FontWeight.w500, color: Colors.grey.shade700)),
                Wrap(
                  spacing: 8,
                  children: _availableServices.entries.map((e) {
                    final selected = _servicesUsed.contains(e.key);
                    return FilterChip(
                      label: Text(e.value),
                      selected: selected,
                      onSelected: (val) {
                        setState(() {
                          if (val) _servicesUsed.add(e.key);
                          else _servicesUsed.remove(e.key);
                        });
                      },
                    );
                  }).toList(),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(child: _buildTextField('Late Charges', _lateChargesController, keyboardType: TextInputType.number)),
                    const SizedBox(width: 12),
                    Expanded(child: _buildTextField('Service Notes', _serviceNotesController)),
                  ],
                ),
              ]),
              const SizedBox(height: 24),

              _buildSectionHeader('3. Final Settlement'),
              _buildCard([
                _buildTextField('Final Bill Amount *', _finalBillController, required: true, keyboardType: TextInputType.number),
                const SizedBox(height: 12),
                _buildTextField('Deposit Refund', _depositRefundController, keyboardType: TextInputType.number),
                const SizedBox(height: 12),
                DropdownButtonFormField<String>(
                  value: _paymentStatus,
                  decoration: _inputDecoration('Payment Status *'),
                  items: const [
                    DropdownMenuItem(value: 'completed', child: Text('Completed')),
                    DropdownMenuItem(value: 'pending', child: Text('Pending')),
                    DropdownMenuItem(value: 'partial', child: Text('Partial')),
                    DropdownMenuItem(value: 'refunded', child: Text('Refunded')),
                  ],
                  onChanged: (val) => setState(() => _paymentStatus = val),
                  validator: (val) => val == null ? 'Required' : null,
                ),
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.blue.shade50,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.blue.shade200),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Balance Pending:', style: GoogleFonts.outfit(fontWeight: FontWeight.w600)),
                          Text('₹${_balancePending.toStringAsFixed(2)}', 
                            style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: Colors.red.shade700)),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                _buildTextField('Amount Collected', _amountCollectedController, 
                  keyboardType: TextInputType.number,
                  required: false),
                const SizedBox(height: 12),
                _buildTextField('Payment Notes', _paymentNotesController, maxLines: 2),
              ]),
              const SizedBox(height: 24),

              _buildSectionHeader('4. Feedback'),
              _buildCard([
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(5, (index) {
                    return IconButton(
                      icon: Icon(
                        index < _rating ? Icons.star : Icons.star_border,
                        color: Colors.amber,
                        size: 32,
                      ),
                      onPressed: () => setState(() => _rating = index + 1),
                    );
                  }),
                ),
                const SizedBox(height: 12),
                _buildTextField('Comments', _feedbackController, maxLines: 3),
              ]),
              const SizedBox(height: 32),

              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: ref.watch(bookingProvider).isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.green, // Check-out style usually distinct? Green like web
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: ref.watch(bookingProvider).isLoading 
                      ? const CircularProgressIndicator(color: Colors.white)
                      : Text('Complete Check-Out', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12, left: 4),
      child: Text(title, style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.bold, color: Colors.blueGrey.shade800)),
    );
  }

  Widget _buildCard(List<Widget> children) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      padding: const EdgeInsets.all(20),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: children),
    );
  }

  Widget _buildTextField(String label, TextEditingController controller, {
    bool required = false,
    TextInputType? keyboardType,
    int maxLines = 1,
    bool readOnly = false,
  }) {
    return TextFormField(
      controller: controller,
      readOnly: readOnly,
      validator: required ? (val) => (val == null || val.isEmpty) ? '$label is required' : null : null,
      keyboardType: keyboardType,
      maxLines: maxLines,
      decoration: _inputDecoration(label),
      style: GoogleFonts.outfit(),
    );
  }

  InputDecoration _inputDecoration(String label) {
    return InputDecoration(
      labelText: label,
      labelStyle: GoogleFonts.outfit(color: Colors.grey.shade500),
      border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide(color: Colors.grey.shade300)),
      enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide(color: Colors.grey.shade300)),
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Colors.green)),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      filled: true,
      fillColor: Colors.grey.shade50,
    );
  }
}
