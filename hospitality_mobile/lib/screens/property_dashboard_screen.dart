import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../providers/property_provider.dart';

class PropertyDashboardScreen extends StatefulWidget {
  final int propertyId;
  final String propertyName;

  const PropertyDashboardScreen({
    Key? key,
    required this.propertyId,
    required this.propertyName,
  }) : super(key: key);

  @override
  _PropertyDashboardScreenState createState() => _PropertyDashboardScreenState();
}

class _PropertyDashboardScreenState extends State<PropertyDashboardScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<PropertyProvider>(context, listen: false)
          .fetchPropertyDashboard(widget.propertyId);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.blueGrey.shade50,
      body: Consumer<PropertyProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.error != null && provider.dashboardData == null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text('Error: ${provider.error}'),
                  ElevatedButton(
                    onPressed: () => provider.fetchPropertyDashboard(widget.propertyId),
                    child: const Text('Retry'),
                  ),
                ],
              ),
            );
          }

          final dashboardData = provider.dashboardData;
          if (dashboardData == null) return const SizedBox.shrink();

          final property = dashboardData['property'];
          final stats = dashboardData['stats'];

          return CustomScrollView(
            slivers: [
              _buildSliverAppBar(property),
              SliverPadding(
                padding: const EdgeInsets.all(16.0),
                sliver: SliverList(
                  delegate: SliverChildListDelegate([
                    _buildSnapshotGrid(stats),
                    const SizedBox(height: 24),
                    _buildSectionTitle('Guests & Bookings'),
                    const SizedBox(height: 12),
                    _buildGuestsSection(stats),
                    const SizedBox(height: 24),
                    _buildSectionTitle('Operations'),
                    const SizedBox(height: 12),
                    _buildOperationsSection(stats),
                    const SizedBox(height: 24),
                    _buildSectionTitle('Performance'),
                    const SizedBox(height: 12),
                    _buildPerformanceSection(stats),
                    const SizedBox(height: 40),
                  ]),
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildSliverAppBar(Map<String, dynamic> property) {
    return SliverAppBar(
      expandedHeight: 200.0,
      floating: false,
      pinned: true,
      backgroundColor: Colors.blue.shade800,
      flexibleSpace: FlexibleSpaceBar(
        title: Text(
          property['name'],
          style: GoogleFonts.poppins(
            fontWeight: FontWeight.bold,
            fontSize: 16,
          ),
        ),
        background: Stack(
          fit: StackFit.expand,
          children: [
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Colors.blue.shade900, Colors.blue.shade700],
                ),
              ),
            ),
             Positioned(
              bottom: 60,
              left: 16,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                   Row(
                     children: [
                       Icon(Icons.location_on, color: Colors.white70, size: 16),
                       SizedBox(width: 4),
                       Text(
                         '${property['location']?['city']?['name'] ?? ''}, ${property['location']?['city']?['district']?['state']?['name'] ?? ''}',
                         style: GoogleFonts.poppins(color: Colors.white70, fontSize: 12),
                       ),
                     ],
                   ),
                   SizedBox(height: 8),
                   Container(
                     padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                     decoration: BoxDecoration(
                       color: Colors.white24,
                       borderRadius: BorderRadius.circular(12),
                     ),
                     child: Text(
                       property['status']?.toUpperCase() ?? 'ACTIVE',
                       style: GoogleFonts.poppins(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                     ),
                   ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSnapshotGrid(Map<String, dynamic> stats) {
    final occupancyRate = stats['monthlyStats']['occupancy_rate'] ?? 0;
    
    return GridView.count(
      crossAxisCount: 2,
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      children: [
        _buildStatCard(
          'Occupancy',
          '${stats['occupiedAccommodations']}/${stats['totalAccommodations']}',
          '$occupancyRate% this month',
          Icons.hotel,
          Colors.green,
        ),
        _buildStatCard(
          'Guests Onsite',
          '${(stats['currentGuests'] as List).length}',
          'Checked in now',
          Icons.people,
          Colors.blue,
        ),
        _buildStatCard(
          'Today\'s Revenue',
          '₹${stats['todaysRevenue']}',
          'Provisional',
          Icons.attach_money,
          Colors.orange,
        ),
        _buildStatCard(
          'Staff On Duty',
          '${(stats['staffOnDuty'] as List).length}',
          '${(stats['pendingTasks'] as List).length} Pending Tasks',
          Icons.badge,
          Colors.purple,
        ),
      ],
    );
  }

  Widget _buildStatCard(String title, String value, String subtitle, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Icon(icon, color: color, size: 24),
              // Could add trend icon here
            ],
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                value,
                style: GoogleFonts.poppins(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: Colors.black87,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                title,
                style: GoogleFonts.poppins(
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  color: Colors.black54,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                subtitle,
                style: GoogleFonts.poppins(
                  fontSize: 10,
                  color: Colors.black38,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: GoogleFonts.poppins(
        fontSize: 18,
        fontWeight: FontWeight.bold,
        color: Colors.blueGrey.shade800,
      ),
    );
  }

  Widget _buildGuestsSection(Map<String, dynamic> stats) {
    final nextCheckins = stats['nextCheckins'] as List;
    final nextCheckouts = stats['nextCheckouts'] as List;
    final currentGuests = stats['currentGuests'] as List;

    return Column(
      children: [
        _buildExpandableList('Next Check-ins (${nextCheckins.length})', nextCheckins, Icons.login, Colors.green),
        const SizedBox(height: 12),
        _buildExpandableList('Next Check-outs (${nextCheckouts.length})', nextCheckouts, Icons.logout, Colors.orange),
        const SizedBox(height: 12),
        _buildExpandableList('Current Guests (${currentGuests.length})', currentGuests, Icons.person, Colors.blue),
      ],
    );
  }

  Widget _buildExpandableList(String title, List items, IconData icon, Color color) {
     if (items.isEmpty) return const SizedBox.shrink();

     return Container(
       decoration: BoxDecoration(
         color: Colors.white,
         borderRadius: BorderRadius.circular(12),
       ),
       child: ExpansionTile(
         leading: CircleAvatar(
           backgroundColor: color.withOpacity(0.1),
           child: Icon(icon, color: color, size: 20),
         ),
         title: Text(
           title, 
           style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 14),
         ),
         children: items.map<Widget>((item) {
           final guestName = item['guest']['name'] ?? 'Guest';
           final roomName = item['property_accommodation']['name'] ?? 'Room';
           final date = DateTime.parse(title.contains('Check-in') ? item['check_in_date'] : item['check_out_date']);
           
           return ListTile(
             title: Text(guestName, style: GoogleFonts.poppins(fontWeight: FontWeight.w500)),
             subtitle: Text(roomName, style: GoogleFonts.poppins(fontSize: 12)),
             trailing: Text(
               DateFormat('MMM d, h:mm a').format(date),
               style: GoogleFonts.poppins(fontSize: 12, fontWeight: FontWeight.bold),
             ),
           );
         }).toList(),
       ),
     );
  }

  Widget _buildOperationsSection(Map<String, dynamic> stats) {
    final pendingTasks = stats['pendingTasks'] as List;
    
    if (pendingTasks.isEmpty) {
        return Container(
            padding: const EdgeInsets.all(16),
            width: double.infinity,
            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
            child: Text('No pending tasks', style: GoogleFonts.poppins(color: Colors.grey)),
        );
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: pendingTasks.length > 5 ? 5 : pendingTasks.length,
      itemBuilder: (context, index) {
        final task = pendingTasks[index];
        return Card(
          elevation: 0,
          color: Colors.white,
          margin: const EdgeInsets.only(bottom: 8),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          child: ListTile(
            leading: CircleAvatar(
              backgroundColor: Colors.purple.shade50,
              child: Icon(Icons.task, color: Colors.purple, size: 18),
            ),
            title: Text(task['task_name'], style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 13)),
            subtitle: Text(task['assigned_staff']?['user']?['name'] ?? 'Unassigned', style: GoogleFonts.poppins(fontSize: 11)),
            trailing: Chip(
               label: Text(task['priority'], style: GoogleFonts.poppins(fontSize: 10, color: Colors.white)),
               backgroundColor: _getPriorityColor(task['priority']),
               padding: EdgeInsets.zero,
               visualDensity: VisualDensity.compact,
            ),
          ),
        );
      },
    );
  }
  
  Color _getPriorityColor(String priority) {
      switch(priority.toLowerCase()) {
          case 'high': return Colors.red;
          case 'medium': return Colors.orange;
          default: return Colors.green;
      }
  }

  Widget _buildPerformanceSection(Map<String, dynamic> stats) {
     final monthly = stats['monthlyStats'];
     
     return Container(
       padding: const EdgeInsets.all(16),
       decoration: BoxDecoration(
         color: Colors.white,
         borderRadius: BorderRadius.circular(16),
       ),
       child: Column(
         children: [
            _buildPerformanceRow('Monthly Revenue', '₹${monthly['revenue']}', Icons.bar_chart, Colors.green),
            Divider(height: 24),
            _buildPerformanceRow('Total Bookings', '${monthly['total_bookings']}', Icons.calendar_today, Colors.blue),
            Divider(height: 24),
            _buildPerformanceRow('Avg Stay Duration', '${monthly['average_stay']} days', Icons.timer, Colors.orange),
         ],
       ),
     );
  }

  Widget _buildPerformanceRow(String label, String value, IconData icon, Color color) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: color, size: 20),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: GoogleFonts.poppins(fontSize: 13, color: Colors.black54)),
              Text(value, style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87)),
            ],
          ),
        ),
      ],
    );
  }
}
