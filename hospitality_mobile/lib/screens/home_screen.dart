import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:shimmer/shimmer.dart';
import '../providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';
import '../providers/riverpod_providers.dart';
import 'main_layout.dart';
import 'checkin_form_screen.dart';
import 'package:intl/intl.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  // Organic Modern Color Palette
  static const Color primaryColor = Color(0xFF2E3E2A); // Deep organic green
  static const Color secondaryColor = Color(
    0xFF191D19,
  ); // Dark organic charcoal
  static const Color backgroundColor = Color(
    0xFFF2F5F0,
  ); // Organic warm cream background
  static const Color cardColor = Colors.white;
  static const Color textPrimary = Color(0xFF191D19); // Charcoal
  static const Color textSecondary = Color(0xFF5A7251); // Soft green

  @override
  void initState() {
    super.initState();
    Future.microtask(() => ref.read(dashboardProvider).fetchDashboardData());
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authProvider);
    final dashboard = ref.watch(dashboardProvider);

    return LayoutBuilder(
      builder: (context, constraints) {
        final isTablet = constraints.maxWidth > 600;

        return Scaffold(
          backgroundColor: backgroundColor,
          body: dashboard.isLoading
              ? _buildShimmerLoading(isTablet)
              : dashboard.error != null
              ? _buildErrorState(dashboard)
              : _buildDashboardContent(context, auth, dashboard, isTablet),
        );
      },
    );
  }

  // ---------------------------------------------------------------------------
  // LOADING STATE (SHIMMER)
  // ---------------------------------------------------------------------------

  Widget _buildShimmerLoading(bool isTablet) {
    return SingleChildScrollView(
      padding: EdgeInsets.all(isTablet ? 32.0 : 20.0),
      child: Shimmer.fromColors(
        baseColor: Colors.grey.shade300,
        highlightColor: Colors.grey.shade100,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header Shimmer
            Row(
              children: [
                const CircleAvatar(radius: 24),
                const SizedBox(width: 16),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(width: 100, height: 14, color: cardColor),
                    const SizedBox(height: 8),
                    Container(width: 150, height: 20, color: cardColor),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 32),
            // Hero Card Shimmer
            Container(
              height: 200,
              decoration: BoxDecoration(
                color: cardColor,
                borderRadius: BorderRadius.circular(24),
              ),
            ),
            const SizedBox(height: 32),
            // Stats Grid Shimmer
            Row(
              children: [
                Expanded(
                  child: Container(
                    height: 120,
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(20),
                    ),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: Container(
                    height: 120,
                    decoration: BoxDecoration(
                      color: cardColor,
                      borderRadius: BorderRadius.circular(20),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  // ---------------------------------------------------------------------------
  // ERROR STATE
  // ---------------------------------------------------------------------------
  Widget _buildErrorState(DashboardProvider provider) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: Colors.red.shade50,
              shape: BoxShape.circle,
            ),
            child: Icon(
              Icons.cloud_off_rounded,
              size: 48,
              color: Colors.blue.shade400,
            ),
          ).animate().fadeIn().scale(),
          const SizedBox(height: 24),
          Text(
            'Unable to load dashboard',
            style: GoogleFonts.outfit(
              color: textPrimary,
              fontSize: 18,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Please check your internet connection',
            style: GoogleFonts.outfit(color: textSecondary),
          ),
          const SizedBox(height: 32),
          ElevatedButton.icon(
            onPressed: () => provider.fetchDashboardData(),
            icon: const Icon(Icons.refresh_rounded),
            label: const Text('Try Again'),
            style: ElevatedButton.styleFrom(
              backgroundColor: primaryColor,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ---------------------------------------------------------------------------
  // MAIN CONTENT
  // ---------------------------------------------------------------------------
  Widget _buildDashboardContent(
    BuildContext context,
    AuthProvider auth,
    DashboardProvider dashboard,
    bool isTablet,
  ) {
    final data = dashboard.data;
    if (data == null) return const SizedBox.shrink();

    final stats = data['stats'];
    final properties = data['properties'] as List;
    final nextBookings = data['nextBookings'] as List?;
    final recentBookings = data['recentBookings'] as List;

    return RefreshIndicator(
      onRefresh: () => dashboard.fetchDashboardData(),
      color: primaryColor,
      child: ListView(
        padding: EdgeInsets.all(isTablet ? 32.0 : 20.0),
        children: [
          _buildHeader(
            auth,
          ).animate().fadeIn(duration: 600.ms).slideY(begin: -0.2, end: 0),
          const SizedBox(height: 20),
          if (nextBookings != null && nextBookings.isNotEmpty)
            _buildNextArrivalsSection(nextBookings, isTablet)
                .animate()
                .fadeIn(delay: 200.ms, duration: 600.ms)
                .slideX(begin: 0.1, end: 0),
          if (nextBookings != null && nextBookings.isNotEmpty)
            const SizedBox(height: 32),
          Text(
            'Overview',
            style: GoogleFonts.outfit(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: textPrimary,
            ),
          ).animate().fadeIn(delay: 300.ms),
          const SizedBox(height: 16),
          _buildStatsGrid(stats, isTablet).animate().fadeIn(delay: 400.ms),
          const SizedBox(height: 32),
          _buildSectionHeader(
            'Your Properties',
            properties.length.toString(),
          ).animate().fadeIn(delay: 500.ms),
          const SizedBox(height: 16),
          _buildPropertiesList(
            properties,
            isTablet,
          ).animate().fadeIn(delay: 600.ms).slideX(begin: 0.1, end: 0),
          const SizedBox(height: 32),
          _buildSectionHeader(
            'Recent Bookings',
            '',
          ).animate().fadeIn(delay: 700.ms),
          const SizedBox(height: 16),
          _buildRecentBookingsList(
            recentBookings,
          ).animate().fadeIn(delay: 800.ms).slideY(begin: 0.1, end: 0),
          const SizedBox(height: 80),
        ],
      ),
    );
  }

  Widget _buildHeader(AuthProvider auth) {
    ImageProvider avatarImage;
    if (auth.profilePhotoUrl != null && auth.profilePhotoUrl!.isNotEmpty) {
      avatarImage = NetworkImage(auth.profilePhotoUrl!);
    } else {
      avatarImage = NetworkImage(
        'https://ui-avatars.com/api/?name=${Uri.encodeComponent(auth.userName ?? "User")}&background=2E3E2A&color=fff',
      );
    }

    return Row(
      children: [
        GestureDetector(
          onTap: () => MainLayout.scaffoldKey.currentState?.openDrawer(),
          child: Container(
            padding: const EdgeInsets.all(2),
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: Colors.white, width: 2),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.05),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: CircleAvatar(
              radius: 26,
              backgroundColor: const Color(0xFF2E3E2A),
              backgroundImage: avatarImage,
            ),
          ),
        ),
        const SizedBox(width: 16),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Welcome back,',
              style: GoogleFonts.outfit(
                fontSize: 14,
                color: textSecondary,
                fontWeight: FontWeight.w500,
              ),
            ),
            Text(
              auth.userName ?? 'Owner',
              style: GoogleFonts.outfit(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: textPrimary,
              ),
            ),
          ],
        ),
        const Spacer(),
        IconButton(
          onPressed: () {},
          icon: const Icon(Icons.notifications_outlined),
          style: IconButton.styleFrom(
            backgroundColor: Colors.white,
            foregroundColor: textPrimary,
            padding: const EdgeInsets.all(12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
              side: BorderSide(color: Colors.grey.shade200),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildNextBookingCard(Map<String, dynamic> booking, bool isTablet) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        color: secondaryColor,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: secondaryColor.withOpacity(0.2),
            blurRadius: 24,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Stack(
        children: [
          // Decorative circles
          Positioned(
            right: -20,
            top: -20,
            child: CircleAvatar(
              radius: 80,
              backgroundColor: Colors.white.withOpacity(0.05),
            ),
          ),
          Positioned(
            left: -40,
            bottom: -40,
            child: CircleAvatar(
              radius: 60,
              backgroundColor: Colors.white.withOpacity(0.05),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(18),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(
                          color: Colors.white.withOpacity(0.1),
                        ),
                      ),
                      child: Row(
                        children: [
                          const Icon(
                            Icons.flight_land_rounded,
                            color: Color(0xFF818CF8),
                            size: 16,
                          ),
                          const SizedBox(width: 6),
                          Text(
                            'NEXT ARRIVAL',
                            style: GoogleFonts.outfit(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                              letterSpacing: 1.0,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Text(
                  booking['guest']?['name'] ?? 'Guest Name',
                  style: GoogleFonts.outfit(
                    fontSize: isTablet ? 28 : 22,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                    height: 1.1,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  booking['accommodation']?['property']?['name'] ??
                      'Property Name',
                  style: GoogleFonts.outfit(
                    fontSize: 12,
                    color: Colors.blueGrey.shade200,
                  ),
                ),
                const SizedBox(height: 14),
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: Colors.white.withOpacity(0.05)),
                  ),
                  child: Row(
                    children: [
                      _buildInfoItem(
                        Icons.calendar_today_rounded,
                        'Check-in',
                        _formatCleanDate(booking['check_in_date']),
                        Colors.white,
                      ),
                      Container(
                        height: 40,
                        width: 1,
                        color: Colors.white10,
                        margin: const EdgeInsets.symmetric(horizontal: 16),
                      ),
                      _buildInfoItem(
                        Icons.bed_rounded,
                        'Room',
                        booking['accommodation']?['name'],
                        Colors.white,
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) =>
                              CheckInFormScreen(booking: booking),
                        ),
                      ).then((_) {
                        ref.read(dashboardProvider).fetchDashboardData();
                      });
                    },
                    icon: const Icon(
                      Icons.check_circle_outline_rounded,
                      size: 18,
                    ),
                    label: Text(
                      'Check In Guest',
                      style: GoogleFonts.outfit(
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                      ),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFFFE8B6),
                      foregroundColor: const Color(0xFF2E3E2A),
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                      elevation: 0,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoItem(
    IconData icon,
    String label,
    String? value,
    Color textColor,
  ) {
    return Expanded(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: textColor.withOpacity(0.6), size: 14),
              const SizedBox(width: 6),
              Text(
                label,
                style: GoogleFonts.outfit(
                  fontSize: 11,
                  color: textColor.withOpacity(0.6),
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          Text(
            value ?? 'N/A',
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: GoogleFonts.outfit(
              fontSize: 15,
              fontWeight: FontWeight.w600,
              color: textColor,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatsGrid(Map<String, dynamic> stats, bool isTablet) {
    return GridView.count(
      crossAxisCount: 4,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisSpacing: 10,
      mainAxisSpacing: 10,
      childAspectRatio: isTablet ? 1.1 : 0.72,
      children: [
        _buildStatCard(
          'This Week',
          (stats['upcoming_week'] ?? 0).toString(),
          Icons.done_all_rounded,
          const Color(0xFF10B981), // Emerald
        ),
        _buildStatCard(
          'This Month',
          (stats['upcoming_month'] ?? 0).toString(),
          Icons.calendar_month_rounded,
          const Color(0xFF3B82F6), // Blue
        ),
        _buildStatCard(
          'Pending',
          (stats['pending_bookings'] ?? 0).toString(),
          Icons.pending_actions_rounded,
          const Color(0xFFF59E0B),
        ),
        _buildStatCard(
          'Active',
          (stats['active_bookings'] ?? 0).toString(),
          Icons.hotel_rounded,
          const Color(0xFF8B5CF6),
        ),
      ],
    );
  }

  Widget _buildStatCard(
    String label,
    String value,
    IconData icon,
    Color color,
  ) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 12),
      decoration: BoxDecoration(
        color: cardColor,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.04),
            blurRadius: 20,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Container(
            padding: const EdgeInsets.all(7),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color, size: 16),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                value,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.outfit(
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  color: textPrimary,
                ),
              ),
              Text(
                label,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: GoogleFonts.outfit(
                  fontSize: 10,
                  color: textSecondary,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(String title, String trailing) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          title,
          style: GoogleFonts.outfit(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: textPrimary,
          ),
        ),
        if (trailing.isNotEmpty)
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.white,
              border: Border.all(color: Colors.grey.shade200),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              '$trailing Total',
              style: GoogleFonts.outfit(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: textSecondary,
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildPropertiesList(List properties, bool isTablet) {
    if (properties.isEmpty) {
      return _buildEmptyState('No properties found');
    }

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: properties.length,
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: isTablet ? 4 : 2,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
        childAspectRatio: isTablet ? 1.15 : 1.05,
      ),
      itemBuilder: (context, index) =>
          _buildPropertyCard(properties[index], isTablet),
    );
  }

  Widget _buildPropertyCard(Map<String, dynamic> property, bool isTablet) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: cardColor,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Text(
                  property['name'] ?? 'Property Name',
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.outfit(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: textPrimary,
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Container(
                padding: const EdgeInsets.all(7),
                decoration: BoxDecoration(
                  color: backgroundColor,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(
                  Icons.business_rounded,
                  color: primaryColor,
                  size: 18,
                ),
              ),
            ],
          ),
          const Spacer(),
          Row(
            children: [
              Icon(
                Icons.location_on_rounded,
                size: 14,
                color: textSecondary.withOpacity(0.7),
              ),
              const SizedBox(width: 4),
              Expanded(
                child: Text(
                  property['location']?['city']?['name'] ?? 'Location',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.outfit(
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                    color: textSecondary,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Wrap(
            spacing: 6,
            runSpacing: 6,
            children: [
              _buildPropertyMiniChip(
                Icons.home_work_rounded,
                '${property['property_accommodations_count'] ?? 0} units',
              ),
              _buildPropertyMiniChip(
                Icons.verified_rounded,
                (property['status'] ?? 'active').toString(),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildPropertyMiniChip(IconData icon, String label) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 5),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(10),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 12, color: primaryColor),
          const SizedBox(width: 4),
          Text(
            label,
            style: GoogleFonts.outfit(
              fontSize: 9.5,
              fontWeight: FontWeight.w700,
              color: primaryColor,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentBookingsList(List bookings) {
    if (bookings.isEmpty) {
      return _buildEmptyState('No recent bookings');
    }

    return ListView.separated(
      physics: const NeverScrollableScrollPhysics(),
      shrinkWrap: true,
      itemCount: bookings.length,
      separatorBuilder: (_, __) => const SizedBox(height: 12),
      itemBuilder: (context, index) => _buildBookingTile(bookings[index]),
    );
  }

  Widget _buildBookingTile(Map<String, dynamic> booking) {
    final status = booking['status'] ?? 'pending';
    // Simplified status handling
    Color statusColor;
    IconData statusIcon;

    switch (status) {
      case 'confirmed':
        statusColor = const Color(0xFF10B981);
        statusIcon = Icons.check_circle_rounded;
        break;
      case 'cancelled':
        statusColor = const Color(0xFFEF4444);
        statusIcon = Icons.cancel_rounded;
        break;
      default:
        statusColor = const Color(0xFFF59E0B);
        statusIcon = Icons.access_time_filled_rounded;
    }

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: cardColor,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: statusColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(statusIcon, color: statusColor, size: 20),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  booking['guest']?['name'] ?? 'Guest',
                  style: GoogleFonts.outfit(
                    fontWeight: FontWeight.w600,
                    fontSize: 15,
                    color: textPrimary,
                  ),
                ),
                Text(
                  booking['accommodation']?['property']?['name'] ?? 'Property',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: GoogleFonts.outfit(fontSize: 12, color: textSecondary),
                ),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                '₹${booking['total_amount']}',
                style: GoogleFonts.outfit(
                  fontWeight: FontWeight.bold,
                  fontSize: 15,
                  color: textPrimary,
                ),
              ),
              Text(
                _formatCleanDate(booking['check_in_date']),
                style: GoogleFonts.outfit(fontSize: 11, color: textSecondary),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState(String message) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          children: [
            Icon(Icons.inbox_rounded, size: 48, color: Colors.grey[200]),
            const SizedBox(height: 8),
            Text(message, style: GoogleFonts.outfit(color: textSecondary)),
          ],
        ),
      ),
    );
  }

  Widget _buildNextArrivalsSection(List bookings, bool isTablet) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          height: isTablet ? 208 : 186,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            physics: const BouncingScrollPhysics(),
            itemCount: bookings.length,
            separatorBuilder: (_, __) => const SizedBox(width: 16),
            itemBuilder: (context, index) {
              final booking = Map<String, dynamic>.from(bookings[index] as Map);
              return Container(
                width:
                    MediaQuery.of(context).size.width *
                    (isTablet ? 0.52 : 0.78),
                constraints: const BoxConstraints(maxWidth: 340),
                child: _buildNextBookingCard(booking, isTablet),
              );
            },
          ),
        ),
      ],
    );
  }

  String _formatCleanDate(dynamic dateValue) {
    if (dateValue == null) return 'N/A';
    try {
      final cleanStr = dateValue.toString().split('T')[0];
      final parsed = DateFormat('yyyy-MM-dd').parse(cleanStr);
      return DateFormat('d MMM').format(parsed);
    } catch (_) {
      try {
        final parsed = DateTime.parse(dateValue.toString());
        return DateFormat('d MMM').format(parsed);
      } catch (_) {
        return dateValue.toString();
      }
    }
  }
}
