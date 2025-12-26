import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../providers/booking_provider.dart';
import '../providers/property_provider.dart';
import '../providers/guest_provider.dart';
import '../providers/b2b_provider.dart';
import '../widgets/searchable_input.dart';

class CreateBookingScreen extends StatefulWidget {
  final String? initialPropertyId;
  final String? initialAccommodationId;
  final Map<String, dynamic>? editingBooking;

  const CreateBookingScreen({
    super.key,
    this.initialPropertyId,
    this.initialAccommodationId,
    this.editingBooking,
  });

  @override
  State<CreateBookingScreen> createState() => _CreateBookingScreenState();
}

class _CreateBookingScreenState extends State<CreateBookingScreen> {
  int _currentStep = 0; // 0: Stay, 1: Guest, 2: Payment
  final _formKey = GlobalKey<FormState>();

  // Use page controller for animations? Or just switching
  // Using switching for simplicity with persistent state

  // Stay
  String? _selectedPropertyId;
  String? _selectedAccommodationId;
  List<dynamic> _accommodations = [];
  DateTimeRange? _dateRange;
  int _adults = 1;
  int _children = 0;

  // Guest
  String _sourceType = 'direct';
  String? _selectedB2bPartnerId;
  Map<String, dynamic>? _selectedPartner;
  String? _selectedGuestId;
  Map<String, dynamic>? _selectedGuest;
  bool _useReservedCustomer = false;
  final _guestNameController = TextEditingController();
  final _guestMobileController = TextEditingController();
  final _guestEmailController = TextEditingController();
  bool _isNewGuest = true;

  // Payment
  final _totalAmountController = TextEditingController();
  final _advanceAmountController = TextEditingController();
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _selectedPropertyId = widget.initialPropertyId;
    _selectedAccommodationId = widget.initialAccommodationId;
    
    Future.microtask(() async {
      await Provider.of<PropertyProvider>(context, listen: false).fetchProperties();
      Provider.of<B2bProvider>(context, listen: false).fetchPartners();
      Provider.of<GuestProvider>(context, listen: false).fetchGuests();
      
      if (mounted) {
         if (widget.initialPropertyId != null) {
            _initializeSelection();
         } else if (widget.editingBooking != null) {
            _initializeEditMode();
         }
      }
    });
  }

  void _initializeSelection() {
    final provider = Provider.of<PropertyProvider>(context, listen: false);
    final props = provider.properties;
    final propExists = props.any((p) => p['id'].toString() == widget.initialPropertyId);
    
    if (propExists) {
        final selectedProp = props.firstWhere((p) => p['id'].toString() == widget.initialPropertyId);
        setState(() {
            if (selectedProp['property_accommodations'] != null) {
                _accommodations = selectedProp['property_accommodations'];
            }
            if (widget.initialAccommodationId != null) {
                if (_accommodations.any((a) => a['id'].toString() == widget.initialAccommodationId)) {
                   // Everything set
                }
            }
        });
    }
  }

  void _initializeEditMode() {
    final booking = widget.editingBooking!;
    final provider = Provider.of<PropertyProvider>(context, listen: false);
    final props = provider.properties;
    setState(() {
       final propId = booking['accommodation']?['property']?['id']?.toString();
       if (propId != null && props.any((p) => p['id'].toString() == propId)) {
           _selectedPropertyId = propId;
           final selectedProp = props.firstWhere((p) => p['id'].toString() == propId);
           _accommodations = selectedProp['property_accommodations'] ?? [];
           _selectedAccommodationId = booking['accommodation']?['id']?.toString();
       }
       _dateRange = DateTimeRange(
         start: DateTime.parse(booking['check_in_date']), 
         end: DateTime.parse(booking['check_out_date'])
       );
       _adults = booking['adults'];
       _children = booking['children'];
       _totalAmountController.text = booking['total_amount'].toString();
       _advanceAmountController.text = booking['advance_paid'].toString();
       
       if (booking['b2b_partner_id'] != null) {
          _sourceType = 'b2b';
          _selectedB2bPartnerId = booking['b2b_partner_id'].toString(); // Careful with int/string
          // Try to set partner object if available in booking details
          if (booking['b2b_partner'] != null) {
             _selectedPartner = booking['b2b_partner'];
          }
       }
       
       if (booking['guest'] != null) {
           _isNewGuest = true; // Default to showing filled fields
           _guestNameController.text = booking['guest']['name'] ?? '';
           _guestMobileController.text = booking['guest']['mobile_number'] ?? '';
           _guestEmailController.text = booking['guest']['email'] ?? '';
           
           // If we are editing, we might want to allow switching to "Existing Guest" mode with this guest selected
           // But for now keeping logic as is, just pre-filling text fields.
           // However, if it was an existing guest, we might want to store it.
           _selectedGuest = booking['guest'];
           _selectedGuestId = booking['guest']['id']?.toString();
       }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          widget.editingBooking != null ? 'Edit Booking' : 'New Booking',
          style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: const Color(0xFF1E293B)),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black87),
        centerTitle: true,
      ),
      body: SafeArea(
        child: Column(
          children: [
            _buildTabs(),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Form(
                  key: _formKey,
                  child: AnimatedSwitcher(
                    duration: const Duration(milliseconds: 300),
                    child: _buildCurrentStep(),
                  ),
                ),
              ),
            ),
            _buildBottomBar(),
          ],
        ),
      ),
    );
  }

  Widget _buildTabs() {
    return Container(
      color: Colors.white,
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: [
          _buildTabItem(0, 'Stay', Icons.king_bed_outlined),
          _buildTabItem(1, 'Guest', Icons.person_outline),
          _buildTabItem(2, 'Payment', Icons.payment_outlined),
        ],
      ),
    );
  }

  Widget _buildTabItem(int index, String label, IconData icon) {
    final isActive = _currentStep == index;
    final isCompleted = _currentStep > index;
    final color = isActive ? Colors.blue : (isCompleted ? Colors.green : Colors.grey);

    return InkWell(
      onTap: () {
        if (index < _currentStep) setState(() => _currentStep = index);
      },
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: isActive ? Colors.blue.shade50 : (isCompleted ? Colors.green.shade50 : Colors.grey.shade50),
              shape: BoxShape.circle,
            ),
            child: Icon(
              isCompleted ? Icons.check : icon,
              color: color,
              size: 20,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: GoogleFonts.outfit(
              fontSize: 12,
              fontWeight: isActive ? FontWeight.bold : FontWeight.w500,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCurrentStep() {
    switch (_currentStep) {
      case 0:
        return _buildStayStep();
      case 1:
        return _buildGuestStep();
      case 2:
        return _buildPaymentStep();
      default:
        return const SizedBox.shrink();
    }
  }

  // --- Step 1: Stay ---
  Widget _buildStayStep() {
    final properties = Provider.of<PropertyProvider>(context).properties;

    return Column(
      key: const ValueKey(0),
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionHeader('Where & When'),
        const SizedBox(height: 16),
        
        // Property Dropdown
        _buildDropdownCard<String>(
          icon: Icons.apartment,
          hint: 'Select Property',
          value: _selectedPropertyId,
          items: properties.map<DropdownMenuItem<String>>((prop) => DropdownMenuItem(value: prop['id'].toString(), child: Text(prop['name']))).toList(),
          onChanged: (widget.initialPropertyId != null) ? null : (String? val) {
             setState(() {
                _selectedPropertyId = val;
                final selectedProp = properties.firstWhere((p) => p['id'].toString() == val, orElse: () => {});
                _accommodations = selectedProp['property_accommodations'] ?? [];
                _selectedAccommodationId = null;
             });
          },
        ),
        const SizedBox(height: 12),
        
        // Accommodation Dropdown
        _buildDropdownCard<String>(
          icon: Icons.bed,
          hint: 'Select Room / Unit',
          value: _selectedAccommodationId,
          items: _accommodations.map<DropdownMenuItem<String>>((acc) {
             String name = acc['custom_name'] ?? acc['predefined_type']?['name'] ?? 'Room ${acc['id']}';
             return DropdownMenuItem(value: acc['id'].toString(), child: Text(name));
          }).toList(),
          onChanged: (widget.initialAccommodationId != null) ? null : (String? val) => setState(() => _selectedAccommodationId = val),
        ),
        const SizedBox(height: 12),
        
        // Date Picker
        GestureDetector(
          onTap: () async {
            final picked = await showDateRangePicker(
              context: context,
              firstDate: DateTime.now(),
              lastDate: DateTime.now().add(const Duration(days: 365)),
              builder: (context, child) {
                 return Theme(
                   data: Theme.of(context).copyWith(
                     colorScheme: ColorScheme.light(primary: Colors.blue),
                   ),
                   child: child!,
                 );
              }
            );
            if (picked != null) setState(() => _dateRange = picked);
          },
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.grey.shade200),
              boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)],
            ),
            child: Row(
              children: [
                const Icon(Icons.date_range, color: Colors.blue),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Check-in - Check-out', style: GoogleFonts.outfit(color: Colors.grey, fontSize: 10)),
                    Text(
                      _dateRange == null 
                        ? 'Select Dates' 
                        : '${DateFormat('EEE, d MMM').format(_dateRange!.start)}  —  ${DateFormat('EEE, d MMM').format(_dateRange!.end)}',
                      style: GoogleFonts.outfit(
                        fontSize: 14, 
                        fontWeight: FontWeight.w600, 
                        color: _dateRange == null ? Colors.grey.shade400 : Colors.black87
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 24),
        
        _buildSectionHeader('Occupancy'),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(child: _buildCounter('Adults', _adults, (v) => setState(() => _adults = v))),
            const SizedBox(width: 16),
            Expanded(child: _buildCounter('Children', _children, (v) => setState(() => _children = v))),
          ],
        ),
      ],
    ).animate().fadeIn().slideX();
  }

  // --- Step 2: Guest ---
  Widget _buildGuestStep() {
    final b2bPartners = Provider.of<B2bProvider>(context).partners;

    return Column(
      key: const ValueKey(1),
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildSectionHeader('Who is booking?'),
        const SizedBox(height: 16),
        
        Container(
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(color: Colors.grey.shade200, borderRadius: BorderRadius.circular(12)),
          child: Row(
             children: [
               Expanded(child: _buildToggleOption('Direct', 'direct')),
               Expanded(child: _buildToggleOption('B2B Partner', 'b2b')),
             ],
          ),
        ),
        const SizedBox(height: 24),
        
        if (_sourceType == 'b2b') ...[
           SearchableInput<dynamic>(
             label: 'Partner',
             hint: 'Select Partner',
             icon: Icons.handshake,
             value: _selectedPartner,
             displayStringForOption: (p) => p['partner_name'] ?? p['name'] ?? 'Partner',
             onSearch: (query) async {
                await Provider.of<B2bProvider>(context, listen: false).fetchPartners(search: query, isRefresh: true);
                return Provider.of<B2bProvider>(context, listen: false).partners;
             },
             onChanged: (val) {
                setState(() {
                  _selectedPartner = val;
                  _selectedB2bPartnerId = val['uuid']?.toString() ?? val['id']?.toString();
                });
             },
           ),
           const SizedBox(height: 12),
           SwitchListTile(
              title: Text('Use Reserved Customer', style: GoogleFonts.outfit(fontWeight: FontWeight.w600)),
              subtitle: Text('Block dates using Partner\'s reserved profile', style: GoogleFonts.outfit(fontSize: 12)),
              value: _useReservedCustomer,
              onChanged: (val) => setState(() => _useReservedCustomer = val),
              activeColor: Colors.blue,
              contentPadding: EdgeInsets.zero,
           ),
        ],

        if (_sourceType == 'direct') ...[
           SwitchListTile(
              title: Text('Block Dates (Maintenance)', style: GoogleFonts.outfit(fontWeight: FontWeight.w600)),
              subtitle: Text('Book as "Reserved" for this unit', style: GoogleFonts.outfit(fontSize: 12)),
              value: _useReservedCustomer,
              onChanged: (val) => setState(() => _useReservedCustomer = val),
              activeColor: Colors.red,
              contentPadding: EdgeInsets.zero,
           ),
        ],
        
        if (!_useReservedCustomer) ...[
           const Divider(height: 32),
           _buildSectionHeader('Guest Details'),
           const SizedBox(height: 12),
           
           // Toggle New/Existing
           Row(
             children: [
                ChoiceChip(
                  label: const Text('New Guest'),
                  selected: _isNewGuest,
                  onSelected: (val) => setState(() => _isNewGuest = true),
                  labelStyle: GoogleFonts.outfit(color: _isNewGuest ? Colors.white : Colors.black),
                  selectedColor: Colors.blue,
                ),
                const SizedBox(width: 12),
                ChoiceChip(
                  label: const Text('Existing Guest'),
                  selected: !_isNewGuest,
                  onSelected: (val) => setState(() => _isNewGuest = false),
                  labelStyle: GoogleFonts.outfit(color: !_isNewGuest ? Colors.white : Colors.black),
                  selectedColor: Colors.blue,
                ),
             ],
           ),
           const SizedBox(height: 16),
           
           if (_isNewGuest) ...[
              _buildModernTextField(_guestNameController, 'Guest Name', Icons.person),
              const SizedBox(height: 12),
              _buildModernTextField(_guestMobileController, 'Mobile Number', Icons.phone, type: TextInputType.phone),
              const SizedBox(height: 12),
              _buildModernTextField(_guestEmailController, 'Email (Optional)', Icons.email, type: TextInputType.emailAddress),
           ] else ...[
               SearchableInput<dynamic>(
                 label: 'Guest',
                 hint: 'Select Guest',
                 icon: Icons.person_search,
                 value: _selectedGuest,
                 displayStringForOption: (g) => '${g['name']} (${g['mobile_number']})',
                 onSearch: (query) async {
                    await Provider.of<GuestProvider>(context, listen: false).fetchGuests(search: query, isRefresh: true);
                    return Provider.of<GuestProvider>(context, listen: false).guests;
                 },
                 onChanged: (val) {
                    setState(() {
                       _selectedGuest = val;
                       _selectedGuestId = val['id']?.toString();
                    });
                 },
               ),
           ]
        ]
      ],
    ).animate().fadeIn().slideX();
  }

  // --- Step 3: Payment ---
  Widget _buildPaymentStep() {
    return Column(
      key: const ValueKey(2),
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
         _buildSectionHeader('Payment Info'),
         const SizedBox(height: 16),
         _buildModernTextField(_totalAmountController, 'Total Amount', Icons.currency_rupee, type: TextInputType.number),
         const SizedBox(height: 12),
         _buildModernTextField(_advanceAmountController, 'Advance Paid', Icons.payments_outlined, type: TextInputType.number),
         const SizedBox(height: 24),
         
         Container(
           padding: const EdgeInsets.all(16),
           decoration: BoxDecoration(color: Colors.blue.shade50, borderRadius: BorderRadius.circular(12)),
           child: Row(
             children: [
               const Icon(Icons.info_outline, color: Colors.blue),
               const SizedBox(width: 12),
               Expanded(
                 child: Text(
                   'Balance can be collected later. Invoice will be generated automatically.',
                   style: GoogleFonts.outfit(fontSize: 12, color: Colors.blue.shade900),
                 ),
               ),
             ],
           ),
         ),
      ],
    ).animate().fadeIn().slideX();
  }

  // --- Widgets ---

  Widget _buildSectionHeader(String title) {
    return Text(
      title,
      style: GoogleFonts.outfit(fontSize: 18, fontWeight: FontWeight.bold, color: const Color(0xFF1E293B)),
    );
  }

  Widget _buildToggleOption(String label, String value) {
     final isSelected = _sourceType == value;
     return GestureDetector(
       onTap: () {
         setState(() {
           _sourceType = value;
           _useReservedCustomer = false;
         });
       },
       child: Container(
         alignment: Alignment.center,
         padding: const EdgeInsets.symmetric(vertical: 10),
         decoration: BoxDecoration(
           color: isSelected ? Colors.white : Colors.transparent,
           borderRadius: BorderRadius.circular(10),
           boxShadow: isSelected ? [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 4)] : null,
         ),
         child: Text(
           label,
           style: GoogleFonts.outfit(
             fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
             color: isSelected ? Colors.blue.shade800 : Colors.grey.shade600,
           ),
         ),
       ),
     );
  }

  Widget _buildDropdownCard<T>({
    required IconData icon,
    required String hint,
    required T? value,
    required List<DropdownMenuItem<T>> items,
    required Function(T?)? onChanged,
  }) {
    // Deduplicate items
    final uniqueItems = <DropdownMenuItem<T>>[];
    final seenValues = <T>{};

    for (var item in items) {
      if (item.value != null && !seenValues.contains(item.value)) {
        seenValues.add(item.value as T);
        uniqueItems.add(item);
      }
    }

    // Ensure value is present in items
    T? effectiveValue = (value != null && seenValues.contains(value)) ? value : null;

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)],
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<T>(
          value: effectiveValue,
          hint: Row(
            children: [
              Icon(icon, color: Colors.grey.shade400, size: 20),
              const SizedBox(width: 12),
              Text(hint, style: GoogleFonts.outfit(color: Colors.grey.shade400, fontSize: 14)),
            ],
          ),
          icon: const Icon(Icons.keyboard_arrow_down_rounded),
          isExpanded: true,
          items: uniqueItems,
          onChanged: onChanged,
          style: GoogleFonts.outfit(color: Colors.black87, fontSize: 15),
          dropdownColor: Colors.white,
        ),
      ),
    );
  }

  Widget _buildModernTextField(TextEditingController controller, String label, IconData icon, {TextInputType type = TextInputType.text}) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 5)],
      ),
      child: TextFormField(
        controller: controller,
        keyboardType: type,
        style: GoogleFonts.outfit(color: Colors.black87),
        decoration: InputDecoration(
          labelText: label,
          labelStyle: GoogleFonts.outfit(color: Colors.grey.shade500),
          prefixIcon: Icon(icon, color: Colors.grey.shade400),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
          filled: true,
          fillColor: Colors.white,
          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        ),
      ),
    );
  }

  Widget _buildCounter(String label, int value, Function(int) onChanged) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: GoogleFonts.outfit(fontSize: 12, color: Colors.grey)),
          const SizedBox(height: 8),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              InkWell(
                onTap: () => value > 0 ? onChanged(value - 1) : null,
                child: Icon(Icons.remove_circle_outline, color: Colors.blue.shade300),
              ),
              Text('$value', style: GoogleFonts.outfit(fontWeight: FontWeight.bold, fontSize: 18)),
              InkWell(
                onTap: () => onChanged(value + 1),
                child: const Icon(Icons.add_circle_outline, color: Colors.blue),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildBottomBar() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, -5))],
      ),
      child: Row(
        children: [
          if (_currentStep > 0)
            Expanded(
              child: OutlinedButton(
                onPressed: () => setState(() => _currentStep--),
                style: OutlinedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  side: BorderSide(color: Colors.grey.shade300),
                ),
                child: Text('Back', style: GoogleFonts.outfit(color: Colors.grey.shade700, fontWeight: FontWeight.bold)),
              ),
            ),
          if (_currentStep > 0) const SizedBox(width: 16),
          Expanded(
            flex: 2,
            child: ElevatedButton(
              onPressed: _isLoading ? null : _handleNext,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF4F46E5), // Indigo
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                elevation: 0,
              ),
              child: _isLoading 
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                : Text(
                  _currentStep == 2 
                      ? (widget.editingBooking != null ? 'Update Booking' : 'Confirm Booking') 
                      : 'Next Step',
                  style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: Colors.white),
                ),
            ),
          ),
        ],
      ),
    );
  }

  void _handleNext() async {
    if (_currentStep == 0) {
       if (_selectedPropertyId == null || _selectedAccommodationId == null || _dateRange == null) {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please select property, room and dates')));
          return;
       }
       setState(() => _currentStep++);
    } else if (_currentStep == 1) {
       // Validate Guest
       if (_sourceType == 'b2b' && _selectedB2bPartnerId == null) {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please select a Partner')));
          return;
       }
       if (!_useReservedCustomer && _isNewGuest && _guestNameController.text.isEmpty) {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please enter guest name')));
          return;
       }
       setState(() => _currentStep++);
    } else {
       // Final Submit
       _submitBooking();
    }
  }

  Future<void> _submitBooking() async {
    setState(() => _isLoading = true);
    try {
        // Logic for Guest ID
        dynamic finalGuestId;
        
        if (_useReservedCustomer) {
             // Find reserved customer ID from Partner or Accommodation
              if (_sourceType == 'b2b') {
                  final partner = _selectedPartner;
                  if (partner == null) throw 'Partner not selected';
                  finalGuestId = partner['reserved_customer']?['id'];
              } else {
                 final accs = _accommodations;
                 final acc = accs.firstWhere((a) => a['id'].toString() == _selectedAccommodationId, orElse: () => {});
                 finalGuestId = acc['reserved_customer']?['id'];
             }
             if (finalGuestId == null) {
                throw 'Reserved Customer profile not found. Please contact support.';
             }
        } else {
           finalGuestId = !_isNewGuest ? _selectedGuestId : null;
        }

        final bookingData = {
          'property_id': _selectedPropertyId,
          'accommodation_id': _selectedAccommodationId,
          'check_in_date': _dateRange?.start.toIso8601String(),
          'check_out_date': _dateRange?.end.toIso8601String(),
          'adults': _adults,
          'children': _children,
          'booking_type': 'per_day',
          'total_amount': double.tryParse(_totalAmountController.text) ?? 0,
          'advance_paid': double.tryParse(_advanceAmountController.text) ?? 0,
          'b2b_partner_id': _sourceType == 'b2b' ? _selectedB2bPartnerId : null,
          'guest_name': (!_useReservedCustomer && _isNewGuest) ? _guestNameController.text : null,
          'guest_mobile': (!_useReservedCustomer && _isNewGuest) ? _guestMobileController.text : null,
          'guest_email': (!_useReservedCustomer && _isNewGuest) ? _guestEmailController.text : null,
          'guest_id': finalGuestId,
        };

        final success = widget.editingBooking != null
            ? await Provider.of<BookingProvider>(context, listen: false).editBooking(widget.editingBooking!['id'], bookingData)
            : await Provider.of<BookingProvider>(context, listen: false).createBooking(bookingData);

        if (!mounted) return;

        if (success) {
           Navigator.pop(context);
           ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Booking Successful!')));
        } else {
           final error = Provider.of<BookingProvider>(context, listen: false).error;
           ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(error ?? 'Failed')));
        }
    } catch (e) {
       if (mounted) {
           ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
       }
    } finally {
       if (mounted) {
           setState(() => _isLoading = false);
       }
    }
  }
}
