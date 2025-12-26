import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/booking_provider.dart';
import 'main_layout.dart';
import 'create_booking_screen.dart';
import 'package:share_plus/share_plus.dart';
import 'package:url_launcher/url_launcher.dart';

class BookingsTab extends StatefulWidget {
  final VoidCallback? onAddBooking;
  
  const BookingsTab({super.key, this.onAddBooking});

  @override
  State<BookingsTab> createState() => _BookingsTabState();
}

class _BookingsTabState extends State<BookingsTab> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _tabController.addListener(_handleTabSelection);
    
    Future.microtask(() {
      final provider = Provider.of<BookingProvider>(context, listen: false);
      provider.fetchCounts();
      provider.fetchBookings(status: 'pending'); // Default to Pending tab
    });
  }

  void _handleTabSelection() {
    if (_tabController.indexIsChanging) {
      final provider = Provider.of<BookingProvider>(context, listen: false);
      if (_tabController.index == 0) {
        provider.fetchBookings(status: 'pending');
      } else {
        provider.fetchBookings(status: 'confirmed');
      }
    }
  }

  @override
  void dispose() {
    _tabController.removeListener(_handleTabSelection);
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final bookingProvider = Provider.of<BookingProvider>(context);
    final counts = bookingProvider.counts;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.menu, color: Colors.black87),
          onPressed: () => MainLayout.scaffoldKey.currentState?.openDrawer(),
        ),
        title: Text(
          'Bookings',
          style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: const Color(0xFF1E293B)),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.black87),
            onPressed: () {
               bookingProvider.fetchBookings(status: _tabController.index == 0 ? 'pending' : 'confirmed');
               bookingProvider.fetchCounts();
            },
          ),
          Padding(
            padding: const EdgeInsets.only(right: 16.0),
            child: IconButton(
              icon: const Icon(Icons.add_circle, color: Colors.blue, size: 32),
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => const CreateBookingScreen()),
                ).then((_) {
                  bookingProvider.fetchBookings(status: _tabController.index == 0 ? 'pending' : 'confirmed');
                  bookingProvider.fetchCounts();
                });
              },
            ),
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          labelColor: Colors.blue,
          unselectedLabelColor: Colors.grey,
          labelStyle: GoogleFonts.outfit(fontWeight: FontWeight.bold),
          indicatorColor: Colors.blue,
          tabs: [
            Tab(text: 'Pending Requests (${counts['pending'] ?? 0})'),
            Tab(text: 'Confirmed (${counts['confirmed'] ?? 0})'),
          ],
        ),
      ),
      body: Stack(
        children: [
          TabBarView(
            controller: _tabController,
            children: [
              _buildBookingList(bookingProvider.bookings, isPending: true),
              _buildBookingList(bookingProvider.bookings, isPending: false),
            ],
          ),
          if (bookingProvider.isLoading)
            Container(
              color: Colors.white.withOpacity(0.5),
              child: const Center(
                child: CircularProgressIndicator(),
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildBookingList(List<dynamic> bookings, {required bool isPending}) {
    if (bookings.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(isPending ? Icons.pending_actions : Icons.check_circle_outline, size: 64, color: Colors.grey[300]),
            const SizedBox(height: 16),
            Text(
              isPending ? 'No pending requests' : 'No confirmed bookings',
              style: GoogleFonts.outfit(color: Colors.grey[500]),
            ),
          ],
        ),
      );
    }

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: bookings.length,
      separatorBuilder: (_, __) => const SizedBox(height: 16),
      itemBuilder: (context, index) {
        final booking = bookings[index];
        return isPending
            ? _buildPendingCard(context, booking)
            : _buildConfirmedCard(context, booking);
      },
    );
  }

  // Pending Card: Confirm / Cancel
  Widget _buildPendingCard(BuildContext context, Map<String, dynamic> booking) {
    return _buildBaseCard(
      context,
      booking,
      actions: Column(
        children: [
          const Divider(),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () => _updateStatus(context, booking['id'], 'cancelled'),
                  icon: const Icon(Icons.close, size: 18),
                  label: const Text('Decline'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: const Color(0xFFEF4444), // Red text
                    side: const BorderSide(color: Color(0xFFEF4444)), // Red border
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    textStyle: GoogleFonts.outfit(fontWeight: FontWeight.bold),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: ElevatedButton.icon(
                  onPressed: () => _showConfirmDialog(context, booking),
                  icon: const Icon(Icons.check, size: 18),
                  label: const Text('Approve Request'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF10B981),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    textStyle: GoogleFonts.outfit(fontWeight: FontWeight.bold),
                    elevation: 0,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // Confirmed Card: Edit, Invoice, Share, Cancel (Icon)
  Widget _buildConfirmedCard(BuildContext context, Map<String, dynamic> booking) {
    return _buildBaseCard(
      context,
      booking,
      actions: Row(
        children: [
          Expanded(
            child: _buildActionButton(Icons.edit, 'Edit', Colors.blue, () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => CreateBookingScreen(editingBooking: booking)),
              ).then((_) {
                 // Refresh after valid return
                 if (context.mounted) {
                   final provider = Provider.of<BookingProvider>(context, listen: false);
                   provider.fetchBookings(status: provider.currentStatus);
                   provider.fetchCounts();
                 }
              });
            }),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildActionButton(Icons.description, 'Invoice', Colors.purple, () => _downloadInvoice(context, booking['id'])),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildActionButton(Icons.share, 'Share', Colors.green, () => _shareBooking(booking)),
          ),
          const SizedBox(width: 8),
          Container(
            decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(8)),
            child: IconButton(
              icon: const Icon(Icons.close, color: Colors.red),
              onPressed: () => _showCancelDialog(context, booking['id']),
              tooltip: 'Cancel Booking',
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBaseCard(BuildContext context, Map<String, dynamic> booking, {required Widget actions}) {
    final guestName = booking['guest']?['name'] ?? 'Guest';
    final propertyName = booking['accommodation']?['property']?['name'] ?? 'Property';
    final accName = booking['accommodation']?['display_name'] ?? 'Unit';
    final checkIn = DateTime.parse(booking['check_in_date']);
    final checkOut = DateTime.parse(booking['check_out_date']);
    final nights = checkOut.difference(checkIn).inDays;
    
    // Parse amounts
    final total = double.tryParse(booking['total_amount']?.toString() ?? '0') ?? 0;
    final pending = double.tryParse(booking['balance_pending']?.toString() ?? '0') ?? 0;
    
    final isB2B = booking['b2b_partner_id'] != null;
    final status = booking['status'].toString();

    // Try to get an image
    String? imageUrl;
    final photos = booking['accommodation']?['photos'] as List?;
    if (photos != null && photos.isNotEmpty) {
      imageUrl = photos[0]['url']; // Assuming photo object has url
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04), // Softer shadow
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Left Image
                Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    color: Colors.grey.shade200,
                    image: imageUrl != null 
                       ? DecorationImage(image: NetworkImage(imageUrl), fit: BoxFit.cover)
                       : null,
                  ),
                  child: imageUrl == null 
                     ? Icon(Icons.apartment, color: Colors.grey.shade400, size: 40)
                     : null,
                ),
                const SizedBox(width: 16),
                
                // Middle Info
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Text(
                              propertyName,
                              style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16, color: const Color(0xFF1E293B)),
                              maxLines: 1, overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          _buildStatusBadge(status),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(
                        accName,
                        style: GoogleFonts.outfit(fontSize: 13, color: Colors.grey[600]),
                        maxLines: 1, overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 8),
                      // Guest Row
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              color: Colors.blue.shade50,
                              shape: BoxShape.circle,
                            ),
                            child: Icon(Icons.person, size: 12, color: Colors.blue.shade700),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              guestName + (isB2B ? ' (B2B)' : ''),
                              style: GoogleFonts.outfit(fontSize: 14, fontWeight: FontWeight.w600, color: const Color(0xFF334155)),
                              maxLines: 1, overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      // Date Row
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              color: Colors.orange.shade50,
                              shape: BoxShape.circle,
                            ),
                            child: Icon(Icons.calendar_today, size: 12, color: Colors.orange.shade700),
                          ),
                          const SizedBox(width: 8),
                          Text(
                            '${DateFormat('d MMM').format(checkIn)} - ${DateFormat('d MMM').format(checkOut)}',
                            style: GoogleFonts.outfit(fontSize: 13, fontWeight: FontWeight.w500, color: const Color(0xFF0F172A)),
                          ),
                          Text(
                            ' • $nights nights',
                            style: GoogleFonts.outfit(fontSize: 12, color: Colors.grey[500]),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          
          // Price and Actions Footer
          Container(
             padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
             decoration: BoxDecoration(
               color: const Color(0xFFF8FAFC),
               borderRadius: const BorderRadius.only(
                 bottomLeft: Radius.circular(12),
                 bottomRight: Radius.circular(12)
               ),
               border: Border(top: BorderSide(color: Colors.grey.shade100)),
             ),
             child: Column(
                children: [
                  Row(
                     mainAxisAlignment: MainAxisAlignment.spaceBetween,
                     children: [
                       Column(
                         crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Total Amount',
                              style: GoogleFonts.outfit(fontSize: 11, color: Colors.grey[500], fontWeight: FontWeight.w500),
                            ),
                            Text(
                              '₹$total',
                              style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.w800, color: const Color(0xFF0F172A)),
                            ),
                          ],
                       ),
                       if (pending > 0)
                         Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                            decoration: BoxDecoration(
                              color: Colors.red.shade50, 
                              borderRadius: BorderRadius.circular(6),
                              border: Border.all(color: Colors.red.shade100),
                            ),
                            child: Row(
                              children: [
                                Icon(Icons.warning_amber_rounded, size: 14, color: Colors.red[700]),
                                const SizedBox(width: 4),
                                Text(
                                  '₹$pending Pending',
                                  style: GoogleFonts.outfit(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.red[700]),
                                ),
                              ],
                            ),
                         ),
                     ],
                  ),
                  if (actions is! SizedBox) ...[
                    const SizedBox(height: 12),
                    actions,
                  ]
                ],
              ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color bg;
    Color text;
    switch (status.toLowerCase()) {
      case 'confirmed':
        bg = const Color(0xFFDCFCE7); // Green 100
        text = const Color(0xFF166534); // Green 800
        break;
      case 'pending':
        bg = const Color(0xFFFEF9C3); // Yellow 100
        text = const Color(0xFF854D0E); // Yellow 800
        break;
      case 'cancelled':
        bg = const Color(0xFFFEE2E2); // Red 100
        text = const Color(0xFF991B1B); // Red 800
        break;
      default:
        bg = Colors.grey.shade100;
        text = Colors.grey.shade700;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(4)),
      child: Text(
        status.toUpperCase(),
        style: GoogleFonts.outfit(fontSize: 10, fontWeight: FontWeight.bold, color: text),
      ),
    );
  }

  Widget _buildActionButton(IconData icon, String label, Color color, VoidCallback onTap) {
    return ElevatedButton(
      onPressed: onTap,
      style: ElevatedButton.styleFrom(
        backgroundColor: color,
        foregroundColor: Colors.white,
        padding: EdgeInsets.zero,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        elevation: 0,
        textStyle: GoogleFonts.outfit(fontSize: 12, fontWeight: FontWeight.w600),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 16),
          const SizedBox(width: 4),
          Text(label),
        ],
      ),
    );
  }

  void _showConfirmDialog(BuildContext context, Map<String, dynamic> booking) {
    final guestName = booking['guest']?['name'] ?? 'Guest';
    final dates = '${_formatDate(booking['check_in_date'])} - ${_formatDate(booking['check_out_date'])}';
    
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            const Icon(Icons.check_circle, color: Color(0xFF10B981), size: 28),
            const SizedBox(width: 12),
            Text('Confirm Request?', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Are you sure you want to approve this booking?', style: GoogleFonts.outfit(fontSize: 16)),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey.shade50,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  _buildDialogRow('Guest:', guestName),
                  const SizedBox(height: 8),
                  _buildDialogRow('Dates:', dates),
                  const SizedBox(height: 8),
                  _buildDialogRow('Total:', '₹${booking['total_amount']}'),
                ],
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Cancel', style: GoogleFonts.outfit(color: Colors.grey[600], fontWeight: FontWeight.bold)),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              _updateStatus(context, booking['id'], 'confirmed');
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF10B981),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
            ),
            child: Text('Confirm Approval', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Widget _buildDialogRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 60,
          child: Text(label, style: GoogleFonts.outfit(color: Colors.grey[500], fontSize: 13)),
        ),
        Expanded(
          child: Text(value, style: GoogleFonts.outfit(fontWeight: FontWeight.w600, fontSize: 13, color: Colors.blueGrey[800])),
        ),
      ],
    );
  }

  void _updateStatus(BuildContext context, int id, String status) async {
      // Direct update for simple status changes (like Confirm)
      final success = await Provider.of<BookingProvider>(context, listen: false)
          .updateBookingStatus(id, status);
      
      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Booking updated to ${status.toUpperCase()}'),
            backgroundColor: status == 'confirmed' ? Colors.green : Colors.red,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
  }

  void _showCancelDialog(BuildContext context, int bookingId) {
    final reasonController = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Cancel Booking', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('Please provide a reason for cancellation:', style: GoogleFonts.outfit(fontSize: 14)),
            const SizedBox(height: 12),
            TextField(
              controller: reasonController,
              decoration: const InputDecoration(
                hintText: 'Reason (e.g. Guest request, No show)',
                border: OutlineInputBorder(),
              ),
              maxLines: 2,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Back', style: GoogleFonts.outfit(color: Colors.grey)),
          ),
          ElevatedButton(
            onPressed: () async {
              if (reasonController.text.isEmpty) {
                 ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Reason is required')));
                 return;
              }
              Navigator.pop(context); // Close dialog
              final success = await Provider.of<BookingProvider>(context, listen: false)
                  .updateBookingStatus(bookingId, 'cancelled', reason: reasonController.text);
              if (success && mounted) {
                 ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Booking cancelled successfully')));
              }
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: Text('Confirm Cancel', style: GoogleFonts.outfit(color: Colors.white)),
          ),
        ],
      ),
    );
  }

  void _shareBooking(Map<String, dynamic> booking) {
    final guest = booking['guest']['name'];
    final prop = booking['accommodation']['property']['name'];
    final dates = '${_formatDate(booking['check_in_date'])} to ${_formatDate(booking['check_out_date'])}';
    final text = 'Booking Confirmed!\nGuest: $guest\nProperty: $prop\nDates: $dates\nTotal: ₹${booking['total_amount']}';
    Share.share(text);
  }

  String _formatDate(dynamic dateStr) {
    if (dateStr == null) return '';
    try {
      final date = DateTime.parse(dateStr.toString());
      return DateFormat('d MMM yyyy, h:mm a').format(date);
    } catch (_) {
      return dateStr.toString();
    }
  }

  void _downloadInvoice(BuildContext context, int bookingId) async {
      final url = await Provider.of<BookingProvider>(context, listen: false).getInvoiceUrl(bookingId);
      if (url != null) {
          final uri = Uri.parse(url);
          if (await canLaunchUrl(uri)) {
             await launchUrl(uri, mode: LaunchMode.externalApplication);
          } else {
             if (mounted) ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Could not launch invoice')));
          }
      }
  }
}
