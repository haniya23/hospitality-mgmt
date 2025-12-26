import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import 'package:table_calendar/table_calendar.dart';
import 'package:intl/intl.dart';
import '../providers/booking_provider.dart';
import 'main_layout.dart';

class CalendarScreen extends StatefulWidget {
  const CalendarScreen({super.key});

  @override
  State<CalendarScreen> createState() => _CalendarScreenState();
}

class _CalendarScreenState extends State<CalendarScreen> {
  CalendarFormat _calendarFormat = CalendarFormat.month;
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;
  Map<DateTime, List<dynamic>> _events = {};

  @override
  void initState() {
    super.initState();
    _selectedDay = _focusedDay;
    Future.microtask(() {
      final provider = Provider.of<BookingProvider>(context, listen: false);
      // Fetch all bookings to populate the calendar
      provider.fetchBookings(status: 'all').then((_) {
        _groupBookings(provider.bookings);
      });
    });
  }

  void _groupBookings(List<dynamic> bookings) {
    Map<DateTime, List<dynamic>> data = {};
    for (var booking in bookings) {
      if (booking['status'] == 'cancelled') continue; // Skip cancelled? Or show in red?
      
      final checkIn = DateTime.parse(booking['check_in_date']);
      // Normalizing date to remove time part
      final date = DateTime(checkIn.year, checkIn.month, checkIn.day);
      
      if (data[date] == null) data[date] = [];
      data[date]!.add(booking);
    }
    setState(() {
      _events = data;
    });
  }

  List<dynamic> _getEventsForDay(DateTime day) {
    // Normalizing date
    final date = DateTime(day.year, day.month, day.day);
    return _events[date] ?? [];
  }

  @override
  Widget build(BuildContext context) {
    // Re-group if provider updates (e.g. if we pull to fresh in another tab, but here we can just rely on initState or listen)
    // Actually we should listen to provider changes in build?
    // If we use Consumer, we can rebuild events map when bookings change.
    final bookingProvider = Provider.of<BookingProvider>(context);
    // Optimization: Only regroup if bookings list changed reference or rely on user to refresh.
    // For simplicity, let's just use the local _events map which we built in initState. 
    // Ideally we should use a `useEffect` or `didUpdateWidget` equivalent if using Riverpod/Provider correctly for derived state.
    // Let's do a quick check: if provider.bookings is different from what we thought, maybe update?
    // For now, let's assume static on load or add a refresh button.
    
    // Better: Derive events directly from provider.bookings in build. This ensures reactiveness.
    final bookings = bookingProvider.bookings;
    
    // We can memorize this efficiently, but for N < 1000 it's fast enough to just loop.
    // Let's create the map on the fly or only if bookings changed.
    // To avoid loop in build, I will stick to what I have but add a listener or use Consumer better.
    // For MVP, let's just regroup in build if the list length implies change or just always regroup (cheap for small lists).
    final eventsMap = <DateTime, List<dynamic>>{};
    for (var booking in bookings) {
       if (booking['status'] == 'cancelled') continue;
       final checkIn = DateTime.parse(booking['check_in_date']);
       final date = DateTime(checkIn.year, checkIn.month, checkIn.day);
       if (eventsMap[date] == null) eventsMap[date] = [];
       eventsMap[date]!.add(booking);
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.menu, color: Colors.black87),
          onPressed: () => MainLayout.scaffoldKey.currentState?.openDrawer(),
        ),
        title: Text(
          'Calendar',
          style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: const Color(0xFF1E293B)),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.black87),
            onPressed: () {
               bookingProvider.fetchBookings(status: 'all');
            },
          ),
        ],
      ),
      body: Column(
        children: [
          Container(
            color: Colors.white,
            padding: const EdgeInsets.only(bottom: 8),
            child: TableCalendar(
              firstDay: DateTime.utc(2023, 1, 1),
              lastDay: DateTime.utc(2030, 12, 31),
              focusedDay: _focusedDay,
              calendarFormat: _calendarFormat,
              selectedDayPredicate: (day) {
                return isSameDay(_selectedDay, day);
              },
              onDaySelected: (selectedDay, focusedDay) {
                if (!isSameDay(_selectedDay, selectedDay)) {
                  setState(() {
                    _selectedDay = selectedDay;
                    _focusedDay = focusedDay;
                  });
                }
              },
              onFormatChanged: (format) {
                if (_calendarFormat != format) {
                  setState(() {
                    _calendarFormat = format;
                  });
                }
              },
              onPageChanged: (focusedDay) {
                _focusedDay = focusedDay;
              },
              eventLoader: (day) {
                final date = DateTime(day.year, day.month, day.day);
                return eventsMap[date] ?? [];
              },
              calendarStyle: CalendarStyle(
                todayDecoration: const BoxDecoration(
                  color: Color(0xFF93C5FD), // Light Blue
                  shape: BoxShape.circle,
                ),
                selectedDecoration: const BoxDecoration(
                  color: Color(0xFF2563EB), // Primary Blue
                  shape: BoxShape.circle,
                ),
                markerDecoration: const BoxDecoration(
                  color: Color(0xFF10B981), // Green for events
                  shape: BoxShape.circle,
                ),
              ),
              headerStyle: HeaderStyle(
                formatButtonVisible: false,
                titleCentered: true,
                titleTextStyle: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16),
              ),
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: _buildEventList(eventsMap),
          ),
        ],
      ),
    );
  }

  Widget _buildEventList(Map<DateTime, List<dynamic>> eventsMap) {
    // Normalizing selected day
    final date = DateTime(_selectedDay!.year, _selectedDay!.month, _selectedDay!.day);
    final events = eventsMap[date] ?? [];

    if (events.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.event_busy, size: 64, color: Colors.grey[300]),
            const SizedBox(height: 16),
            Text(
              'No bookings for this day',
              style: GoogleFonts.outfit(color: Colors.grey[500]),
            ),
          ],
        ),
      );
    }

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: events.length,
      separatorBuilder: (_, __) => const SizedBox(height: 16),
      itemBuilder: (context, index) {
        final booking = events[index];
        return _buildBookingCard(booking);
      },
    );
  }

  Widget _buildBookingCard(Map<String, dynamic> booking) {
    final guestName = booking['guest']?['name'] ?? 'Guest';
    final propertyName = booking['accommodation']?['property']?['name'] ?? 'Property';
    final accName = booking['accommodation']?['display_name'] ?? 'Unit';
    final status = booking['status'].toString();
    final checkIn = DateTime.parse(booking['check_in_date']);
    final checkOut = DateTime.parse(booking['check_out_date']);
    final nights = checkOut.difference(checkIn).inDays;

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: ListTile(
        contentPadding: const EdgeInsets.all(12),
        leading: CircleAvatar(
          backgroundColor: Colors.blue.shade50,
          child: Text(
            guestName.substring(0, 1).toUpperCase(),
            style: GoogleFonts.outfit(color: Colors.blue, fontWeight: FontWeight.bold),
          ),
        ),
        title: Text(
          guestName,
          style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 16),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 4),
            Text('$propertyName - $accName', style: GoogleFonts.outfit(fontSize: 13, color: Colors.grey[700])),
            Text(
              '${DateFormat('MMM d').format(checkIn)} - ${DateFormat('MMM d').format(checkOut)} ($nights nights)',
              style: GoogleFonts.outfit(fontSize: 12, color: Colors.grey[500]),
            ),
          ],
        ),
        trailing: Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: status == 'confirmed' ? const Color(0xFFDCFCE7) : const Color(0xFFFEF9C3),
            borderRadius: BorderRadius.circular(4),
          ),
          child: Text(
            status.toUpperCase(),
            style: GoogleFonts.outfit(
              fontSize: 10, 
              fontWeight: FontWeight.bold, 
              color: status == 'confirmed' ? const Color(0xFF166534) : const Color(0xFF854D0E),
            ),
          ),
        ),
      ),
    );
  }
}
