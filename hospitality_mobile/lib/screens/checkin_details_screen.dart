import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';

class CheckInDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> checkIn;

  const CheckInDetailsScreen({super.key, required this.checkIn});

  @override
  Widget build(BuildContext context) {
    final guest = checkIn['guest'] ?? checkIn['reservation']?['guest'] ?? {};
    final accommodation = checkIn['reservation']?['accommodation'] ?? {};
    final property = accommodation['property'] ?? {};
    final staff = checkIn['staff'] ?? {};
    
    // Parse dates
    final checkInTime = DateTime.tryParse(checkIn['check_in_time'] ?? '');
    final expectedCheckOut = DateTime.tryParse(checkIn['expected_check_out_date'] ?? '');
    final processedAt = DateTime.tryParse(checkIn['created_at'] ?? '');

    return Scaffold(
      appBar: AppBar(
        title: Text(
          'Check-in Details', 
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
            _buildSectionHeader('Complete check-in information'),
            const SizedBox(height: 8),
            _buildInfoRow('Check-in ID', checkIn['uuid'] ?? 'N/A', isCopyable: true),
            
            const Divider(height: 32),
            _buildSectionHeader('Guest Information'),
            const SizedBox(height: 8),
            _buildSubHeader('Personal Details'),
            _buildInfoRow('Full Name', guest['name'] ?? 'N/A'),
            _buildInfoRow('Contact Number', guest['mobile_number'] ?? checkIn['guest_contact'] ?? 'N/A'),
            const SizedBox(height: 12),
            _buildSubHeader('Identification'),
            _buildInfoRow('ID Proof Type', checkIn['id_proof_type'] ?? 'N/A'),
            
            const Divider(height: 32),
            _buildSectionHeader('Stay Information'),
            const SizedBox(height: 8),
            _buildSubHeader('Accommodation'),
            _buildInfoRow('Property', property['name'] ?? 'N/A'),
            _buildInfoRow('Room Name', accommodation['custom_name'] ?? accommodation['name'] ?? 'N/A'),
            _buildInfoRow('Booking Reference', checkIn['reservation']?['confirmation_number'] ?? 'N/A', isCopyable: true),
            const SizedBox(height: 12),
            _buildSubHeader('Timing'),
            _buildInfoRow('Check-in Time', checkInTime != null ? DateFormat('MMM d, y h:mm a').format(checkInTime) : 'N/A'),
            _buildInfoRow('Expected Check-out', expectedCheckOut != null ? DateFormat('MMM d, y').format(expectedCheckOut) : 'N/A'),
            const SizedBox(height: 12),
            _buildSubHeader('Guests'),
            _buildInfoRow('Guests', '${checkIn['reservation']?['adults'] ?? 1} adults, ${checkIn['reservation']?['children'] ?? 0} children'),

            if (checkIn['special_requests'] != null) ...[
              const Divider(height: 32),
              _buildSectionHeader('Special Requests & Notes'),
              const SizedBox(height: 8),
              _buildSubHeader('Special Requests'),
              Text(
                checkIn['special_requests'],
                style: GoogleFonts.outfit(color: Colors.black87, fontSize: 14),
              ),
            ],

            const Divider(height: 32),
            _buildSectionHeader('Staff Information'),
            const SizedBox(height: 8),
            ListTile(
               contentPadding: EdgeInsets.zero,
               leading: CircleAvatar(
                 backgroundColor: Colors.blue.shade100,
                 child: Text((staff['name'] ?? 'S')[0], style: TextStyle(color: Colors.blue.shade800)),
               ),
               title: Text(staff['name'] ?? 'Unknown Staff', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
               subtitle: Text(
                 'Processed check-in on ${processedAt != null ? DateFormat('MMM d, y h:mm a').format(processedAt) : 'N/A'}',
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
