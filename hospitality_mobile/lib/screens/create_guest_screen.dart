import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/riverpod_providers.dart';

class CreateGuestScreen extends ConsumerStatefulWidget {
  final Map<String, dynamic>? guest;

  const CreateGuestScreen({super.key, this.guest});

  @override
  ConsumerState<CreateGuestScreen> createState() => _CreateGuestScreenState();
}

class _CreateGuestScreenState extends ConsumerState<CreateGuestScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _mobileController = TextEditingController();
  final _emailController = TextEditingController();
  final _kycTypeController = TextEditingController();
  final _kycNumberController = TextEditingController();
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    if (widget.guest != null) {
      _nameController.text = widget.guest!['name'] ?? '';
      _mobileController.text = widget.guest!['mobile_number'] ?? '';
      _emailController.text = widget.guest!['email'] ?? '';
      _kycTypeController.text = widget.guest!['kyc_document_type'] ?? '';
      _kycNumberController.text = widget.guest!['kyc_document_number'] ?? '';
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _mobileController.dispose();
    _emailController.dispose();
    _kycTypeController.dispose();
    _kycNumberController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);

    final data = {
      'name': _nameController.text.trim(),
      'mobile_number': _mobileController.text.trim(),
      'email': _emailController.text.trim(),
      'kyc_document_type': _kycTypeController.text.trim(),
      'kyc_document_number': _kycNumberController.text.trim(),
    };

    dynamic result;
    if (widget.guest != null) {
      result = await ref.read(guestProvider).updateGuest(widget.guest!['id'], data);
    } else {
      result = await ref.read(guestProvider).createGuest(data);
    }

    setState(() => _isLoading = false);

    if (result != null && result != false) {
      if (mounted) {
        if (ref.read(guestProvider).error == null) {
          // Success
          Navigator.pop(context, true);
          ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(widget.guest != null ? 'Guest updated successfully' : 'Guest added successfully')));
          ref.read(guestProvider).fetchGuests(isRefresh: true);
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(ref.read(guestProvider).error!)));
        }
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(ref.read(guestProvider).error ?? 'Error')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.guest != null ? 'Edit Guest' : 'Add Guest',
          style: GoogleFonts.outfit(
              color: Colors.black87, fontWeight: FontWeight.bold),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black87),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _buildLabel('Full Name'),
              _buildTextField(_nameController, 'Enter guest name',
                  icon: Icons.person_outline),
              const SizedBox(height: 20),
              _buildLabel('Mobile Number'),
              _buildTextField(_mobileController, 'Enter mobile number',
                  icon: Icons.phone_outlined,
                  keyboardType: TextInputType.phone),
              const SizedBox(height: 20),
              _buildLabel('Email (Optional)'),
              _buildTextField(_emailController, 'Enter email address',
                  icon: Icons.email_outlined,
                  keyboardType: TextInputType.emailAddress,
                  required: false),
              const SizedBox(height: 20),
              _buildLabel('KYC Document Type'),
              _buildTextField(_kycTypeController, 'e.g. Aadhar, Passport',
                  icon: Icons.badge_outlined, required: false),
              const SizedBox(height: 20),
              _buildLabel('KYC Document Number'),
              _buildTextField(_kycNumberController, 'Enter document number',
                  icon: Icons.numbers_outlined, required: false),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                height: 54,
                child: ElevatedButton(
                  onPressed: _isLoading ? null : _submit,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF4F46E5),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: _isLoading
                      ? const CircularProgressIndicator(color: Colors.white)
                      : Text(
                          widget.guest != null ? 'Update Guest' : 'Save Guest',
                          style: GoogleFonts.outfit(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Colors.white),
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLabel(String label) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Text(
        label,
        style: GoogleFonts.outfit(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: const Color(0xFF1E293B),
        ),
      ),
    );
  }

  Widget _buildTextField(TextEditingController controller, String hint,
      {IconData? icon,
      TextInputType keyboardType = TextInputType.text,
      bool required = true}) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      validator: required
          ? (value) {
              if (value == null || value.isEmpty) {
                return 'This field is required';
              }
              return null;
            }
          : null,
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: GoogleFonts.outfit(color: Colors.grey.shade400),
        prefixIcon: icon != null
            ? Icon(icon, color: Colors.grey.shade400, size: 20)
            : null,
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: Colors.grey.shade200),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(color: Colors.grey.shade200),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Color(0xFF4F46E5), width: 2),
        ),
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      ),
      style: GoogleFonts.outfit(color: const Color(0xFF1E293B)),
    );
  }
}
