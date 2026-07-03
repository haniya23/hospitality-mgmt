import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/booking_provider.dart';
import 'main_layout.dart';
import 'package:share_plus/share_plus.dart';
import 'package:url_launcher/url_launcher.dart';
import 'booking_details_screen.dart';
import 'create_booking_screen.dart';

class BookingsTab extends StatefulWidget {
  final VoidCallback? onAddBooking;

  const BookingsTab({super.key, this.onAddBooking});

  @override
  State<BookingsTab> createState() => _BookingsTabState();
}

class _BookingsTabState extends State<BookingsTab> {
  int _selectedIndex =
      0; // 0: Pending, 1: Confirmed, 2: Completed, 3: Cancelled
  bool _isRefundSubmitting = false;

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      _loadData();
    });
  }

  void _loadData() {
    final provider = Provider.of<BookingProvider>(context, listen: false);
    provider.fetchCounts();
    _fetchBookingsForCurrentTab();
  }

  void _fetchBookingsForCurrentTab() {
    final provider = Provider.of<BookingProvider>(context, listen: false);
    String status;
    switch (_selectedIndex) {
      case 0:
        status = 'pending';
        break;
      case 1:
        status = 'confirmed';
        break;
      case 2:
        status = 'checked_out';
        break;
      case 3:
        status = 'cancelled';
        break;
      default:
        status = 'pending';
    }
    provider.fetchBookings(status: status);
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<BookingProvider>(context);
    final bookings = provider.bookings;
    final counts = provider.counts;

    return Scaffold(
      backgroundColor: const Color(0xFFF2F5F0), // Organic warm cream background
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.menu_rounded, color: Color(0xFF191D19)),
          onPressed: () => MainLayout.scaffoldKey.currentState?.openDrawer(),
        ),
        title: Text(
          'Bookings',
          style: GoogleFonts.outfit(
            fontWeight: FontWeight.bold,
            color: const Color(0xFF191D19),
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.add_rounded, color: Color(0xFF191D19)),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const CreateBookingScreen(),
                ),
              ).then((_) {
                provider.fetchCounts();
                _fetchBookingsForCurrentTab();
              });
            },
          ),
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: Color(0xFF191D19)),
            onPressed: () {
              provider.fetchCounts();
              _fetchBookingsForCurrentTab();
            },
          ),
        ],
      ),
      body: Stack(
        children: [
          Column(
            children: [
              _buildStatusCards(counts),
              const SizedBox(height: 8),
              Expanded(
                child: provider.isLoading && bookings.isEmpty
                    ? const Center(child: CircularProgressIndicator())
                    : RefreshIndicator(
                        onRefresh: () async {
                          provider.fetchCounts();
                          _fetchBookingsForCurrentTab();
                        },
                        child: _buildBookingList(bookings),
                      ),
              ),
            ],
          ),
          if (provider.isLoading && bookings.isNotEmpty)
            Positioned(
              top: 0,
              left: 16,
              right: 16,
              child: _buildLoadingBanner(provider.loadingMessage),
            ),
        ],
      ),
    );
  }

  Widget _buildLoadingBanner(String message) {
    return IgnorePointer(
      ignoring: true,
      child: Container(
        margin: const EdgeInsets.only(top: 8),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color: const Color(0xFF2E3E2A),
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.08),
              blurRadius: 16,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: Row(
          children: [
            const SizedBox(
              width: 18,
              height: 18,
              child: CircularProgressIndicator(
                strokeWidth: 2.2,
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xFFFFE8B6)),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                message,
                style: GoogleFonts.outfit(
                  color: Colors.white,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusCards(Map<String, int> counts) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
      color: Colors.transparent,
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          children: [
            _buildStatusCardWrapper(
              0,
              'Pending',
              counts['pending'] ?? 0,
              const Color(0xFFF59E0B),
              Icons.pending_actions_rounded,
            ),
            const SizedBox(width: 8),
            _buildStatusCardWrapper(
              1,
              'Confirmed',
              counts['confirmed'] ?? 0,
              const Color(0xFF3B82F6),
              Icons.check_circle_rounded,
            ),
            const SizedBox(width: 8),
            _buildStatusCardWrapper(
              2,
              'Completed',
              counts['completed'] ?? 0,
              const Color(0xFF10B981),
              Icons.task_alt_rounded,
            ),
            const SizedBox(width: 8),
            _buildStatusCardWrapper(
              3,
              'Cancelled',
              counts['cancelled'] ?? 0,
              const Color(0xFFEF4444),
              Icons.cancel_outlined,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusCardWrapper(
    int index,
    String title,
    int count,
    Color color,
    IconData icon,
  ) {
    final isSelected = _selectedIndex == index;
    return SizedBox(
      width: isSelected ? 132 : 84,
      child: _buildSingleStatusCard(
        index: index,
        title: title,
        count: count,
        primaryColor: color,
        icon: icon,
      ),
    );
  }

  Widget _buildSingleStatusCard({
    required int index,
    required String title,
    required int count,
    required Color primaryColor,
    required IconData icon,
  }) {
    final isSelected = _selectedIndex == index;
    return GestureDetector(
      onTap: () {
        setState(() {
          _selectedIndex = index;
        });
        _fetchBookingsForCurrentTab();
      },
      child: Container(
        padding: EdgeInsets.symmetric(
          vertical: isSelected ? 12 : 10,
          horizontal: isSelected ? 12 : 8,
        ),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF2E3E2A) : Colors.white,
          borderRadius: BorderRadius.circular(22),
          border: Border.all(
            color: isSelected
                ? const Color(0xFF2E3E2A)
                : const Color(0xFF2E3E2A).withOpacity(0.08),
            width: isSelected ? 1.5 : 1,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(
                0xFF2E3E2A,
              ).withOpacity(isSelected ? 0.06 : 0.02),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              color: isSelected
                  ? const Color(0xFFFFE8B6)
                  : primaryColor.withOpacity(0.9),
              size: isSelected ? 20 : 18,
            ),
            SizedBox(height: isSelected ? 6 : 4),
            Text(
              count.toString(),
              style: GoogleFonts.outfit(
                fontSize: isSelected ? 18 : 15,
                fontWeight: FontWeight.bold,
                color: isSelected ? Colors.white : const Color(0xFF191D19),
              ),
            ),
            Text(
              title,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              textAlign: TextAlign.center,
              style: GoogleFonts.outfit(
                fontSize: isSelected ? 11 : 10,
                color: isSelected
                    ? Colors.white.withOpacity(0.8)
                    : Colors.grey[600],
                fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBookingList(List<dynamic> bookings) {
    if (bookings.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.inbox_rounded, size: 64, color: Color(0xFF5A7251)),
            const SizedBox(height: 16),
            Text(
              'No bookings found',
              style: GoogleFonts.outfit(color: Colors.grey[500], fontSize: 16),
            ),
          ],
        ),
      );
    }
    return ListView.separated(
      padding: const EdgeInsets.fromLTRB(
        16,
        16,
        16,
        100,
      ), // padding for bottom navigation
      itemCount: bookings.length,
      separatorBuilder: (_, __) => const SizedBox(height: 16),
      itemBuilder: (context, index) {
        final booking = bookings[index];
        if (_selectedIndex == 3) return _buildCancelledCard(context, booking);
        if (_selectedIndex == 2) return _buildCompletedCard(context, booking);
        return _selectedIndex == 0
            ? _buildPendingCard(context, booking)
            : _buildConfirmedCard(context, booking);
      },
    );
  }

  Widget _buildCancelledCard(
    BuildContext context,
    Map<String, dynamic> booking,
  ) {
    final advancePaid =
        double.tryParse(booking['advance_paid']?.toString() ?? '0') ?? 0;
    final refundAmount =
        double.tryParse(booking['refund_amount']?.toString() ?? '0') ?? 0;
    final remainingRefundable =
        double.tryParse(
          booking['remaining_refundable_amount']?.toString() ?? '',
        ) ??
        (advancePaid - refundAmount);
    final canRefund = remainingRefundable > 0;

    return _buildBaseCard(
      context,
      booking,
      actions: Row(
        children: [
          Expanded(
            child: _buildActionButton(
              Icons.visibility_rounded,
              'View Details',
              Colors.grey.shade700,
              () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => BookingDetailsScreen(
                      bookingId: booking['id'],
                      initialData: booking,
                    ),
                  ),
                );
              },
            ),
          ),
          if (canRefund) ...[
            const SizedBox(width: 8),
            Expanded(
              child: _buildActionButton(
                Icons.undo_rounded,
                'Refund',
                Colors.red.shade700,
                () {
                  _showRefundDialog(context, booking);
                },
              ),
            ),
          ],
        ],
      ),
    );
  }

  void _showRefundDialog(BuildContext context, Map<String, dynamic> booking) {
    final advancePaid =
        double.tryParse(booking['advance_paid']?.toString() ?? '0') ?? 0;
    final refundAmount =
        double.tryParse(booking['refund_amount']?.toString() ?? '0') ?? 0;
    final maxRefund =
        double.tryParse(
          booking['remaining_refundable_amount']?.toString() ?? '',
        ) ??
        (advancePaid - refundAmount);

    final amountController = TextEditingController(
      text: maxRefund.toStringAsFixed(2),
    );
    final reasonController = TextEditingController();
    final formKey = GlobalKey<FormState>();

    showDialog(
      context: context,
      barrierDismissible: !_isRefundSubmitting,
      builder: (dialogContext) => StatefulBuilder(
        builder: (dialogContext, setDialogState) => AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
          title: Row(
            children: [
              Icon(Icons.undo_rounded, color: Colors.red.shade700),
              const SizedBox(width: 8),
              Text(
                'Record Refund',
                style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
              ),
            ],
          ),
          content: Form(
            key: formKey,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Max Refundable: ₹${maxRefund.toStringAsFixed(2)}',
                  style: GoogleFonts.outfit(
                    fontWeight: FontWeight.w600,
                    color: Colors.grey.shade700,
                  ),
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: amountController,
                  keyboardType: const TextInputType.numberWithOptions(
                    decimal: true,
                  ),
                  enabled: !_isRefundSubmitting,
                  autofocus: true,
                  style: GoogleFonts.outfit(fontWeight: FontWeight.w600),
                  decoration: InputDecoration(
                    labelText: 'Refund Amount *',
                    labelStyle: GoogleFonts.outfit(fontSize: 12),
                    prefixText: '₹ ',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Required';
                    final amt = double.tryParse(v);
                    if (amt == null || amt <= 0) return 'Enter a valid amount';
                    if (amt > maxRefund) return 'Cannot exceed max refundable';
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: reasonController,
                  maxLines: 2,
                  enabled: !_isRefundSubmitting,
                  style: GoogleFonts.outfit(fontSize: 13),
                  decoration: InputDecoration(
                    labelText: 'Reason / Notes',
                    labelStyle: GoogleFonts.outfit(fontSize: 12),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
                if (_isRefundSubmitting) ...[
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation<Color>(
                            Colors.red.shade700,
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          'Recording refund and updating finance...',
                          style: GoogleFonts.outfit(
                            fontSize: 12,
                            color: Colors.grey.shade700,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: _isRefundSubmitting
                  ? null
                  : () => Navigator.pop(dialogContext),
              child: Text(
                'Cancel',
                style: GoogleFonts.outfit(
                  color: Colors.grey.shade700,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red.shade700,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
              onPressed: _isRefundSubmitting
                  ? null
                  : () async {
                      if (!formKey.currentState!.validate()) return;
                      final amt = double.parse(amountController.text);
                      final reason = reasonController.text.trim();
                      final scaffoldMessenger = ScaffoldMessenger.of(context);
                      final bookingProv = Provider.of<BookingProvider>(
                        dialogContext,
                        listen: false,
                      );

                      setState(() {
                        _isRefundSubmitting = true;
                      });
                      setDialogState(() {});

                      final success = await bookingProv.recordBookingRefund(
                        booking['uuid'],
                        amt,
                        reason,
                      );

                      if (!mounted) return;

                      setState(() {
                        _isRefundSubmitting = false;
                      });

                      if (dialogContext.mounted) {
                        Navigator.pop(dialogContext);
                      }

                      scaffoldMessenger.showSnackBar(
                        SnackBar(
                          content: Text(
                            success
                                ? 'Refund recorded successfully!'
                                : (bookingProv.error ??
                                      'Failed to record refund'),
                          ),
                          backgroundColor: success ? Colors.green : Colors.red,
                        ),
                      );
                    },
              child: Text(
                _isRefundSubmitting ? 'Recording...' : 'Record Refund',
                style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildCompletedCard(
    BuildContext context,
    Map<String, dynamic> booking,
  ) {
    final balancePending =
        double.tryParse(booking['balance_pending']?.toString() ?? '0') ?? 0;
    return _buildBaseCard(
      context,
      booking,
      actions: Row(
        children: [
          Expanded(
            child: _buildActionButton(
              Icons.visibility_rounded,
              'View Details',
              Colors.grey.shade700,
              () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => BookingDetailsScreen(
                      bookingId: booking['id'],
                      initialData: booking,
                    ),
                  ),
                );
              },
            ),
          ),
          if (balancePending > 0) ...[
            const SizedBox(width: 8),
            Expanded(
              child: _buildActionButton(
                Icons.payments_rounded,
                'Collect Payment',
                Colors.green,
                () {
                  _showPaymentCollectionDialog(context, booking);
                },
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildPendingCard(BuildContext context, Map<String, dynamic> booking) {
    return _buildBaseCard(
      context,
      booking,
      actions: Row(
        children: [
          Expanded(
            child: _buildActionButton(
              Icons.visibility_rounded,
              'View Details',
              Colors.grey.shade700,
              () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => BookingDetailsScreen(
                      bookingId: booking['id'],
                      initialData: booking,
                    ),
                  ),
                );
              },
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: _buildActionButton(
              Icons.check_circle_rounded,
              'Approve',
              const Color(0xFF2E3E2A),
              () {
                _showConfirmDialog(context, booking);
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildConfirmedCard(
    BuildContext context,
    Map<String, dynamic> booking,
  ) {
    final balancePending =
        double.tryParse(booking['balance_pending']?.toString() ?? '0') ?? 0;
    return _buildBaseCard(
      context,
      booking,
      actions: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: _buildActionButton(
                  Icons.visibility_rounded,
                  'View Details',
                  Colors.grey.shade700,
                  () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => BookingDetailsScreen(
                          bookingId: booking['id'],
                          initialData: booking,
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _buildActionButton(
                  Icons.share_rounded,
                  'Share Booking',
                  const Color(0xFF5A7251),
                  () {
                    _shareBooking(booking);
                  },
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: _buildActionButton(
                  Icons.download_rounded,
                  'Invoice',
                  Colors.blue.shade800,
                  () {
                    _downloadInvoice(context, booking['id']);
                  },
                ),
              ),
              const SizedBox(width: 8),
              Expanded(
                child: _buildActionButton(
                  balancePending > 0
                      ? Icons.payments_rounded
                      : Icons.cancel_rounded,
                  balancePending > 0 ? 'Collect Payment' : 'Cancel Booking',
                  balancePending > 0
                      ? Colors.green.shade700
                      : Colors.red.shade700,
                  () {
                    if (balancePending > 0) {
                      _showPaymentCollectionDialog(context, booking);
                    } else {
                      _showCancelDialog(context, booking['id']);
                    }
                  },
                ),
              ),
            ],
          ),
          if (balancePending > 0) ...[
            const SizedBox(height: 8),
            SizedBox(
              width: double.infinity,
              child: _buildActionButton(
                Icons.cancel_rounded,
                'Cancel Booking',
                Colors.red.shade700,
                () {
                  _showCancelDialog(context, booking['id']);
                },
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildBaseCard(
    BuildContext context,
    Map<String, dynamic> booking, {
    required Widget actions,
  }) {
    String guestName = 'Guest';
    if (booking['guest'] != null && booking['guest']['name'] != null) {
      guestName = booking['guest']['name'];
    } else if (booking['b2b_partner'] != null &&
        booking['b2b_partner']['name'] != null) {
      guestName = booking['b2b_partner']['name'];
    } else if (booking['guest_name'] != null) {
      guestName = booking['guest_name'];
    }

    final propertyName =
        booking['accommodation']?['property']?['name'] ?? 'Property';
    final accName =
        booking['accommodation']?['display_name'] ??
        booking['accommodation']?['name'] ??
        'Unit';
    final checkIn = DateTime.parse(booking['check_in_date']);
    final checkOut = DateTime.parse(booking['check_out_date']);
    final nights = checkOut.difference(checkIn).inDays;

    final total =
        double.tryParse(booking['total_amount']?.toString() ?? '0') ?? 0;
    final pending =
        double.tryParse(booking['balance_pending']?.toString() ?? '0') ?? 0;
    final advancePaid =
        double.tryParse(booking['advance_paid']?.toString() ?? '0') ?? 0;
    final refundAmount =
        double.tryParse(booking['refund_amount']?.toString() ?? '0') ?? 0;
    final isB2B = booking['b2b_partner_id'] != null;
    final status = booking['status'].toString();

    String? imageUrl;
    final photos = booking['accommodation']?['photos'] as List?;
    if (photos != null && photos.isNotEmpty) {
      imageUrl = photos[0]['url'];
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: const Color(0xFF2E3E2A).withOpacity(0.08),
          width: 1.5,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF2E3E2A).withOpacity(0.02),
            blurRadius: 15,
            offset: const Offset(0, 8),
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
                    borderRadius: BorderRadius.circular(18),
                    color: const Color(0xFFF2F5F0),
                    image: imageUrl != null
                        ? DecorationImage(
                            image: NetworkImage(imageUrl),
                            fit: BoxFit.cover,
                          )
                        : null,
                  ),
                  child: imageUrl == null
                      ? const Icon(
                          Icons.apartment_rounded,
                          color: Color(0xFF5A7251),
                          size: 36,
                        )
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
                              style: GoogleFonts.outfit(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                                color: const Color(0xFF191D19),
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          _buildStatusBadge(status),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(
                        accName,
                        style: GoogleFonts.outfit(
                          fontSize: 13,
                          color: Colors.grey[600],
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 8),
                      // Guest Row
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              color: isB2B
                                  ? const Color(0xFFB8B8FF).withOpacity(0.2)
                                  : const Color(0xFFFFE8B6),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Icon(
                              isB2B
                                  ? Icons.business_rounded
                                  : Icons.person_rounded,
                              size: 14,
                              color: const Color(0xFF2E3E2A),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              guestName,
                              style: GoogleFonts.outfit(
                                fontWeight: FontWeight.w600,
                                fontSize: 14,
                                color: const Color(0xFF191D19),
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          const Divider(height: 1, color: Color(0xFFEBF0E6)),

          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                // Dates Row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'CHECK IN',
                          style: GoogleFonts.outfit(
                            fontSize: 10,
                            color: Colors.grey,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          DateFormat('dd MMM yyyy').format(checkIn),
                          style: GoogleFonts.outfit(
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                            color: const Color(0xFF191D19),
                          ),
                        ),
                      ],
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF2F5F0),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        '$nights ${nights == 1 ? "Night" : "Nights"}',
                        style: GoogleFonts.outfit(
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                        ),
                      ),
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(
                          'CHECK OUT',
                          style: GoogleFonts.outfit(
                            fontSize: 10,
                            color: Colors.grey,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          DateFormat('dd MMM yyyy').format(checkOut),
                          style: GoogleFonts.outfit(
                            fontWeight: FontWeight.bold,
                            fontSize: 13,
                            color: const Color(0xFF191D19),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Finance Row
                status == 'cancelled'
                    ? Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'TOTAL AMOUNT',
                                style: GoogleFonts.outfit(
                                  fontSize: 10,
                                  color: Colors.grey,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                '₹${total.toStringAsFixed(2)}',
                                style: GoogleFonts.outfit(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 13,
                                  color: const Color(0xFF191D19),
                                ),
                              ),
                            ],
                          ),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.center,
                            children: [
                              Text(
                                'MONEY COLLECTED',
                                style: GoogleFonts.outfit(
                                  fontSize: 10,
                                  color: Colors.grey,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                '₹${advancePaid.toStringAsFixed(2)}',
                                style: GoogleFonts.outfit(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 13,
                                  color: Colors.blue.shade700,
                                ),
                              ),
                            ],
                          ),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text(
                                'REFUNDED',
                                style: GoogleFonts.outfit(
                                  fontSize: 10,
                                  color: Colors.grey,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                '₹${refundAmount.toStringAsFixed(2)}',
                                style: GoogleFonts.outfit(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 13,
                                  color: Colors.red.shade700,
                                ),
                              ),
                            ],
                          ),
                        ],
                      )
                    : Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'TOTAL AMOUNT',
                                style: GoogleFonts.outfit(
                                  fontSize: 10,
                                  color: Colors.grey,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                '₹${total.toStringAsFixed(2)}',
                                style: GoogleFonts.outfit(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 15,
                                  color: const Color(0xFF191D19),
                                ),
                              ),
                            ],
                          ),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.end,
                            children: [
                              Text(
                                'BALANCE PENDING',
                                style: GoogleFonts.outfit(
                                  fontSize: 10,
                                  color: Colors.grey,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                '₹${pending.toStringAsFixed(2)}',
                                style: GoogleFonts.outfit(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 15,
                                  color: pending > 0
                                      ? Colors.red.shade700
                                      : Colors.green.shade700,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),

                const SizedBox(height: 16),
                actions,
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
        bg = const Color(0xFFD4EED8); // Soft green
        text = const Color(0xFF166534);
        break;
      case 'pending':
        bg = const Color(0xFFFFE8B6); // Soft yellow
        text = const Color(0xFF854D0E);
        break;
      case 'cancelled':
        bg = const Color(0xFFFEE2E2); // Soft red
        text = const Color(0xFF991B1B);
        break;
      case 'checked_out':
        bg = Colors.grey.shade200;
        text = Colors.grey.shade800;
        break;
      default:
        bg = Colors.grey.shade100;
        text = Colors.grey.shade700;
    }
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        status.toUpperCase(),
        style: GoogleFonts.outfit(
          fontSize: 9,
          fontWeight: FontWeight.bold,
          color: text,
        ),
      ),
    );
  }

  Widget _buildActionButton(
    IconData icon,
    String label,
    Color color,
    VoidCallback onTap,
  ) {
    return ElevatedButton(
      onPressed: onTap,
      style: ElevatedButton.styleFrom(
        backgroundColor: color,
        foregroundColor: Colors.white,
        padding: const EdgeInsets.symmetric(vertical: 12),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        elevation: 0,
        textStyle: GoogleFonts.outfit(
          fontSize: 12,
          fontWeight: FontWeight.bold,
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        mainAxisSize: MainAxisSize.min,
        children: [Icon(icon, size: 16), const SizedBox(width: 6), Text(label)],
      ),
    );
  }

  void _showConfirmDialog(BuildContext context, Map<String, dynamic> booking) {
    final guestName = booking['guest']?['name'] ?? 'Guest';
    final dates =
        '${_formatDate(booking['check_in_date'])} - ${_formatDate(booking['check_out_date'])}';

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: const Color(0xFFF2F5F0),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Row(
          children: [
            const Icon(
              Icons.check_circle_rounded,
              color: Color(0xFF2E3E2A),
              size: 28,
            ),
            const SizedBox(width: 12),
            Text(
              'Confirm Request?',
              style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Are you sure you want to approve this booking?',
              style: GoogleFonts.outfit(fontSize: 15),
            ),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(
                  color: const Color(0xFF2E3E2A).withOpacity(0.08),
                ),
              ),
              child: Column(
                children: [
                  _buildDialogRow('Guest:', guestName),
                  const SizedBox(height: 8),
                  _buildDialogRow('Dates:', dates),
                  const SizedBox(height: 8),
                  _buildDialogRow('Total:', '₹${booking['total_amount']}'),
                  const SizedBox(height: 12),
                  const Text(
                    'Note: This will mark the booking as confirmed.',
                    style: TextStyle(fontSize: 11, color: Colors.grey),
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              'Cancel',
              style: GoogleFonts.outfit(
                color: Colors.grey[600],
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              _updateStatus(context, booking['id'], 'confirmed');
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF2E3E2A),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
            ),
            child: Text(
              'Confirm Approval',
              style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
            ),
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
          child: Text(
            label,
            style: GoogleFonts.outfit(color: Colors.grey[500], fontSize: 13),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: GoogleFonts.outfit(
              fontWeight: FontWeight.bold,
              fontSize: 13,
              color: const Color(0xFF191D19),
            ),
          ),
        ),
      ],
    );
  }

  void _updateStatus(BuildContext context, int id, String status) async {
    final success = await Provider.of<BookingProvider>(
      context,
      listen: false,
    ).updateBookingStatus(id, status);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Booking updated to ${status.toUpperCase()}'),
          backgroundColor: status == 'confirmed' ? Colors.green : Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
      _loadData();
    }
  }

  void _showCancelDialog(BuildContext context, int bookingId) {
    final reasonController = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: const Color(0xFFF2F5F0),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Text(
          'Cancel Booking',
          style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'Please provide a reason for cancellation:',
              style: GoogleFonts.outfit(fontSize: 14),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: reasonController,
              decoration: InputDecoration(
                hintText: 'Reason (e.g. Guest request, No show)',
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14),
                ),
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
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Reason is required')),
                );
                return;
              }
              Navigator.pop(context);
              final success =
                  await Provider.of<BookingProvider>(
                    context,
                    listen: false,
                  ).updateBookingStatus(
                    bookingId,
                    'cancelled',
                    reason: reasonController.text,
                  );
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Booking cancelled successfully'),
                  ),
                );
                _loadData();
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red.shade700,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: Text(
              'Confirm Cancel',
              style: GoogleFonts.outfit(
                color: Colors.white,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }

  void _shareBooking(Map<String, dynamic> booking) {
    final guest = booking['guest']['name'];
    final prop = booking['accommodation']['property']['name'];
    final dates =
        '${_formatDate(booking['check_in_date'])} to ${_formatDate(booking['check_out_date'])}';
    final text =
        'Booking Confirmed!\nGuest: $guest\nProperty: $prop\nDates: $dates\nTotal: ₹${booking['total_amount']}';
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
    final url = await Provider.of<BookingProvider>(
      context,
      listen: false,
    ).getInvoiceUrl(bookingId);
    if (url != null) {
      final uri = Uri.parse(url);
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      } else {
        if (mounted)
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Could not launch invoice')),
          );
      }
    }
  }

  void _showPaymentCollectionDialog(
    BuildContext context,
    Map<String, dynamic> booking,
  ) {
    final balancePending =
        double.tryParse(booking['balance_pending']?.toString() ?? '0') ?? 0;
    final amountController = TextEditingController(
      text: balancePending.toStringAsFixed(2),
    );
    final notesController = TextEditingController();
    final guestName = booking['guest']?['name'] ?? 'Guest';

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: const Color(0xFFF2F5F0),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Collect Payment',
              style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 4),
            Text(
              guestName,
              style: GoogleFonts.outfit(
                fontSize: 14,
                color: Colors.grey.shade600,
              ),
            ),
          ],
        ),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'Current Balance:',
                      style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                        color: Colors.red.shade900,
                      ),
                    ),
                    Text(
                      '₹${balancePending.toStringAsFixed(2)}',
                      style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold,
                        color: Colors.red.shade700,
                        fontSize: 18,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              Text(
                'Amount to Collect *',
                style: GoogleFonts.outfit(
                  fontWeight: FontWeight.bold,
                  fontSize: 13,
                  color: const Color(0xFF2E3E2A),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: amountController,
                keyboardType: const TextInputType.numberWithOptions(
                  decimal: true,
                ),
                decoration: InputDecoration(
                  hintText: 'Enter amount',
                  prefixText: '₹ ',
                  filled: true,
                  fillColor: Colors.white,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 14,
                  ),
                ),
              ),
              const SizedBox(height: 16),
              Text(
                'Payment Notes (Optional)',
                style: GoogleFonts.outfit(
                  fontWeight: FontWeight.bold,
                  fontSize: 13,
                  color: const Color(0xFF2E3E2A),
                ),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: notesController,
                decoration: InputDecoration(
                  hintText: 'Add any notes...',
                  filled: true,
                  fillColor: Colors.white,
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 14,
                  ),
                ),
                maxLines: 2,
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text(
              'Cancel',
              style: GoogleFonts.outfit(color: Colors.grey),
            ),
          ),
          ElevatedButton.icon(
            onPressed: () async {
              final amount = double.tryParse(amountController.text);
              if (amount == null || amount <= 0) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Please enter a valid amount')),
                );
                return;
              }
              Navigator.pop(context);
              final success =
                  await Provider.of<BookingProvider>(
                    context,
                    listen: false,
                  ).updateBookingPayment(
                    booking['uuid'],
                    amount,
                    notesController.text,
                  );
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Payment collected successfully!'),
                    backgroundColor: Colors.green,
                  ),
                );
                _loadData();
              } else if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      Provider.of<BookingProvider>(
                            context,
                            listen: false,
                          ).error ??
                          'Failed',
                    ),
                    backgroundColor: Colors.red,
                  ),
                );
              }
            },
            icon: const Icon(Icons.check, size: 16),
            label: Text(
              'Collect Payment',
              style: GoogleFonts.outfit(fontWeight: FontWeight.bold),
            ),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF2E3E2A),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
