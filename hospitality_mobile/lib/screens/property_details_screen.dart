import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'accommodation_details_screen.dart'; // Forward reference

class PropertyDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> property;

  const PropertyDetailsScreen({super.key, required this.property});

  @override
  Widget build(BuildContext context) {
    final photos = property['photos'] as List? ?? [];
    final amenities = property['amenities'] as List? ?? [];
    final accommodations = property['property_accommodations'] as List? ?? [];

    return Scaffold(
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 250.0,
            floating: false,
            pinned: true,
            flexibleSpace: FlexibleSpaceBar(
              title: Text(
                property['name'],
                style: GoogleFonts.poppins(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                  color: Colors.white,
                  shadows: [Shadow(color: Colors.black45, blurRadius: 10)],
                ),
              ),
              background: Stack(
                fit: StackFit.expand,
                children: [
                  photos.isNotEmpty
                      ? Image.network(
                          photos[0]['url'] ?? 'https://via.placeholder.com/400x250',
                          fit: BoxFit.cover,
                          errorBuilder: (ctx, err, _) => Container(color: Colors.grey[200]),
                        )
                      : Container(color: Colors.blueGrey.shade100),
                  const DecoratedBox(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        colors: [Colors.transparent, Colors.black54],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                   // Address
                   if (property['address_line_1'] != null)
                    Row(
                      children: [
                        Icon(Icons.location_on, size: 16, color: Colors.blue[700]),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            [
                              property['address_line_1'],
                              property['location']?['city']?['name'],
                              property['location']?['city']?['district']?['state']?['name']
                            ].where((s) => s != null).join(', '),
                            style: GoogleFonts.poppins(color: Colors.grey[600], fontSize: 13),
                          ),
                        ),
                      ],
                    ),
                   const SizedBox(height: 16),
                   
                   // Description
                   Text(
                     'About Property',
                     style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 18),
                   ),
                   const SizedBox(height: 8),
                   Text(
                     property['description'] ?? 'No description available.',
                     style: GoogleFonts.poppins(color: Colors.grey[800], height: 1.5),
                   ),
                   const SizedBox(height: 24),

                   // Amenities
                   if (amenities.isNotEmpty) ...[
                     Text(
                       'Amenities',
                       style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 18),
                     ),
                     const SizedBox(height: 12),
                     Wrap(
                       spacing: 8,
                       runSpacing: 8,
                       children: amenities.map<Widget>((amenity) {
                         return Chip(
                           label: Text(amenity['name']),
                           backgroundColor: Colors.blue.shade50,
                           labelStyle: GoogleFonts.poppins(color: Colors.blue.shade900, fontSize: 12),
                           avatar: const Icon(Icons.check_circle, size: 16, color: Colors.blue),
                         );
                       }).toList(),
                     ),
                     const SizedBox(height: 24),
                   ],

                   // Accommodations List
                   Text(
                     'Accommodations (${accommodations.length})',
                     style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 18),
                   ),
                   const SizedBox(height: 12),
                   ...accommodations.map((acc) => _buildAccommodationCard(context, acc)),
                   
                   const SizedBox(height: 40),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAccommodationCard(BuildContext context, Map<String, dynamic> acc) {
    // Ensure property_id is present for booking
    acc['property_id'] ??= property['id']; 
    
    final predefinedName = acc['predefined_type']?['name'] ?? 'Room';
    final name = acc['custom_name'] ?? predefinedName;
    final photos = acc['photos'] as List? ?? [];
    final price = acc['base_price'];

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      margin: const EdgeInsets.only(bottom: 16),
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => AccommodationDetailsScreen(accommodation: acc),
            ),
          );
        },
        borderRadius: BorderRadius.circular(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            ClipRRect(
              borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
              child: SizedBox(
                height: 150,
                width: double.infinity,
                child: photos.isNotEmpty
                    ? Image.network(
                        photos[0]['url'] ?? '',
                        fit: BoxFit.cover,
                        errorBuilder: (c,e,s) => Container(color: Colors.grey[300], child: const Icon(Icons.image_not_supported)),
                      )
                    : Container(color: Colors.grey[300], child: const Icon(Icons.hotel, size: 40, color: Colors.white)),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(12.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          name,
                          style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      if (price != null)
                        Text(
                          '₹$price',
                          style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16, color: Colors.blue[800]),
                        ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  if (acc['size'] != null)
                    Text(
                      'Size: ${acc['size']} sqft • Max Occupancy: ${acc['max_occupancy'] ?? 2}',
                      style: GoogleFonts.poppins(color: Colors.grey[600], fontSize: 12),
                    ),
                  const SizedBox(height: 8),
                  Text(
                    'View Details',
                    style: GoogleFonts.poppins(color: Colors.blue, fontWeight: FontWeight.w600, fontSize: 12),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
