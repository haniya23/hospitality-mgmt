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
    final initials = auth.userName != null && auth.userName!.isNotEmpty
        ? auth.userName!.substring(0, 1).toUpperCase()
        : 'U';
    
    ImageProvider? avatarImage;
    if (auth.profilePhotoUrl != null && auth.profilePhotoUrl!.isNotEmpty) {
      avatarImage = NetworkImage(auth.profilePhotoUrl!);
    }

    return Drawer(
      backgroundColor: const Color(0xFFF2F5F0), // Organic warm cream background
      child: Column(
        children: [
          UserAccountsDrawerHeader(
            decoration: const BoxDecoration(
              color: Color(0xFF2E3E2A), // Deep organic green
            ),
            currentAccountPicture: Container(
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: const Color(0xFFFFE8B6), width: 2.5),
              ),
              child: CircleAvatar(
                backgroundColor: const Color(0xFFFFE8B6),
                backgroundImage: avatarImage,
                child: avatarImage == null
                    ? Text(
                        initials,
                        style: GoogleFonts.outfit(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                        ),
                      )
                    : null,
              ),
            ),
            accountName: Text(
              auth.userName ?? 'User',
              style: GoogleFonts.outfit(
                fontWeight: FontWeight.bold,
                fontSize: 18,
                color: Colors.white,
              ),
            ),
            accountEmail: Text(
              auth.userEmail ?? 'Owner',
              style: GoogleFonts.outfit(
                fontSize: 13,
                color: const Color(0xFFFFE8B6),
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          Expanded(
            child: ListView(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              children: [
                _buildDrawerItem(0, Icons.donut_large_rounded, 'Dashboard', context),
                _buildDrawerItem(1, Icons.calendar_month_rounded, 'Bookings', context),
                _buildDrawerItem(8, Icons.calendar_today_rounded, 'Calendar', context),
                _buildDrawerItem(7, Icons.room_service_rounded, 'Guest Services', context),
                _buildDrawerItem(2, Icons.apartment_rounded, 'Properties', context),
                _buildDrawerItem(6, Icons.hotel_rounded, 'Accommodations', context),
                _buildDrawerItem(3, Icons.handshake_rounded, 'B2B Partners', context),
                _buildDrawerItem(4, Icons.people_rounded, 'Guests', context),
                _buildDrawerItem(9, Icons.payments_rounded, 'Finance', context),
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Divider(color: Color(0xFFEBF0E6), thickness: 1.5),
                ),
                _buildDrawerItem(5, Icons.person_rounded, 'Profile', context),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(24.0),
            child: Text(
              'v1.0.0',
              style: GoogleFonts.outfit(
                color: const Color(0xFF5A7251),
                fontSize: 12,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDrawerItem(int index, IconData icon, String title, BuildContext context) {
    final isSelected = currentIndex == index;
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 4),
      decoration: BoxDecoration(
        color: isSelected ? const Color(0xFF2E3E2A).withOpacity(0.08) : Colors.transparent,
        borderRadius: BorderRadius.circular(16),
      ),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 2),
        leading: Icon(
          icon,
          color: isSelected ? const Color(0xFF2E3E2A) : const Color(0xFF5A7251),
        ),
        title: Text(
          title,
          style: GoogleFonts.outfit(
            fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
            color: isSelected ? const Color(0xFF2E3E2A) : const Color(0xFF191D19),
            fontSize: 15,
          ),
        ),
        selected: isSelected,
        onTap: () {
          Navigator.pop(context); // Close drawer
          onItemSelected(index);
        },
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
      ),
    );
  }
}
