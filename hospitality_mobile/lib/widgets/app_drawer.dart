import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';

class AppDrawer extends StatelessWidget {
  final int currentIndex;
  final Function(int) onItemSelected;

  const AppDrawer({
    super.key,
    required this.currentIndex,
    required this.onItemSelected,
  });

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    
    return Drawer(
      child: Column(
        children: [
          UserAccountsDrawerHeader(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF2563EB), Color(0xFF1E3A8A)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
            ),
            currentAccountPicture: CircleAvatar(
              backgroundColor: Colors.white,
              child: Text(
                auth.userName?.substring(0, 1).toUpperCase() ?? 'U',
                style: GoogleFonts.poppins(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF1E3A8A),
                ),
              ),
            ),
            accountName: Text(
              auth.userName ?? 'User',
              style: GoogleFonts.poppins(fontWeight: FontWeight.bold),
            ),
            accountEmail: Text(
              'Owner', // Or fetching email if available
              style: GoogleFonts.poppins(fontSize: 12, color: Colors.white70),
            ),
          ),
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                _buildDrawerItem(0, Icons.dashboard_outlined, 'Dashboard', context),
                _buildDrawerItem(1, Icons.calendar_month_outlined, 'Bookings', context),
                _buildDrawerItem(8, Icons.calendar_today_outlined, 'Calendar', context),
                _buildDrawerItem(7, Icons.room_service_outlined, 'Guest Services', context),
                _buildDrawerItem(2, Icons.apartment_outlined, 'Properties', context),
                _buildDrawerItem(6, Icons.hotel_outlined, 'Accommodations', context),
                _buildDrawerItem(3, Icons.handshake_outlined, 'B2B Partners', context),
                _buildDrawerItem(4, Icons.people_outline, 'Guests', context),
                const Divider(),
                _buildDrawerItem(5, Icons.person_outline, 'Profile', context),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: Text(
              'v1.0.0', // Version
              style: GoogleFonts.poppins(color: Colors.grey[400], fontSize: 12),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDrawerItem(int index, IconData icon, String title, BuildContext context) {
    final isSelected = currentIndex == index;
    return ListTile(
      leading: Icon(
        icon,
        color: isSelected ? const Color(0xFF2563EB) : Colors.grey[600],
      ),
      title: Text(
        title,
        style: GoogleFonts.poppins(
          fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
          color: isSelected ? const Color(0xFF2563EB) : Colors.black87,
        ),
      ),
      selected: isSelected,
      selectedTileColor: Colors.blue.shade50,
      onTap: () {
        Navigator.pop(context); // Close drawer
        onItemSelected(index);
      },
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(0), // Full width highlight or rounded? 
        // Typically drawers use rectangular tiles or rounded at end. 
        // Keeping it simple.
      ),
    );
  }
}
