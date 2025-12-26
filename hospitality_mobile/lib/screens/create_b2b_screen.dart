import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/riverpod_providers.dart';

class CreateB2bScreen extends ConsumerStatefulWidget {
  final Map<String, dynamic>? partner;

  const CreateB2bScreen({super.key, this.partner});

  @override
  ConsumerState<CreateB2bScreen> createState() => _CreateB2bScreenState();
}

class _CreateB2bScreenState extends ConsumerState<CreateB2bScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _mobileController = TextEditingController();
  final _emailController = TextEditingController();
  final _commissionController = TextEditingController(text: '10');
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    if (widget.partner != null) {
      _nameController.text = widget.partner!['partner_name'] ?? '';
      _mobileController.text = widget.partner!['phone'] ?? '';
      _emailController.text = widget.partner!['email'] ?? '';
      _commissionController.text = widget.partner!['commission_rate']?.toString() ?? '10';
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _mobileController.dispose();
    _emailController.dispose();
    _commissionController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);

    final data = {
      'partner_name': _nameController.text.trim(),
      'phone': _mobileController.text.trim(),
      'email': _emailController.text.trim(),
      'commission_rate': _commissionController.text.trim(),
    };

    bool result;
    if (widget.partner != null) {
      result = await ref.read(b2bProvider).updatePartner(widget.partner!['id'], data);
    } else {
      result = await ref.read(b2bProvider).addPartner(data);
    }

    setState(() => _isLoading = false);

    if (result) {
      if (mounted) {
        Navigator.pop(context, true);
        ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(widget.partner != null ? 'Partner updated successfully' : 'Partner added successfully')));
        ref.read(b2bProvider).fetchPartners(isRefresh: true);
      }
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(ref.read(b2bProvider).error ?? 'Error')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.partner != null ? 'Edit Partner' : 'Add Partner',
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
              _buildLabel('Partner/Agency Name'),
              _buildTextField(_nameController, 'Enter partner name',
                  icon: Icons.business_outlined),
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
              _buildLabel('Commission (%)'),
              _buildTextField(_commissionController, '10',
                  icon: Icons.percent_outlined,
                  keyboardType: TextInputType.number),
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
                          widget.partner != null ? 'Update Partner' : 'Save Partner',
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
