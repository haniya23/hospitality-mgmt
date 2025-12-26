import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../providers/riverpod_providers.dart';
import '../providers/booking_provider.dart';
import 'checkin_form_screen.dart';
import 'checkout_form_screen.dart';
import 'checkin_details_screen.dart';
import 'checkout_details_screen.dart';

class GuestServicesScreen extends ConsumerStatefulWidget {
  const GuestServicesScreen({super.key});

  @override
  ConsumerState<GuestServicesScreen> createState() => _GuestServicesScreenState();
}

class _GuestServicesScreenState extends ConsumerState<GuestServicesScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final DateTime _today = DateTime.now();
  bool _showAll = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _refreshData();
  }

  Future<void> _refreshData() async {
    final dateStr = DateFormat('yyyy-MM-dd').format(_today);
    // Fetch all required data
    await Future.wait([
      ref.read(bookingProvider).fetchCheckIns(dateStr, showAll: _showAll),
      ref.read(bookingProvider).fetchCheckOuts(dateStr, showAll: _showAll),
      ref.read(bookingProvider).fetchCheckInHistory(),
      ref.read(bookingProvider).fetchCheckOutHistory(),
    ]);
  }

  @override
  Widget build(BuildContext context) {
    final bookingProv = ref.watch(bookingProvider);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'Guest Services',
          style: GoogleFonts.outfit(
            color: const Color(0xFF1E293B),
            fontWeight: FontWeight.w600,
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Color(0xFF1E293B)),
        bottom: TabBar(
          controller: _tabController,
          labelColor: const Color(0xFF4F46E5),
          unselectedLabelColor: const Color(0xFF64748B),
          labelStyle: GoogleFonts.outfit(fontWeight: FontWeight.w600),
          indicatorColor: const Color(0xFF4F46E5),
          isScrollable: true,
          tabs: const [
            Tab(text: 'Ready for Check-in'),
            Tab(text: 'Check-out Pending'),
            Tab(text: 'Recent Check-ins'),
            Tab(text: 'Recent Check-outs'),
          ],
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 16.0),
            child: PopupMenuButton<bool>(
              icon: const Icon(Icons.filter_list),
              onSelected: (val) {
                if (_showAll != val) {
                  setState(() => _showAll = val);
                  _refreshData();
                }
              },
              itemBuilder: (context) => [
                const PopupMenuItem(value: false, child: Text('Today Only')),
                const PopupMenuItem(value: true, child: Text('All Upcoming')),
              ],
            ),
          ),
        ],
      ),
      body: bookingProv.isLoading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
              controller: _tabController,
              children: [
                _buildList(bookingProv.checkIns, isCheckIn: true, isHistory: false),
                _buildList(bookingProv.checkOuts, isCheckIn: false, isHistory: false),
                _buildList(bookingProv.recentCheckIns, isCheckIn: true, isHistory: true),
                _buildList(bookingProv.recentCheckOuts, isCheckIn: false, isHistory: true),
              ],
            ),
    );
  }

  Widget _buildList(List bookings, {required bool isCheckIn, required bool isHistory}) {
    if (bookings.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              isCheckIn ? Icons.login_rounded : Icons.logout_rounded,
              size: 64,
              color: Colors.grey.shade300,
            ),
            const SizedBox(height: 16),
            Text(
              isHistory 
                  ? (isCheckIn ? 'No recent check-ins' : 'No recent check-outs')
                  : (isCheckIn ? 'No pending check-ins' : 'No pending check-outs'),
              style: GoogleFonts.outfit(
                fontSize: 16,
                color: Colors.grey.shade500,
              ),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _refreshData,
      child: ListView.separated(
        padding: const EdgeInsets.all(20),
        itemCount: bookings.length,
        separatorBuilder: (_, __) => const SizedBox(height: 16),
        itemBuilder: (context, index) {
          final booking = bookings[index];
          return _buildBookingCard(booking, isCheckIn, isHistory)
              .animate()
              .fadeIn(delay: (50 * index).ms)
              .slideY(begin: 0.1, end: 0);
        },
      ),
    );
  }

  Widget _buildBookingCard(Map<String, dynamic> booking, bool isCheckIn, bool isHistory) {
    // Determine data paths based on history (CheckIn/CheckOut model) vs pending (Reservation model)
    final guestName = booking['guest']?['name'] ?? booking['guest_name'] ?? 'Unknown Guest';
    
    // For history, accommodation info is nested under 'reservation'
    final accommodation = isHistory 
        ? booking['reservation']?['accommodation']
        : booking['accommodation'];
        
    final propertyName = accommodation?['property']?['name'] ?? 'Property';
    final roomName = accommodation?['custom_name'] ?? accommodation?['name'] ?? 'Room';
    
    final dateStr = isHistory
        ? (isCheckIn ? booking['check_in_time'] : booking['check_out_time'])
        : (isCheckIn ? booking['check_in_date'] : booking['check_out_date']);
        
    final parsedDate = DateTime.tryParse(dateStr ?? '');
    final dateDisplay = parsedDate != null 
        ? DateFormat(isHistory ? 'MMM d, h:mm a' : 'MMM d').format(parsedDate) 
        : 'Unknown';

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 16,
            offset: const Offset(0, 4),
          ),
        ],
        border: Border.all(color: Colors.grey.shade100),
      ),
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    guestName,
                    style: GoogleFonts.outfit(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: const Color(0xFF1E293B),
                    ),
                  ),
                  Text(
                    '$propertyName • $roomName',
                    style: GoogleFonts.outfit(
                      fontSize: 13,
                      color: const Color(0xFF64748B),
                    ),
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: (isCheckIn ? Colors.indigo : Colors.orange).withValues(alpha: 0.1),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  isCheckIn ? Icons.login : Icons.logout,
                  color: isCheckIn ? Colors.indigo : Colors.orange,
                  size: 20,
                ),
              ),
            ],
          ),
          const Divider(height: 24),
          Row(
            children: [
              _buildInfoChip(
                  Icons.calendar_today, 
                  isHistory 
                      ? (isCheckIn ? 'Checked in $dateDisplay' : 'Checked out $dateDisplay')
                      : (isCheckIn ? 'Arriving $dateDisplay' : 'Departing $dateDisplay')
              ),
              const Spacer(),
              ElevatedButton.icon(
                onPressed: () => isHistory 
                    ? _viewDetails(context, booking['uuid'], isCheckIn)
                    : _handleStatusChange(booking['id'], isCheckIn),
                icon: Icon(isHistory ? Icons.visibility : (isCheckIn ? Icons.check : Icons.done_all), size: 16),
                label: Text(isHistory ? 'View Details' : (isCheckIn ? 'Check In' : 'Check Out')),
                style: ElevatedButton.styleFrom(
                  backgroundColor: isHistory 
                      ? Colors.white 
                      : (isCheckIn ? const Color(0xFF4F46E5) : const Color(0xFFF59E0B)),
                  foregroundColor: isHistory ? Colors.blue.shade700 : Colors.white,
                  elevation: isHistory ? 0 : 2,
                  side: isHistory ? BorderSide(color: Colors.blue.shade200) : null,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildInfoChip(IconData icon, String label) {
    return Row(
      children: [
        Icon(icon, size: 14, color: Colors.grey.shade400),
        const SizedBox(width: 6),
        Text(
          label,
          style: GoogleFonts.outfit(
            fontSize: 13,
            color: Colors.grey.shade600,
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  Future<void> _handleStatusChange(int bookingId, bool isCheckIn) async {
    final bookingProv = ref.read(bookingProvider);
    final booking = isCheckIn 
        ? bookingProv.checkIns.firstWhere((b) => b['id'] == bookingId)
        : bookingProv.checkOuts.firstWhere((b) => b['id'] == bookingId);

    if (isCheckIn) {
      final result = await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => CheckInFormScreen(booking: booking),
        ),
      );
      if (result == true) _refreshData();
    } else {
      final result = await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => CheckOutFormScreen(booking: booking),
        ),
      );
      if (result == true) _refreshData();
    }
  }

  Future<void> _viewDetails(BuildContext context, String uuid, bool isCheckIn) async {
    final bookingProv = ref.read(bookingProvider);
    
    // Show loading
    showDialog(context: context, barrierDismissible: false, builder: (_) => const Center(child: CircularProgressIndicator()));
    
    try {
      if (isCheckIn) {
        final details = await bookingProv.fetchCheckInDetails(uuid);
        Navigator.pop(context); // hide loading
        if (details != null) {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => CheckInDetailsScreen(checkIn: details)),
          );
        }
      } else {
        final details = await bookingProv.fetchCheckOutDetails(uuid);
        Navigator.pop(context);
        if (details != null) {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => CheckOutDetailsScreen(checkOut: details)),
          );
        }
      }
    } catch (e) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error loading details: $e')));
    }
  }
}
