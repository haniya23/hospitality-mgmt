import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:image_cropper/image_cropper.dart';
import 'package:provider/provider.dart';
import '../providers/property_provider.dart';
import '../utils/image_helper.dart';
import 'create_booking_screen.dart';

class AccommodationDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> accommodation;

  const AccommodationDetailsScreen({super.key, required this.accommodation});

  @override
  Widget build(BuildContext context) {
    // If photos update, we might need to refresh? But provider updates should handle it if we listen...
    // Actually this screen is stateless and receives map. It won't auto-update unless parent rebuilds or we fetch.
    // Ideally we should use Consumer or fetch fresh data. For now, rely on what's passed, 
    // but upload actions will be simpler.
    
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
        actions: [
          IconButton(
            icon: const Icon(Icons.add_a_photo, color: Colors.blue),
            onPressed: () => _pickAndUploadPhoto(context),
          ),
        ],
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
                        return Stack(
                          fit: StackFit.expand,
                          children: [
                            Image.network(
                              photos[index]['url'] ?? '',
                              fit: BoxFit.cover,
                              errorBuilder: (c,e,s) => Container(color: Colors.grey[300]),
                            ),
                            Positioned(
                              top: 10,
                              right: 10,
                              child: IconButton(
                                icon: const Icon(Icons.delete, color: Colors.red),
                                onPressed: () => _confirmDeletePhoto(context, photos[index]['id']),
                              ),
                            ),
                          ],
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

  Future<void> _pickAndUploadPhoto(BuildContext context) async {
    final helper = ImageHelper.instance;
    
    // 1. Pick
    final XFile? file = await helper.pickImage();
    if (file == null) return;

    // 2. Crop
    final CroppedFile? croppedFile = await helper.crop(file: file);
    if (croppedFile == null) return;

    // 3. Compress
    final XFile? compressedFile = await helper.compress(file: File(croppedFile.path));
    if (compressedFile == null) return;
    
    final propertyId = accommodation['property_id'];
    if (propertyId == null) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Error: Property ID missing')));
        return;
    }
    
    final success = await Provider.of<PropertyProvider>(context, listen: false)
        .uploadAccommodationPhoto(propertyId, accommodation['id'], compressedFile.path);
    
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Photo uploaded successfully. Go back to see changes.')));
    } else {
      final provider = Provider.of<PropertyProvider>(context, listen: false);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(provider.error ?? 'Failed to upload photo')));
    }
  }

  void _confirmDeletePhoto(BuildContext context, int photoId) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Delete Photo?'),
        content: const Text('Are you sure you want to delete this photo?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context);
              
              final propertyId = accommodation['property_id'];
              if (propertyId == null) return;

              final success = await Provider.of<PropertyProvider>(context, listen: false)
                  .deleteAccommodationPhoto(propertyId, accommodation['id'], photoId);
               if (success) {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Photo deleted successfully. Go back to see changes.')));
              } else {
                 final provider = Provider.of<PropertyProvider>(context, listen: false);
                 ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(provider.error ?? 'Failed to delete photo')));
              }
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Delete', style: TextStyle(color: Colors.white)),
          ),
        ],
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
