import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../providers/riverpod_providers.dart';
import '../providers/booking_provider.dart';
import 'checkin_form_screen.dart';
import 'checkout_form_screen.dart';

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
    _tabController = TabController(length: 2, vsync: this);
    _refreshData();
  }

  Future<void> _refreshData() async {
    final dateStr = DateFormat('yyyy-MM-dd').format(_today);
    // Fetch parallel
    await Future.wait([
      ref.read(bookingProvider).fetchCheckIns(dateStr, showAll: _showAll),
      ref.read(bookingProvider).fetchCheckOuts(dateStr, showAll: _showAll),
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
          tabs: const [
            Tab(text: 'Ready for Check-in'),
            Tab(text: 'Check-out Pending'),
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
      body: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            color: Colors.white,
            child: Row(
              children: [
                ActionChip(
                  label: const Text('Today'),
                  backgroundColor: !_showAll ? Colors.blue.shade50 : Colors.grey.shade100,
                  labelStyle: GoogleFonts.outfit(
                    color: !_showAll ? Colors.blue.shade700 : Colors.grey.shade700,
                    fontWeight: !_showAll ? FontWeight.bold : FontWeight.normal,
                  ),
                  onPressed: () {
                    if (_showAll) {
                      setState(() => _showAll = false);
                      _refreshData();
                    }
                  },
                ),
                const SizedBox(width: 12),
                ActionChip(
                  label: const Text('upcoming'),
                  backgroundColor: _showAll ? Colors.blue.shade50 : Colors.grey.shade100,
                  labelStyle: GoogleFonts.outfit(
                    color: _showAll ? Colors.blue.shade700 : Colors.grey.shade700,
                    fontWeight: _showAll ? FontWeight.bold : FontWeight.normal,
                  ),
                  onPressed: () {
                    if (!_showAll) {
                      setState(() => _showAll = true);
                      _refreshData();
                    }
                  },
                ),
              ],
            ),
          ),
          Expanded(
            child: bookingProv.isLoading
                ? const Center(child: CircularProgressIndicator())
                : TabBarView(
                    controller: _tabController,
                    children: [
                      _buildList(bookingProv.checkIns, isCheckIn: true),
                      _buildList(bookingProv.checkOuts, isCheckIn: false),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildList(List bookings, {required bool isCheckIn}) {
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
              isCheckIn
                  ? 'No check-ins found'
                  : 'No check-outs found',
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
          return _buildBookingCard(booking, isCheckIn)
              .animate()
              .fadeIn(delay: (50 * index).ms)
              .slideY(begin: 0.1, end: 0);
        },
      ),
    );
  }

  Widget _buildBookingCard(Map<String, dynamic> booking, bool isCheckIn) {
    final guestName = booking['guest']?['name'] ?? 'Unknown Guest';
    final propertyName = booking['accommodation']?['property']?['name'] ?? 'Property';
    final roomName = booking['accommodation']?['name'] ?? 'Room';
    final bookingId = booking['id'];
    
    final dateStr = isCheckIn ? booking['check_in_date'] : booking['check_out_date'];
    final parsedDate = DateTime.tryParse(dateStr ?? '');
    final dateDisplay = parsedDate != null ? DateFormat('d MMM').format(parsedDate) : 'Unknown';

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
                  Icons.calendar_today, isCheckIn ? 'Arriving $dateDisplay' : 'Departing $dateDisplay'),
              const Spacer(),
              ElevatedButton.icon(
                onPressed: () => _handleStatusChange(bookingId, isCheckIn),
                icon: Icon(isCheckIn ? Icons.check : Icons.done_all, size: 16),
                label: Text(isCheckIn ? 'Check In' : 'Check Out'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: isCheckIn ? const Color(0xFF4F46E5) : const Color(0xFFF59E0B),
                  foregroundColor: Colors.white,
                  elevation: 0,
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
    // Find booking data
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
}
