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
    return Consumer<PropertyProvider>(
      builder: (context, provider, child) {
         // Find fresh property
         final propertyId = accommodation['property_id'];
         final freshProperty = provider.properties.firstWhere(
            (p) => p['id'] == propertyId,
            orElse: () => null,
         );

         // Find fresh accommodation
         dynamic freshAccommodation = accommodation;
         if (freshProperty != null) {
            final accs = freshProperty['property_accommodations'] as List?;
            if (accs != null) {
               freshAccommodation = accs.firstWhere(
                 (a) => a['id'] == accommodation['id'],
                 orElse: () => accommodation,
               );
            }
         }

        final photos = freshAccommodation['photos'] as List? ?? [];
        final amenities = freshAccommodation['amenities'] as List? ?? [];
        final predefinedName = freshAccommodation['predefined_type']?['name'] ?? 'Room';
        final name = freshAccommodation['custom_name'] ?? predefinedName;

        return Scaffold(
          body: CustomScrollView(
            slivers: [
              SliverAppBar(
                expandedHeight: 250.0,
                floating: false,
                pinned: true,
                actions: [
                  IconButton(
                    icon: const Icon(Icons.add_a_photo),
                    onPressed: () => _pickAndUploadPhoto(context, freshAccommodation),
                  ),
                ],
                flexibleSpace: FlexibleSpaceBar(
                  title: Text(
                    name,
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
                          ? PageView.builder(
                              itemCount: photos.length,
                              itemBuilder: (context, index) {
                                return Stack(
                                  fit: StackFit.expand,
                                  children: [
                                    Image.network(
                                      photos[index]['url'] ?? 'https://via.placeholder.com/400x250',
                                      fit: BoxFit.cover,
                                      errorBuilder: (ctx, err, _) => Container(color: Colors.grey[200]),
                                    ),
                                    Positioned(
                                      top: 10,
                                      right: 10,
                                      child: IconButton(
                                        icon: const Icon(Icons.delete, color: Colors.red),
                                        onPressed: () => _confirmDeletePhoto(context, freshAccommodation, photos[index]['id']),
                                      ),
                                    ),
                                  ],
                                );
                              },
                            )
                          : Container(color: Colors.blueGrey.shade100, child: const Icon(Icons.hotel, size: 50, color: Colors.white54)),
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
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            name,
                            style: GoogleFonts.poppins(fontSize: 20, fontWeight: FontWeight.bold),
                          ),
                          Text(
                            '₹${double.parse(freshAccommodation['base_price'].toString()).toStringAsFixed(2)}',
                            style: GoogleFonts.poppins(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.blue[700]),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Text(
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

  Future<void> _pickAndUploadPhoto(BuildContext context, [Map<String, dynamic>? accommodationData]) async {
    final targetAccommodation = accommodationData ?? accommodation;
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
    
    final propertyId = targetAccommodation['property_id'];
    if (propertyId == null) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Error: Property ID missing')));
        return;
    }
    
    final success = await Provider.of<PropertyProvider>(context, listen: false)
        .uploadAccommodationPhoto(propertyId, targetAccommodation['id'], compressedFile.path);
    
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Photo uploaded successfully.')));
    } else {
      final provider = Provider.of<PropertyProvider>(context, listen: false);
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(provider.error ?? 'Failed to upload photo')));
    }
  }

  void _confirmDeletePhoto(BuildContext context, Map<String, dynamic> accommodationData, int photoId) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Delete Photo?'),
        content: const Text('Are you sure you want to delete this photo?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () async {
              Navigator.pop(ctx);
              
              final propertyId = accommodationData['property_id'];
              if (propertyId == null) return;

              final success = await Provider.of<PropertyProvider>(context, listen: false)
                  .deleteAccommodationPhoto(propertyId, accommodationData['id'], photoId);
               if (success) {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Photo deleted successfully. Go back to see changes.')));
              } else {
                 final provider = Provider.of<PropertyProvider>(context, listen: false);
                 ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(provider.error ?? 'Failed to delete photo')));
              }
            },
            child: const Text('Delete', style: TextStyle(color: Colors.red)),
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
