import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'home_screen.dart';
import 'bookings_tab.dart';
import 'properties_tab.dart';
import 'b2b_tab.dart';
import 'guests_tab.dart';
import 'profile_tab.dart';
import 'accommodations_tab.dart';
import 'guest_services_screen.dart';
import 'calendar_screen.dart';
import '../widgets/app_drawer.dart';

class MainLayout extends StatefulWidget {
  static final GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();
  const MainLayout({super.key});

  @override
  State<MainLayout> createState() => _MainLayoutState();
}

class _MainLayoutState extends State<MainLayout> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    const HomeScreen(),
    const BookingsTab(),
    const PropertiesTab(),
    const B2bTab(),
    const GuestsTab(),
    const ProfileTab(),
    const AccommodationsTab(),
    const GuestServicesScreen(),
    const CalendarScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      key: MainLayout.scaffoldKey,
      backgroundColor: const Color(0xFFF2F5F0), // Organic warm cream background
      drawer: AppDrawer(
        currentIndex: _currentIndex,
        onItemSelected: (index) {
          setState(() => _currentIndex = index);
        },
      ),
      body: SafeArea(
        child: IndexedStack(
          index: _currentIndex,
          children: _screens,
        ),
      ),
      bottomNavigationBar: Container(
        margin: const EdgeInsets.fromLTRB(20, 0, 20, 24),
        height: 68,
        decoration: BoxDecoration(
          color: const Color(0xFF191D19), // Dark organic charcoal
          borderRadius: BorderRadius.circular(34),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF2E3E2A).withOpacity(0.12),
              blurRadius: 20,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 8),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceAround,
            children: [
              _buildBottomNavItem(0, Icons.donut_large_rounded, 'Dashboard'),
              _buildBottomNavItem(1, Icons.calendar_month_rounded, 'Bookings'),
              _buildBottomNavItem(2, Icons.apartment_rounded, 'Properties'),
              _buildBottomNavItem(6, Icons.hotel_rounded, 'Rooms'),
              _buildBottomNavItem(5, Icons.person_rounded, 'Profile'),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBottomNavItem(int targetIndex, IconData icon, String label) {
    final isSelected = _currentIndex == targetIndex;
    
    return GestureDetector(
      onTap: () {
        setState(() {
          _currentIndex = targetIndex;
        });
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: isSelected ? const Color(0xFF2E3E2A) : Colors.transparent, // Deep green highlight
          borderRadius: BorderRadius.circular(20),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              color: isSelected ? const Color(0xFFFFE8B6) : Colors.grey[500], // Yellow active from screenshot
              size: 22,
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: GoogleFonts.outfit(
                color: isSelected ? Colors.white : Colors.grey[500],
                fontSize: 10,
                fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
