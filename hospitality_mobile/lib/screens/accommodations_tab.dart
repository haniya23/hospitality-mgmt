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
    Future.microtask(() {
      final provider = Provider.of<PropertyProvider>(context, listen: false);
      provider.fetchProperties();
      provider.fetchPredefinedTypes();
      provider.fetchAmenities();
    });
  }

  void _showAddAccommodationSheet(BuildContext context) {
    final provider = Provider.of<PropertyProvider>(context, listen: false);
    final customNameController = TextEditingController();
    final basePriceController = TextEditingController();
    final maxOccupancyController = TextEditingController(text: '2');
    final sizeController = TextEditingController(text: '0');
    final descController = TextEditingController();
    
    int? selectedPropertyId;
    int? selectedPredefinedTypeId;
    final List<int> selectedAmenityIds = [];
    bool isSaving = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: const Color(0xFFF2F5F0), // Warm cream background
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(30)),
      ),
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setSheetState) {
            final properties = provider.properties;
            final types = provider.predefinedTypes;
            final amenities = provider.amenities;

            return Padding(
              padding: EdgeInsets.fromLTRB(
                24,
                24,
                24,
                MediaQuery.of(context).viewInsets.bottom + 24,
              ),
              child: SizedBox(
                height: MediaQuery.of(context).size.height * 0.75,
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Center(
                        child: Container(
                          width: 50,
                          height: 5,
                          decoration: BoxDecoration(
                            color: const Color(0xFF2E3E2A).withOpacity(0.2),
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),
                      Text(
                        'Add New Accommodation',
                        style: GoogleFonts.outfit(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF191D19),
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Select Property Dropdown
                      Text(
                        'Select Property',
                        style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      DropdownButtonFormField<int>(
                        value: selectedPropertyId,
                        dropdownColor: Colors.white,
                        decoration: InputDecoration(
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.12)),
                          ),
                        ),
                        items: properties.map<DropdownMenuItem<int>>((prop) {
                          return DropdownMenuItem<int>(
                            value: prop['id'],
                            child: Text(prop['name'] ?? 'Unnamed', style: GoogleFonts.outfit()),
                          );
                        }).toList(),
                        onChanged: isSaving ? null : (val) {
                          setSheetState(() {
                            selectedPropertyId = val;
                          });
                        },
                      ),
                      const SizedBox(height: 20),

                      // Custom Name Field
                      Text(
                        'Room / Accommodation Name',
                        style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      TextField(
                        controller: customNameController,
                        enabled: !isSaving,
                        decoration: InputDecoration(
                          hintText: 'e.g. Deluxe Ocean View Suite',
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.12)),
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Predefined Type Choice Chips
                      Text(
                        'Accommodation Type',
                        style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      types.isEmpty
                          ? const Center(child: CircularProgressIndicator())
                          : Wrap(
                              spacing: 8,
                              runSpacing: 8,
                              children: types.map<Widget>((type) {
                                final isSelected = selectedPredefinedTypeId == type['id'];
                                return ChoiceChip(
                                  label: Text(type['name']),
                                  selected: isSelected,
                                  onSelected: isSaving ? null : (selected) {
                                    setSheetState(() {
                                      selectedPredefinedTypeId = selected ? type['id'] : null;
                                    });
                                  },
                                  selectedColor: const Color(0xFF2E3E2A),
                                  backgroundColor: Colors.white,
                                  labelStyle: GoogleFonts.outfit(
                                    color: isSelected ? Colors.white : const Color(0xFF2E3E2A),
                                    fontWeight: FontWeight.w600,
                                  ),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    side: BorderSide(
                                      color: isSelected
                                          ? const Color(0xFF2E3E2A)
                                          : const Color(0xFF2E3E2A).withOpacity(0.12),
                                    ),
                                  ),
                                );
                              }).toList(),
                            ),
                      const SizedBox(height: 20),

                      // Base Price & Max Occupancy & Size Fields
                      Row(
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Base Price (₹)',
                                  style: GoogleFonts.outfit(
                                    fontWeight: FontWeight.bold,
                                    color: const Color(0xFF2E3E2A),
                                    fontSize: 14,
                                  ),
                                ),
                                const SizedBox(height: 8),
                                TextField(
                                  controller: basePriceController,
                                  keyboardType: TextInputType.number,
                                  enabled: !isSaving,
                                  decoration: InputDecoration(
                                    hintText: 'e.g. 1500',
                                    filled: true,
                                    fillColor: Colors.white,
                                    border: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(16),
                                      borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.12)),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Max Occupancy',
                                  style: GoogleFonts.outfit(
                                    fontWeight: FontWeight.bold,
                                    color: const Color(0xFF2E3E2A),
                                    fontSize: 14,
                                  ),
                                ),
                                const SizedBox(height: 8),
                                TextField(
                                  controller: maxOccupancyController,
                                  keyboardType: TextInputType.number,
                                  enabled: !isSaving,
                                  decoration: InputDecoration(
                                    hintText: 'e.g. 2',
                                    filled: true,
                                    fillColor: Colors.white,
                                    border: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(16),
                                      borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.12)),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),

                      // Size Field
                      Text(
                        'Size (Sq Ft)',
                        style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      TextField(
                        controller: sizeController,
                        keyboardType: TextInputType.number,
                        enabled: !isSaving,
                        decoration: InputDecoration(
                          hintText: 'e.g. 350',
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.12)),
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Description
                      Text(
                        'Description',
                        style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      TextField(
                        controller: descController,
                        maxLines: 3,
                        enabled: !isSaving,
                        decoration: InputDecoration(
                          hintText: 'Room view, bed config, etc...',
                          filled: true,
                          fillColor: Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.12)),
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),

                      // Amenities Selection
                      Text(
                        'Amenities',
                        style: GoogleFonts.outfit(
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2E3E2A),
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      amenities.isEmpty
                          ? const Center(child: CircularProgressIndicator())
                          : Wrap(
                              spacing: 8,
                              runSpacing: 8,
                              children: amenities.map<Widget>((amenity) {
                                final isSelected = selectedAmenityIds.contains(amenity['id']);
                                return FilterChip(
                                  label: Text(amenity['name']),
                                  selected: isSelected,
                                  onSelected: isSaving ? null : (selected) {
                                    setSheetState(() {
                                      if (selected) {
                                        selectedAmenityIds.add(amenity['id']);
                                      } else {
                                        selectedAmenityIds.remove(amenity['id']);
                                      }
                                    });
                                  },
                                  selectedColor: const Color(0xFF5A7251).withOpacity(0.2),
                                  checkmarkColor: const Color(0xFF2E3E2A),
                                  backgroundColor: Colors.white,
                                  labelStyle: GoogleFonts.outfit(
                                    color: const Color(0xFF2E3E2A),
                                    fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                  ),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                    side: BorderSide(
                                      color: isSelected
                                          ? const Color(0xFF2E3E2A)
                                          : const Color(0xFF2E3E2A).withOpacity(0.12),
                                    ),
                                  ),
                                );
                              }).toList(),
                            ),
                      const SizedBox(height: 32),

                      // Submit Button
                      SizedBox(
                        width: double.infinity,
                        height: 56,
                        child: ElevatedButton(
                          onPressed: isSaving ? null : () async {
                            if (selectedPropertyId == null ||
                                customNameController.text.trim().isEmpty ||
                                basePriceController.text.trim().isEmpty) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('Please fill out Property, Name, and Price')),
                              );
                              return;
                            }

                            setSheetState(() {
                              isSaving = true;
                            });

                            final messenger = ScaffoldMessenger.of(context);

                            final success = await provider.addAccommodation(
                              propertyId: selectedPropertyId!,
                              customName: customNameController.text.trim(),
                              predefinedTypeId: selectedPredefinedTypeId ?? 3,
                              basePrice: double.tryParse(basePriceController.text) ?? 0.0,
                              maxOccupancy: int.tryParse(maxOccupancyController.text) ?? 2,
                              size: double.tryParse(sizeController.text) ?? 0.0,
                              description: descController.text.trim(),
                              amenityIds: selectedAmenityIds,
                            );

                            if (success) {
                              if (context.mounted) {
                                Navigator.pop(context);
                              }
                              messenger.showSnackBar(
                                const SnackBar(content: Text('Accommodation added successfully!')),
                              );
                            } else {
                              setSheetState(() {
                                isSaving = false;
                              });
                              messenger.showSnackBar(
                                SnackBar(content: Text(provider.error ?? 'Failed to add accommodation')),
                              );
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF191D19),
                            foregroundColor: Colors.white,
                            disabledBackgroundColor: const Color(0xFF191D19).withOpacity(0.6),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20),
                            ),
                          ),
                          child: isSaving
                              ? const SizedBox(
                                  width: 24,
                                  height: 24,
                                  child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                                )
                              : Text(
                                  'Create Accommodation',
                                  style: GoogleFonts.outfit(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                        ),
                      ),
                      const SizedBox(height: 24),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
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
        acc['property_name'] = prop['name'];
        acc['property_id'] = prop['id'];
        allAccommodations.add(acc);
      }
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF2F5F0), // Warm organic cream
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.menu_rounded, color: Color(0xFF191D19)),
          onPressed: () => MainLayout.scaffoldKey.currentState?.openDrawer(),
        ),
        title: Text(
          'Accommodations',
          style: GoogleFonts.outfit(
            fontWeight: FontWeight.bold,
            color: const Color(0xFF191D19),
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: Color(0xFF191D19)),
            onPressed: () => propertyProvider.fetchProperties(),
          ),
          IconButton(
            icon: const Icon(Icons.add_circle_outline_rounded, color: Color(0xFF2E3E2A), size: 28),
            onPressed: () => _showAddAccommodationSheet(context),
          ),
        ],
      ),
      body: propertyProvider.isLoading
          ? const Center(child: CircularProgressIndicator())
          : allAccommodations.isEmpty
              ? _buildEmptyState()
              : ListView.separated(
                  padding: const EdgeInsets.fromLTRB(16, 16, 16, 100), // Extra bottom padding for navigation bar
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
          borderRadius: BorderRadius.circular(24),
          border: Border.all(
            color: const Color(0xFF2E3E2A).withOpacity(0.08),
            width: 1.5,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF2E3E2A).withOpacity(0.02),
              blurRadius: 15,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        child: Row(
          children: [
            // Image with rounded corners
            Container(
              width: 110,
              height: 110,
              decoration: const BoxDecoration(
                color: Color(0xFFF2F5F0),
                borderRadius: BorderRadius.horizontal(left: Radius.circular(22)),
              ),
              child: ClipRRect(
                borderRadius: const BorderRadius.horizontal(left: Radius.circular(22)),
                child: photos.isNotEmpty
                    ? Image.network(
                        photos[0]['url'] ?? '',
                        fit: BoxFit.cover,
                        errorBuilder: (c, e, s) => const Icon(Icons.hotel_rounded, color: Color(0xFF5A7251)),
                      )
                    : const Icon(Icons.hotel_rounded, color: Color(0xFF5A7251), size: 36),
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
                        style: GoogleFonts.outfit(
                          fontSize: 10,
                          color: const Color(0xFF5A7251),
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    const SizedBox(height: 2),
                    Text(
                      name,
                      style: GoogleFonts.outfit(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF191D19),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 6),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        if (price != null)
                          Text(
                            '₹$price',
                            style: GoogleFonts.outfit(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: const Color(0xFF2E3E2A),
                            ),
                          ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(
                            color: const Color(0xFFF2F5F0),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.people_alt_rounded, size: 12, color: Color(0xFF2E3E2A)),
                              const SizedBox(width: 4),
                              Text(
                                '${acc['max_occupancy'] ?? 2}',
                                style: GoogleFonts.outfit(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: const Color(0xFF2E3E2A),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.0),
              child: Icon(Icons.arrow_forward_ios_rounded, size: 14, color: Color(0xFF5A7251)),
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
          const Icon(Icons.bed_outlined, size: 64, color: Color(0xFF5A7251)),
          const SizedBox(height: 16),
          Text(
            'No accommodations found',
            style: GoogleFonts.outfit(color: Colors.grey[500]),
          ),
        ],
      ),
    );
  }
}
