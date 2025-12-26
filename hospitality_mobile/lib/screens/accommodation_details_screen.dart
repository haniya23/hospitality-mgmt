import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'create_booking_screen.dart';

class AccommodationDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> accommodation;

  const AccommodationDetailsScreen({super.key, required this.accommodation});

  @override
  Widget build(BuildContext context) {
    final photos = accommodation['photos'] as List? ?? [];
    final amenities = accommodation['amenities'] as List? ?? [];
    final predefinedName = accommodation['predefined_type']?['name'] ?? 'Room';
    final name = accommodation['custom_name'] ?? predefinedName;
    
    return Scaffold(
      appBar: AppBar(
        title: Text(name, style: GoogleFonts.poppins(color: Colors.black87)),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black87),
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Gallery
            SizedBox(
              height: 250,
              child: photos.isNotEmpty
                  ? PageView.builder(
                      itemCount: photos.length,
                      itemBuilder: (context, index) {
                        return Image.network(
                          photos[index]['url'] ?? '',
                          fit: BoxFit.cover,
                          errorBuilder: (c,e,s) => Container(color: Colors.grey[300]),
                        );
                      },
                    )
                  : Container(
                      color: Colors.grey[200],
                      child: const Center(child: Icon(Icons.hotel, size: 64, color: Colors.grey)),
                    ),
            ),
            
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Text(
                          name,
                          style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold),
                        ),
                      ),
                      Text(
                        '₹${accommodation['base_price'] ?? '0'}',
                        style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.blue[800]),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 16,
                    children: [
                      _buildInfoBadge(Icons.square_foot, '${accommodation['size'] ?? '-'} sqft'),
                      _buildInfoBadge(Icons.people, 'Max ${accommodation['max_occupancy'] ?? 2} Guests'),
                    ],
                  ),
                  const SizedBox(height: 24),
                  
                  // Description
                  if (accommodation['description'] != null) ...[
                    Text('Description', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    Text(
                      accommodation['description'],
                      style: GoogleFonts.poppins(color: Colors.grey[800], height: 1.5),
                    ),
                    const SizedBox(height: 24),
                  ],

                  // Amenities
                   if (amenities.isNotEmpty) ...[
                     Text('Amenities', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                     const SizedBox(height: 12),
                     Wrap(
                       spacing: 8,
                       runSpacing: 8,
                       children: amenities.map<Widget>((amenity) {
                         return Chip(
                           label: Text(amenity['name']),
                           avatar: const Icon(Icons.star, size: 16, color: Colors.orange),
                           backgroundColor: Colors.orange.shade50,
                           labelStyle: GoogleFonts.poppins(color: Colors.orange.shade900),
                         );
                       }).toList(),
                     ),
                   ],
                ],
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(16.0),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: const Offset(0, -5))],
        ),
        child: ElevatedButton(
          onPressed: () {
             // Navigate to Booking wizard with pre-filled data
             final propertyId = accommodation['property_id']?.toString();
             final accId = accommodation['id']?.toString();
             
             if (propertyId != null && accId != null) {
               Navigator.push(
                 context,
                 MaterialPageRoute(
                   builder: (context) => CreateBookingScreen(
                     initialPropertyId: propertyId,
                     initialAccommodationId: accId,
                   ),
                 ),
               );
             } else {
               ScaffoldMessenger.of(context).showSnackBar(
                 const SnackBar(content: Text('Error: Property information missing used for booking.')),
               );
             }
          },
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.blue[700],
            padding: const EdgeInsets.symmetric(vertical: 16),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          ),
          child: Text('Book Now', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold)),
        ),
      ),
    );
  }

  Widget _buildInfoBadge(IconData icon, String text) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 4),
        Text(text, style: GoogleFonts.poppins(color: Colors.grey[600])),
      ],
    );
  }
}
