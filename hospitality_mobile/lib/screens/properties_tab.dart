import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import '../providers/property_provider.dart';
import 'main_layout.dart';
import 'property_details_screen.dart';

class PropertiesTab extends StatefulWidget {
  const PropertiesTab({super.key});

  @override
  State<PropertiesTab> createState() => _PropertiesTabState();
}

class _PropertiesTabState extends State<PropertiesTab> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() =>
        Provider.of<PropertyProvider>(context, listen: false).fetchProperties());
  }

  @override
  Widget build(BuildContext context) {
    final propertyProvider = Provider.of<PropertyProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.menu, color: Colors.black87),
          onPressed: () => MainLayout.scaffoldKey.currentState?.openDrawer(),
        ),
        title: Text(
          'Properties',
          style: GoogleFonts.outfit(
            fontWeight: FontWeight.bold,
            color: const Color(0xFF1E293B),
          ),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.black87),
            onPressed: () => propertyProvider.fetchProperties(),
          ),
          IconButton(
            icon: const Icon(Icons.add_circle_outline, color: Colors.blue, size: 28),
            onPressed: () {
              // TODO: Navigate to Add Property
              ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Add Property coming soon')));
            },
          ),
        ],
      ),
      body: propertyProvider.isLoading
          ? const Center(child: CircularProgressIndicator())
          : propertyProvider.error != null
              ? Center(child: Text(propertyProvider.error!, style: GoogleFonts.outfit()))
              : propertyProvider.properties.isEmpty
                  ? _buildEmptyState()
                  : ListView.separated(
                      padding: const EdgeInsets.all(16),
                      itemCount: propertyProvider.properties.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 16),
                      itemBuilder: (context, index) =>
                          _buildPropertyCard(propertyProvider.properties[index]),
                    ),
    );
  }

  Widget _buildPropertyCard(Map<String, dynamic> property) {
    final photos = property['photos'] as List? ?? [];
    final imageUrl = photos.isNotEmpty ? photos[0]['url'] : null;
    final isActive = property['status'] == 'active';

    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => PropertyDetailsScreen(property: property),
          ),
        );
      },
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.06),
              blurRadius: 10,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
               // Left Image
               Container(
                 width: 90,
                 height: 90,
                 decoration: BoxDecoration(
                   borderRadius: BorderRadius.circular(8),
                   color: Colors.grey.shade200,
                   image: imageUrl != null 
                      ? DecorationImage(image: NetworkImage(imageUrl), fit: BoxFit.cover)
                      : null,
                 ),
                 child: imageUrl == null 
                    ? Icon(Icons.apartment, color: Colors.grey.shade400, size: 40)
                    : null,
               ),
               const SizedBox(width: 16),
               
               // Middle Info
               Expanded(
                 child: Column(
                   crossAxisAlignment: CrossAxisAlignment.start,
                   children: [
                     Row(
                       mainAxisAlignment: MainAxisAlignment.spaceBetween,
                       children: [
                         Expanded(
                           child: Text(
                             property['name'] ?? 'Property Name',
                             style: GoogleFonts.outfit(
                               fontSize: 16,
                               fontWeight: FontWeight.bold,
                               color: const Color(0xFF1E293B),
                             ),
                             maxLines: 1, overflow: TextOverflow.ellipsis,
                           ),
                         ),
                         // Status Switch
                         Transform.scale(
                           scale: 0.8,
                           child: Switch(
                             value: isActive,
                             onChanged: (val) {
                               Provider.of<PropertyProvider>(context, listen: false).toggleStatus(property['id']);
                             },
                             activeColor: Colors.blue,
                           ),
                         ),
                       ],
                     ),
                     const SizedBox(height: 4),
                     Row(
                       children: [
                         if (property['category'] != null)
                           Container(
                             padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                             decoration: BoxDecoration(
                               color: Colors.blue.shade50,
                               borderRadius: BorderRadius.circular(4),
                             ),
                             child: Text(
                               property['category']['name'] ?? '',
                               style: GoogleFonts.outfit(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.blue.shade700),
                             ),
                           ),
                         const SizedBox(width: 8),
                         Expanded(
                           child: Text(
                             property['location']?['city']?['name'] ?? 'Location',
                             style: GoogleFonts.outfit(fontSize: 13, color: Colors.grey[600]),
                             maxLines: 1, overflow: TextOverflow.ellipsis,
                           ),
                         ),
                       ],
                     ),
                     const SizedBox(height: 12),
                     
                     // Bottom Stats
                     Row(
                       mainAxisAlignment: MainAxisAlignment.spaceBetween,
                       children: [
                         Container(
                           padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                           decoration: BoxDecoration(
                             color: Colors.grey.shade100,
                             borderRadius: BorderRadius.circular(6),
                           ),
                           child: Row(
                             children: [
                               Icon(Icons.bed, size: 14, color: Colors.grey[600]),
                               const SizedBox(width: 4),
                               Text(
                                 '${property['property_accommodations_count'] ?? 0} Units',
                                 style: GoogleFonts.outfit(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey[700]),
                               ),
                             ],
                           ),
                         ),
                         
                         // Edit Button
                         IconButton(
                           icon: const Icon(Icons.edit, size: 20, color: Colors.blueGrey),
                           padding: EdgeInsets.zero,
                           constraints: const BoxConstraints(),
                           onPressed: () => _showEditDialog(property),
                         ),
                       ],
                     ),
                   ],
                 ),
               ),
            ],
          ),
        ),
      ),
    );
  }

  void _showEditDialog(Map<String, dynamic> property) {
    final nameController = TextEditingController(text: property['name']);
    final descController = TextEditingController(text: property['description']);
    
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Edit Property', style: GoogleFonts.outfit(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: nameController,
              decoration: const InputDecoration(labelText: 'Property Name', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: descController,
              decoration: const InputDecoration(labelText: 'Description', border: OutlineInputBorder()),
              maxLines: 3,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Cancel', style: GoogleFonts.outfit(color: Colors.grey)),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context);
              final success = await Provider.of<PropertyProvider>(context, listen: false).updateProperty(
                property['id'], 
                {
                  'name': nameController.text,
                  'description': descController.text,
                }
              );
              
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Property updated successfully')));
              }
            },
            style: ElevatedButton.styleFrom(backgroundColor: Colors.blue),
            child: Text('Save', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: Colors.white)),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.apartment_rounded, size: 64, color: Colors.grey[300]),
          const SizedBox(height: 16),
          Text(
            'No properties found',
            style: GoogleFonts.outfit(color: Colors.grey[500], fontSize: 16),
          ),
        ],
      ),
    );
  }
}
