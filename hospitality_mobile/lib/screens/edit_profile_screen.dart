import 'dart:io';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';

class EditProfileScreen extends StatefulWidget {
  const EditProfileScreen({super.key});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _mobileController;
  late TextEditingController _pinController;
  
  File? _imageFile;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    final auth = Provider.of<AuthProvider>(context, listen: false);
    _nameController = TextEditingController(text: auth.userName);
    _emailController = TextEditingController(text: auth.userEmail);
    _mobileController = TextEditingController(text: auth.userMobile);
    _pinController = TextEditingController();
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _mobileController.dispose();
    _pinController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    showModalBottomSheet(
      context: context,
      backgroundColor: const Color(0xFFF2F5F0),
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (context) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Select Profile Photo',
                style: GoogleFonts.outfit(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF191D19),
                ),
              ),
              const SizedBox(height: 24),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _buildSourceButton(
                    icon: Icons.camera_alt_rounded,
                    label: 'Camera',
                    source: ImageSource.camera,
                  ),
                  _buildSourceButton(
                    icon: Icons.photo_library_rounded,
                    label: 'Gallery',
                    source: ImageSource.gallery,
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSourceButton({
    required IconData icon,
    required String label,
    required ImageSource source,
  }) {
    return GestureDetector(
      onTap: () async {
        Navigator.pop(context);
        try {
          final pickedFile = await _picker.pickImage(
            source: source,
            maxWidth: 1000,
            maxHeight: 1000,
            imageQuality: 85,
          );
          if (pickedFile != null) {
            setState(() {
              _imageFile = File(pickedFile.path);
            });
          }
        } catch (e) {
          debugPrint('Error picking image: $e');
        }
      },
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFF2E3E2A).withOpacity(0.08), width: 1.5),
            ),
            child: Icon(icon, color: const Color(0xFF2E3E2A), size: 30),
          ),
          const SizedBox(height: 8),
          Text(label, style: GoogleFonts.outfit(fontWeight: FontWeight.bold, color: const Color(0xFF191D19))),
        ],
      ),
    );
  }

  Future<void> _saveProfile() async {
    if (!_formKey.currentState!.validate()) return;

    final auth = Provider.of<AuthProvider>(context, listen: false);
    final success = await auth.updateProfile(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      mobileNumber: _mobileController.text.trim(),
      pin: _pinController.text.isNotEmpty ? _pinController.text.trim() : null,
      photoPath: _imageFile?.path,
    );

    if (mounted) {
      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Profile updated successfully!'), backgroundColor: Colors.green),
        );
        Navigator.pop(context);
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(auth.error ?? 'Failed to update profile'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);

    ImageProvider avatarImage;
    if (_imageFile != null) {
      avatarImage = FileImage(_imageFile!);
    } else if (auth.profilePhotoUrl != null && auth.profilePhotoUrl!.isNotEmpty) {
      avatarImage = NetworkImage(auth.profilePhotoUrl!);
    } else {
      avatarImage = NetworkImage('https://ui-avatars.com/api/?name=${Uri.encodeComponent(auth.userName ?? "User")}&background=2E3E2A&color=fff');
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF2F5F0),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Color(0xFF191D19)),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Edit Profile',
          style: GoogleFonts.outfit(
            fontWeight: FontWeight.bold,
            color: const Color(0xFF191D19),
          ),
        ),
      ),
      body: auth.isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF2E3E2A)))
          : Form(
              key: _formKey,
              child: ListView(
                padding: const EdgeInsets.all(24),
                children: [
                  // Photo Selection Area
                  Center(
                    child: Stack(
                      children: [
                        Container(
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            border: Border.all(color: const Color(0xFFFFE8B6), width: 4),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFF2E3E2A).withOpacity(0.1),
                                blurRadius: 15,
                                offset: const Offset(0, 8),
                              ),
                            ],
                          ),
                          child: CircleAvatar(
                            radius: 60,
                            backgroundColor: const Color(0xFF2E3E2A),
                            backgroundImage: avatarImage,
                          ),
                        ),
                        Positioned(
                          bottom: 0,
                          right: 0,
                          child: GestureDetector(
                            onTap: _pickImage,
                            child: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: const BoxDecoration(
                                color: Color(0xFF2E3E2A),
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.camera_alt_rounded,
                                color: Color(0xFFFFE8B6),
                                size: 20,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 32),

                  // Fields Container
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: const Color(0xFF2E3E2A).withOpacity(0.08), width: 1.5),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildInputField(
                          label: 'Full Name',
                          controller: _nameController,
                          validator: (value) => value == null || value.trim().isEmpty ? 'Name is required' : null,
                          icon: Icons.person_outline_rounded,
                        ),
                        const SizedBox(height: 16),
                        _buildInputField(
                          label: 'Email Address',
                          controller: _emailController,
                          keyboardType: TextInputType.emailAddress,
                          validator: (value) {
                            if (value != null && value.trim().isNotEmpty) {
                              final emailRegex = RegExp(r'^[^@]+@[^@]+\.[^@]+$');
                              if (!emailRegex.hasMatch(value.trim())) {
                                return 'Please enter a valid email';
                              }
                            }
                            return null;
                          },
                          icon: Icons.email_outlined,
                        ),
                        const SizedBox(height: 16),
                        _buildInputField(
                          label: 'Mobile Number',
                          controller: _mobileController,
                          keyboardType: TextInputType.phone,
                          validator: (value) => value == null || value.trim().isEmpty ? 'Mobile number is required' : null,
                          icon: Icons.phone_outlined,
                        ),
                        const SizedBox(height: 16),
                        _buildInputField(
                          label: 'New PIN (4 digits)',
                          controller: _pinController,
                          keyboardType: TextInputType.number,
                          obscureText: true,
                          maxLength: 4,
                          validator: (value) {
                            if (value != null && value.isNotEmpty) {
                              if (value.length != 4 || int.tryParse(value) == null) {
                                return 'PIN must be a 4-digit number';
                              }
                            }
                            return null;
                          },
                          icon: Icons.lock_outline_rounded,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 32),

                  // Save Button
                  ElevatedButton(
                    onPressed: _saveProfile,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2E3E2A),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                      elevation: 4,
                      shadowColor: const Color(0xFF2E3E2A).withOpacity(0.3),
                    ),
                    child: Text(
                      'Save Changes',
                      style: GoogleFonts.outfit(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildInputField({
    required String label,
    required TextEditingController controller,
    String? Function(String?)? validator,
    TextInputType keyboardType = TextInputType.text,
    bool obscureText = false,
    int? maxLength,
    required IconData icon,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: GoogleFonts.outfit(
            fontSize: 13,
            fontWeight: FontWeight.bold,
            color: const Color(0xFF5A7251),
          ),
        ),
        const SizedBox(height: 6),
        TextFormField(
          controller: controller,
          validator: validator,
          keyboardType: keyboardType,
          obscureText: obscureText,
          maxLength: maxLength,
          style: GoogleFonts.outfit(fontSize: 15, color: const Color(0xFF191D19), fontWeight: FontWeight.w600),
          buildCounter: (context, {required currentLength, required isFocused, maxLength}) => null,
          decoration: InputDecoration(
            prefixIcon: Icon(icon, color: const Color(0xFF5A7251), size: 20),
            filled: true,
            fillColor: const Color(0xFFF2F5F0).withOpacity(0.3),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.08)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: BorderSide(color: const Color(0xFF2E3E2A).withOpacity(0.05)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(14),
              borderSide: const BorderSide(color: Color(0xFF2E3E2A), width: 1.5),
            ),
            errorStyle: GoogleFonts.outfit(fontSize: 11),
          ),
        ),
      ],
    );
  }
}
