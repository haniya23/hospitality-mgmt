```dart
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:image_picker/image_picker.dart';
import 'package:image_cropper/image_cropper.dart';
import 'package:provider/provider.dart';
import '../providers/property_provider.dart';
import '../utils/image_helper.dart';
import 'accommodation_details_screen.dart'; // Forward reference
import 'property_dashboard_screen.dart';

class PropertyDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> property;

  const PropertyDetailsScreen({super.key, required this.property});

  @override
  Widget build(BuildContext context) {
    return Consumer<PropertyProvider>(
      builder: (context, provider, child) {
        // Try to find the latest version of this property
        final freshProperty = provider.properties.firstWhere(
          (p) => p['id'] == property['id'],
          orElse: () => property,
        );

        final photos = freshProperty['photos'] as List? ?? [];
        final amenities = freshProperty['amenities'] as List? ?? [];
        final accommodations = freshProperty['property_accommodations'] as List? ?? [];

        return Scaffold(
          body: CustomScrollView(
            slivers: [
              SliverAppBar(
                expandedHeight: 250.0,
                floating: false,
                pinned: true,
                actions: [
                  IconButton(
                    icon: const Icon(Icons.analytics_outlined),
                    tooltip: 'Dashboard',
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => PropertyDashboardScreen(
                            propertyId: freshProperty['id'],
                            propertyName: freshProperty['name'],
                          ),
                        ),
                      );
                    },
                  ),
                  IconButton(
                    icon: const Icon(Icons.add_a_photo),
                    onPressed: () => _pickAndUploadPhoto(context, freshProperty['id']),
                  ),
                ],
                flexibleSpace: FlexibleSpaceBar(
                  title: Text(
                    freshProperty['name'],
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
                                        onPressed: () => _confirmDeletePhoto(context, photos[index]['id']),
                                      ),
                                    ),
                                  ],
                                );
                              },
                            )
                          : Container(color: Colors.blueGrey.shade100, child: const Icon(Icons.image, size: 50, color: Colors.white54)),
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
                       if (freshProperty['address_line_1'] != null)
                        Row(
                          children: [
                            Icon(Icons.location_on, size: 16, color: Colors.blue[700]),
                            const SizedBox(width: 4),
                            Expanded(
                              child: Text(
                                [
                                  freshProperty['address_line_1'],
                                  freshProperty['location']?['city']?['name'],
                                  freshProperty['location']?['city']?['district']?['state']?['name']
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
                         freshProperty['description'] ?? 'No description available.',
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
      },
    );
  }

  Future<void> _pickAndUploadPhoto(BuildContext context, [int? propertyId]) async {
    final targetPropertyId = propertyId ?? property['id'];
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

    // 4. Upload
    final success = await Provider.of<PropertyProvider>(context, listen: false)
        .uploadPropertyPhoto(targetPropertyId, compressedFile.path);
    
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Photo uploaded successfully')));
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
              final success = await Provider.of<PropertyProvider>(context, listen: false)
                  .deletePropertyPhoto(property['id'], photoId);
                if (success) {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Photo deleted successfully')));
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
