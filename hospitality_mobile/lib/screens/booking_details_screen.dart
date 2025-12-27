import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../providers/booking_provider.dart';

class BookingDetailsScreen extends StatefulWidget {
  final int bookingId;
  final Map<String, dynamic>? initialData;

  const BookingDetailsScreen({super.key, required this.bookingId, this.initialData});

  @override
  State<BookingDetailsScreen> createState() => _BookingDetailsScreenState();
}

class _BookingDetailsScreenState extends State<BookingDetailsScreen> {
  Map<String, dynamic>? _bookingData;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    if (widget.initialData != null) {
      _bookingData = widget.initialData;
      _isLoading = false;
    }
    _fetchDetails();
  }

  Future<void> _fetchDetails() async {
    setState(() => _isLoading = true);
    final data = await Provider.of<BookingProvider>(context, listen: false)
        .fetchBookingDetails(widget.bookingId);
    if (mounted) {
      if (data != null) {
        setState(() {
          _bookingData = data;
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Failed to load latest booking details')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text('Booking Details', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: Colors.black)),
        backgroundColor: Colors.white,
        elevation: 0,
        leading: BackButton(color: Colors.black),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.black),
            onPressed: _fetchDetails,
          ),
        ],
      ),
      body: _isLoading && _bookingData == null
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _fetchDetails,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildHeaderCard(),
                    const SizedBox(height: 16),
                    _buildGuestInfoCard(),
                    const SizedBox(height: 16),
                    _buildFinancialCard(),
                    const SizedBox(height: 16),
                    _buildTimelineCard(),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildHeaderCard() {
    final booking = _bookingData!;
    final status = booking['status'] ?? 'unknown';
    final property = booking['accommodation']?['property']?['name'] ?? 'Property';
    final unit = booking['accommodation']?['display_name'] ?? 'Unit';
    final checkIn = DateTime.parse(booking['check_in_date']);
    final checkOut = DateTime.parse(booking['check_out_date']);
    
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 8, offset: const Offset(0, 2))],
      ),
      child: Column(
        children: [
          // Status Banner
          Container(
            width: double.infinity,
            padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
            decoration: BoxDecoration(
              color: _getStatusColor(status).withOpacity(0.1),
              borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
            ),
            child: Row(
              children: [
                Icon(_getStatusIcon(status), color: _getStatusColor(status), size: 20),
                const SizedBox(width: 8),
                Text(
                  status.toUpperCase(),
                  style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: _getStatusColor(status)),
                ),
                const Spacer(),
                Text('#${booking['booking_id'] ?? booking['id']}', style: GoogleFonts.outfit(color: Colors.grey[600], fontWeight: FontWeight.bold)),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(property, style: GoogleFonts.outfit(fontSize: 20, fontWeight: FontWeight.bold)),
                Text(unit, style: GoogleFonts.outfit(fontSize: 14, color: Colors.grey[600])),
                const Divider(height: 24),
                Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('CHECK-IN', style: GoogleFonts.outfit(fontSize: 10, color: Colors.grey[500], fontWeight: FontWeight.bold)),
                          Text(DateFormat('d MMM yyyy').format(checkIn), style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.w600)),
                          Text(DateFormat('h:mm a').format(checkIn), style: GoogleFonts.outfit(fontSize: 12, color: Colors.grey[500])),
                        ],
                      ),
                    ),
                    Icon(Icons.arrow_forward, color: Colors.grey[300]),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text('CHECK-OUT', style: GoogleFonts.outfit(fontSize: 10, color: Colors.grey[500], fontWeight: FontWeight.bold)),
                          Text(DateFormat('d MMM yyyy').format(checkOut), style: GoogleFonts.outfit(fontSize: 16, fontWeight: FontWeight.w600)),
                          Text(DateFormat('h:mm a').format(checkOut), style: GoogleFonts.outfit(fontSize: 12, color: Colors.grey[500])),
                        ],
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildGuestInfoCard() {
    final booking = _bookingData!;
    final guest = booking['guest'] ?? {};
    final name = guest['name'] ?? 'Guest';
    final email = guest['email'] ?? 'No Email';
    final phone = guest['phone'] ?? 'No Phone';

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 8, offset: const Offset(0, 2))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.person, color: Colors.blue),
              const SizedBox(width: 8),
              Text('Guest Details', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16)),
            ],
          ),
          const Divider(height: 24),
          ListTile(
            contentPadding: EdgeInsets.zero,
            leading: CircleAvatar(backgroundColor: Colors.blue.shade50, child: Text(name[0].toUpperCase())),
            title: Text(name, style: GoogleFonts.outfit(fontWeight: FontWeight.w600)),
            subtitle: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (email != 'No Email') Text(email),
                if (phone != 'No Phone') Text(phone),
              ],
            ),
            trailing: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                IconButton(
                  icon: const Icon(Icons.phone, color: Colors.green),
                  onPressed: () => launchUrl(Uri.parse('tel:$phone')),
                ),
                IconButton(
                  icon: const Icon(Icons.message, color: Colors.blue),
                  onPressed: () => launchUrl(Uri.parse('sms:$phone')),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFinancialCard() {
    final booking = _bookingData!;
    final total = double.tryParse(booking['total_amount']?.toString() ?? '0') ?? 0;
    final pending = double.tryParse(booking['balance_pending']?.toString() ?? '0') ?? 0;
    final paid = total - pending;

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 8, offset: const Offset(0, 2))],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.receipt_long, color: Colors.purple),
              const SizedBox(width: 8),
              Text('Payment Info', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16)),
            ],
          ),
          const Divider(height: 24),
          _buildRow('Total Amount', '₹$total', isBold: true),
          const SizedBox(height: 8),
          _buildRow('Paid Amount', '₹$paid', color: Colors.green),
          const SizedBox(height: 8),
          _buildRow('Pending Balance', '₹$pending', color: pending > 0 ? Colors.red : Colors.grey, isBold: pending > 0),
        ],
      ),
    );
  }
  
  Widget _buildTimelineCard() {
      // Placeholder for actual timeline data if available, or just create/update dates
      final booking = _bookingData!;
      final created = DateTime.parse(booking['created_at']);
      
      return Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 8, offset: const Offset(0, 2))],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
               Text('Timeline', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16)),
               const SizedBox(height: 16),
               _buildTimelineItem('Booking Created', created, true),
               _buildTimelineItem('Current Status: ${booking['status'].toString().toUpperCase()}', DateTime.parse(booking['updated_at']), false),
            ],
          ),
      );
  }
  
  Widget _buildTimelineItem(String title, DateTime date, bool showLine) {
      return Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
              Column(
                  children: [
                      Container(
                          width: 12, height: 12,
                          decoration: const BoxDecoration(color: Colors.blue, shape: BoxShape.circle),
                      ),
                      if (showLine) Container(width: 2, height: 40, color: Colors.grey[200]),
                  ],
              ),
              const SizedBox(width: 12),
              Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                      Text(title, style: GoogleFonts.outfit(fontWeight: FontWeight.w500)),
                      Text(DateFormat('d MMM yyyy, h:mm a').format(date), style: GoogleFonts.outfit(fontSize: 12, color: Colors.grey)),
                      const SizedBox(height: 20),
                  ],
              )
          ],
      );
  }

  Widget _buildRow(String label, String value, {Color? color, bool isBold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: GoogleFonts.outfit(color: Colors.grey[600])),
        Text(
          value,
          style: GoogleFonts.outfit(
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
            color: color ?? Colors.black87,
            fontSize: isBold ? 16 : 14,
          ),
        ),
      ],
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'confirmed': return Colors.green;
      case 'pending': return Colors.orange;
      case 'cancelled': return Colors.red;
      case 'checked_out': return Colors.purple;
      case 'checked_in': return Colors.blue; 
      default: return Colors.grey;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status.toLowerCase()) {
       case 'confirmed': return Icons.check_circle;
       case 'pending': return Icons.pending;
       case 'cancelled': return Icons.cancel;
       case 'checked_out': return Icons.exit_to_app;
       case 'checked_in': return Icons.login;
       default: return Icons.info;
    }
  }
}
