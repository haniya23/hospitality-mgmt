import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/riverpod_providers.dart';
import '../providers/booking_provider.dart';

class CheckInFormScreen extends ConsumerStatefulWidget {
  final Map<String, dynamic> booking;

  const CheckInFormScreen({super.key, required this.booking});

  @override
  ConsumerState<CheckInFormScreen> createState() => _CheckInFormScreenState();
}

class _CheckInFormScreenState extends ConsumerState<CheckInFormScreen> {
  final _formKey = GlobalKey<FormState>();

  // Guest Info
  late TextEditingController _nameController;
  late TextEditingController _contactController;
  late TextEditingController _emailController;
  late TextEditingController _addressController;
  late TextEditingController _idNumberController;
  String? _selectedIdType;

  // Stay Info
  late TextEditingController _checkInTimeController;
  late TextEditingController _expectedCheckOutController;
  late TextEditingController _requestsController;
  late TextEditingController _notesController;

  DateTime _selectedDate = DateTime.now();

  @override
  void initState() {
    super.initState();
    final guest = widget.booking['guest'] ?? {};
    _nameController = TextEditingController(text: guest['name']);
    _contactController = TextEditingController(text: guest['mobile_number']);
    _emailController = TextEditingController(text: guest['email']);
    _addressController = TextEditingController(text: guest['address']);
    _idNumberController = TextEditingController(text: guest['id_number']);
    _selectedIdType = guest['id_type'];
    
    // Default ID Type logic just in case backend has null or invalid
    if (!['passport', 'aadhaar', 'driving_license', 'pan', 'voter_id']
        .contains(_selectedIdType)) {
      _selectedIdType = null;
    }

    _checkInTimeController = TextEditingController(
        text: DateFormat('yyyy-MM-dd HH:mm').format(DateTime.now()));
    
    final checkoutStr = widget.booking['check_out_date'];
    _expectedCheckOutController = TextEditingController(text: checkoutStr ?? '');
    
    _requestsController = TextEditingController(text: widget.booking['special_requests']);
    _notesController = TextEditingController();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _contactController.dispose();
    _emailController.dispose();
    _addressController.dispose();
    _idNumberController.dispose();
    _checkInTimeController.dispose();
    _expectedCheckOutController.dispose();
    _requestsController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    final data = {
      'guest_name': _nameController.text,
      'guest_contact': _contactController.text,
      'guest_email': _emailController.text,
      'guest_address': _addressController.text,
      'id_proof_type': _selectedIdType,
      'id_proof_number': _idNumberController.text,
      'nationality': '', // Add field if needed, currently empty default
      'check_in_time': _checkInTimeController.text, // Should be ISO or parsable
      'expected_check_out_date': _expectedCheckOutController.text,
      'special_requests': _requestsController.text,
      'notes': _notesController.text,
      // Signatures skipped for MVP
    };

    final result = await ref
        .read(bookingProvider)
        .performCheckIn(widget.booking['id'], data);

    if (result && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Guest Checked In Successfully!')),
      );
      Navigator.pop(context, true); // Return success
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(ref.read(bookingProvider).error ?? 'Check-in failed'),
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
        title: Text('Guest Check-In', style: GoogleFonts.outfit(color: Colors.black)),
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
              _buildSectionHeader('1. Guest Information'),
              _buildCard([
                _buildTextField('Full Name *', _nameController, required: true),
                const SizedBox(height: 12),
                _buildTextField('Contact Number *', _contactController, required: true, keyboardType: TextInputType.phone),
                const SizedBox(height: 12),
                _buildTextField('Email', _emailController, keyboardType: TextInputType.emailAddress),
                const SizedBox(height: 12),
                _buildTextField('Address', _addressController, maxLines: 2),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      flex: 2,
                      child: DropdownButtonFormField<String>(
                        value: _selectedIdType,
                        decoration: _inputDecoration('ID Type'),
                        items: const [
                          DropdownMenuItem(value: 'passport', child: Text('Passport')),
                          DropdownMenuItem(value: 'aadhaar', child: Text('Aadhaar')),
                          DropdownMenuItem(value: 'driving_license', child: Text('Driving License')),
                          DropdownMenuItem(value: 'pan', child: Text('PAN Card')),
                          DropdownMenuItem(value: 'voter_id', child: Text('Voter ID')),
                        ],
                        onChanged: (val) => setState(() => _selectedIdType = val),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      flex: 3,
                      child: _buildTextField('ID Number', _idNumberController),
                    ),
                  ],
                ),
              ]),
              const SizedBox(height: 24),
              _buildSectionHeader('2. Stay Details'),
              _buildCard([
                _buildTextField('Check-in Date & Time *', _checkInTimeController, readOnly: true, onTap: () async {
                   // Simple mock logic for now, or just keep current time
                   // Ideally implement Date/Time Picker
                }),
                const SizedBox(height: 12),
                _buildTextField('Expected Check-out *', _expectedCheckOutController, readOnly: true),
                const SizedBox(height: 12),
                _buildTextField('Special Requests', _requestsController, maxLines: 2),
                const SizedBox(height: 12),
                _buildTextField('Additional Notes', _notesController, maxLines: 2),
              ]),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: ref.watch(bookingProvider).isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF4F46E5),
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  child: ref.watch(bookingProvider).isLoading 
                      ? const CircularProgressIndicator(color: Colors.white)
                      : Text('Complete Check-In', style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.bold)),
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
    VoidCallback? onTap,
  }) {
    return TextFormField(
      controller: controller,
      readOnly: readOnly,
      onTap: onTap,
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
      focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: const BorderSide(color: Color(0xFF4F46E5))),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      filled: true,
      fillColor: Colors.grey.shade50,
    );
  }
}
