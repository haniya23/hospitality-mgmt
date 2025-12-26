import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'create_guest_screen.dart';

class GuestDetailsScreen extends StatefulWidget {
  final Map<String, dynamic> guest;

  const GuestDetailsScreen({super.key, required this.guest});

  @override
  State<GuestDetailsScreen> createState() => _GuestDetailsScreenState();
}

class _GuestDetailsScreenState extends State<GuestDetailsScreen> {
  late Map<String, dynamic> _guest;

  @override
  void initState() {
    super.initState();
    _guest = widget.guest;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'Guest Details',
          style: GoogleFonts.outfit(
              color: const Color(0xFF1E293B), fontWeight: FontWeight.bold),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Color(0xFF1E293B)),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit_rounded, color: Color(0xFF4F46E5)),
            onPressed: () async {
              final result = await Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => CreateGuestScreen(guest: _guest),
                ),
              );

              if (result == true) {
                 // Optimization: We could just pop. Or we can request generic refresh?
                 // Popping ensures the list (which is refreshed by CreateGuestScreen) is shown.
                 if (mounted) Navigator.pop(context); 
              }
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            _buildProfileHeader(),
            const SizedBox(height: 24),
            _buildInfoCard(),
            const SizedBox(height: 24),
            // _buildHistorySection(), // Future enhancement
          ],
        ),
      ),
    );
  }

  Widget _buildProfileHeader() {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: CircleAvatar(
            radius: 40,
            backgroundColor: const Color(0xFFE0E7FF),
            child: Text(
              _guest['name'] != null && _guest['name'].isNotEmpty
                  ? _guest['name'].substring(0, 1).toUpperCase()
                  : 'G',
              style: GoogleFonts.outfit(
                fontSize: 32,
                fontWeight: FontWeight.bold,
                color: const Color(0xFF4F46E5),
              ),
            ),
          ),
        ),
        const SizedBox(height: 16),
        Text(
          _guest['name'] ?? 'Guest Name',
          style: GoogleFonts.outfit(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: const Color(0xFF1E293B),
          ),
        ),
      ],
    );
  }

  Widget _buildInfoCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          _buildInfoRow(Icons.phone_rounded, 'Mobile',
              _guest['mobile_number'] ?? 'N/A'),
          const Divider(height: 32),
          _buildInfoRow(
              Icons.email_rounded, 'Email', _guest['email'] ?? 'N/A'),
          const Divider(height: 32),
          _buildInfoRow(Icons.badge_rounded, 'KYC Document',
              '${_guest['kyc_document_type'] ?? 'None'} - ${_guest['kyc_document_number'] ?? ''}'),
        ],
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: const Color(0xFFF1F5F9),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: const Color(0xFF64748B), size: 20),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: GoogleFonts.outfit(
                  fontSize: 12,
                  color: const Color(0xFF94A3B8),
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: GoogleFonts.outfit(
                  fontSize: 16,
                  color: const Color(0xFF334155),
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
