import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/property_provider.dart';
import 'accommodation_details_screen.dart';
import 'main_layout.dart';

class AccommodationsTab extends StatefulWidget {
  const AccommodationsTab({super.key});

  @override
  State<AccommodationsTab> createState() => _AccommodationsTabState();
}

class _AccommodationsTabState extends State<AccommodationsTab> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() =>
        Provider.of<PropertyProvider>(context, listen: false).fetchProperties());
  }

  @override
  Widget build(BuildContext context) {
    final propertyProvider = Provider.of<PropertyProvider>(context);
    final properties = propertyProvider.properties;
    
    // Flatten accommodations
    final allAccommodations = [];
    for (var prop in properties) {
      final accs = prop['property_accommodations'] as List? ?? [];
      for (var acc in accs) {
        // Enriched with property data if needed
        acc['property_name'] = prop['name'];
        acc['property_id'] = prop['id'];
        allAccommodations.add(acc);
      }
    }

    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.menu_rounded, color: Color(0xFF1E293B)),
          onPressed: () => MainLayout.scaffoldKey.currentState?.openDrawer(),
        ),
        title: Text(
          'Accommodations',
          style: GoogleFonts.poppins(
            fontWeight: FontWeight.bold,
            color: const Color(0xFF1E293B),
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
      ),
      body: propertyProvider.isLoading
          ? const Center(child: CircularProgressIndicator())
          : allAccommodations.isEmpty
              ? _buildEmptyState()
              : ListView.separated(
                  padding: const EdgeInsets.all(16),
                  itemCount: allAccommodations.length,
                  separatorBuilder: (_, __) => const SizedBox(height: 16),
                  itemBuilder: (context, index) =>
                      _buildAccommodationCard(context, allAccommodations[index]),
                ),
    );
  }

  Widget _buildAccommodationCard(BuildContext context, Map<String, dynamic> acc) {
    final predefinedName = acc['predefined_type']?['name'] ?? 'Room';
    final name = acc['custom_name'] ?? predefinedName;
    final photos = acc['photos'] as List? ?? [];
    final propertyName = acc['property_name'];
    final price = acc['base_price'];

    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => AccommodationDetailsScreen(accommodation: acc),
          ),
        );
      },
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          children: [
            // Image
            Container(
              width: 120,
              height: 120,
              decoration: BoxDecoration(
                color: Colors.grey.shade200,
                borderRadius: const BorderRadius.horizontal(left: Radius.circular(16)),
              ),
              child: ClipRRect(
                borderRadius: const BorderRadius.horizontal(left: Radius.circular(16)),
                child: photos.isNotEmpty
                    ? Image.network(photos[0]['url'] ?? '', fit: BoxFit.cover, errorBuilder: (c,e,s) => const Icon(Icons.hotel, color: Colors.grey))
                    : const Icon(Icons.hotel, color: Colors.grey, size: 40),
              ),
            ),
            
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    if (propertyName != null)
                      Text(
                        propertyName,
                        style: GoogleFonts.poppins(fontSize: 10, color: Colors.grey[600], fontWeight: FontWeight.w500),
                      ),
                    Text(
                      name,
                      style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    if (price != null)
                      Text(
                        '₹$price',
                        style: GoogleFonts.poppins(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.blue[800]),
                      ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                         Icon(Icons.people, size: 14, color: Colors.grey[500]),
                         const SizedBox(width: 4),
                         Text('${acc['max_occupancy'] ?? 2}', style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey[600])),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const Padding(
              padding: EdgeInsets.only(right: 16.0),
              child: Icon(Icons.arrow_forward_ios, size: 16, color: Colors.grey),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.bed_outlined, size: 64, color: Colors.grey[300]),
          const SizedBox(height: 16),
          Text(
            'No accommodations found',
            style: GoogleFonts.poppins(color: Colors.grey[500]),
          ),
        ],
      ),
    );
  }
}
