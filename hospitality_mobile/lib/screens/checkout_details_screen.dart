import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

class CheckOutDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> checkOut;

  const CheckOutDetailsScreen({super.key, required this.checkOut});

  @override
  Widget build(BuildContext context) {
    final guest = checkOut['guest'] ?? checkOut['reservation']?['guest'] ?? {};
    final accommodation = checkOut['reservation']?['accommodation'] ?? {};
    final property = accommodation['property'] ?? {};
    final staff = checkOut['staff'] ?? {};
    
    // Parse dates
    final checkOutTime = DateTime.tryParse(checkOut['check_out_time'] ?? '');
    final processedAt = DateTime.tryParse(checkOut['created_at'] ?? '');

    return Scaffold(
      appBar: AppBar(
        title: Text(
          'Check-out Details', 
          style: GoogleFonts.outfit(color: Colors.black, fontWeight: FontWeight.w600),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildSectionHeader('Complete Check-out Information'),
            const SizedBox(height: 8),
            _buildInfoRow('Check-out ID', checkOut['uuid'] ?? 'N/A', isCopyable: true),
            
            const Divider(height: 32),
            _buildSectionHeader('Stay Summary'),
            const SizedBox(height: 8),
            _buildInfoRow('Guest Name', guest['name'] ?? checkOut['guest_name'] ?? 'N/A'),
            _buildInfoRow('Property', property['name'] ?? 'N/A'),
            _buildInfoRow('Room Name', accommodation['custom_name'] ?? accommodation['name'] ?? 'N/A'),
            _buildInfoRow('Booking Ref', checkOut['reservation']?['confirmation_number'] ?? 'N/A', isCopyable: true),
            _buildInfoRow('Check-out Time', checkOutTime != null ? DateFormat('MMM d, y h:mm a').format(checkOutTime) : 'N/A'),

            const Divider(height: 32),
            _buildSectionHeader('Billing & Payments'),
            const SizedBox(height: 8),
            _buildInfoRow('Final Bill', '₹${checkOut['final_bill'] ?? 0}'),
            if ((checkOut['late_checkout_charges'] ?? 0) > 0)
              _buildInfoRow('Late Charges', '₹${checkOut['late_checkout_charges']}'),
            _buildInfoRow('Payment Status', (checkOut['payment_status'] ?? 'pending').toString().toUpperCase()),
            
            if (checkOut['services_used'] != null && (checkOut['services_used'] as List).isNotEmpty) ...[
              const SizedBox(height: 12),
              _buildSubHeader('Services Used'),
              Wrap(
                spacing: 8,
                children: (checkOut['services_used'] as List).map<Widget>((s) => Chip(
                  label: Text(s.toString()),
                  backgroundColor: Colors.blue.shade50,
                  labelStyle: TextStyle(color: Colors.blue.shade800, fontSize: 12),
                )).toList(),
              ),
            ],

            if (checkOut['rating'] != null) ...[
              const Divider(height: 32),
              _buildSectionHeader('Feedback'),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Text('Rating: '),
                  ...List.generate(5, (index) => Icon(
                    index < (checkOut['rating'] ?? 0) ? Icons.star : Icons.star_border,
                    color: Colors.amber,
                    size: 20,
                  )),
                ],
              ),
              if (checkOut['feedback_comments'] != null)
                Padding(
                  padding: const EdgeInsets.only(top: 8.0),
                  child: Text(
                    '"${checkOut['feedback_comments']}"',
                    style: GoogleFonts.outfit(fontStyle: FontStyle.italic, color: Colors.grey[700]),
                  ),
                ),
            ],

            const Divider(height: 32),
            _buildSectionHeader('Staff Information'),
            const SizedBox(height: 8),
            ListTile(
               contentPadding: EdgeInsets.zero,
               leading: CircleAvatar(
                 backgroundColor: Colors.orange.shade100,
                 child: Text((staff['name'] ?? 'S')[0], style: TextStyle(color: Colors.orange.shade800)),
               ),
               title: Text(staff['name'] ?? 'Unknown Staff', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
               subtitle: Text(
                 'Processed check-out on ${processedAt != null ? DateFormat('MMM d, y h:mm a').format(processedAt) : 'N/A'}',
                 style: GoogleFonts.outfit(fontSize: 12, color: Colors.grey),
               ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return Text(
      title,
      style: GoogleFonts.outfit(
        fontSize: 18,
        fontWeight: FontWeight.bold,
        color: Colors.black87,
      ),
    );
  }

  Widget _buildSubHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Text(
        title,
        style: GoogleFonts.outfit(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: Colors.grey[600],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isCopyable = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 140,
            child: Text(
              label,
              style: GoogleFonts.outfit(
                color: Colors.grey[600],
                fontSize: 14,
              ),
            ),
          ),
          Expanded(
            child: Row(
              children: [
                Expanded(
                  child: Text(
                    value,
                    style: GoogleFonts.outfit(
                      color: Colors.black87,
                      fontSize: 14,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
                if (isCopyable)
                  const Padding(
                    padding: EdgeInsets.only(left: 4.0),
                    child: Icon(Icons.copy, size: 14, color: Colors.grey),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
