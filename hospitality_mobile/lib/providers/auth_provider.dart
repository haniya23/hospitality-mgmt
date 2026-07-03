import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class AuthProvider with ChangeNotifier {
  bool _isAuthenticated = false;
  String? _token;
  String? _userName;
  String? _userEmail;
  String? _userMobile;
  String? _profilePhotoUrl;
  bool _isLoading = false;
  String? _error;

  bool get isAuthenticated => _isAuthenticated;
  String? get token => _token;
  String? get userName => _userName;
  String? get userEmail => _userEmail;
  String? get userMobile => _userMobile;
  String? get profilePhotoUrl => _profilePhotoUrl;
  bool get isLoading => _isLoading;
  String? get error => _error;

  // Initialize auth state from shared preferences
  Future<void> initAuth() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    _userName = prefs.getString('user_name');
    _userEmail = prefs.getString('user_email');
    _userMobile = prefs.getString('user_mobile');
    _profilePhotoUrl = prefs.getString('profile_photo_url');
    
    if (_token != null) {
      _isAuthenticated = true;
      // Fetch fresh profile in the background
      fetchProfile();
    }
    notifyListeners();
  }

  Future<void> fetchProfile() async {
    if (_token == null) return;
    try {
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/owner/profile'),
        headers: {
          'Authorization': 'Bearer $_token',
          'Accept': 'application/json',
        },
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final user = data['data'];
          _userName = user['name'];
          _userEmail = user['email'];
          _userMobile = user['mobile_number'];
          _profilePhotoUrl = user['profile_photo_url'];
          
          final prefs = await SharedPreferences.getInstance();
          await prefs.setString('user_name', _userName!);
          if (_userEmail != null) await prefs.setString('user_email', _userEmail!);
          if (_userMobile != null) await prefs.setString('user_mobile', _userMobile!);
          if (_profilePhotoUrl != null) await prefs.setString('profile_photo_url', _profilePhotoUrl!);
          
          notifyListeners();
        }
      }
    } catch (e) {
      debugPrint('Error fetching profile: $e');
    }
  }

  Future<bool> login(String mobileNumber, String pin) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await http.post(
        Uri.parse(ApiConfig.loginEndpoint),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'mobile_number': mobileNumber,
          'pin': pin,
          'device_name': 'flutter_app',
        }),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        _token = data['data']['token'];
        _userName = data['data']['user']['name'];
        _userEmail = data['data']['user']['email'];
        _userMobile = data['data']['user']['mobile_number'];
        _profilePhotoUrl = data['data']['user']['profile_photo_url'];
        _isAuthenticated = true;

        // Save to preferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);
        await prefs.setString('user_name', _userName!);
        if (_userEmail != null) await prefs.setString('user_email', _userEmail!);
        if (_userMobile != null) await prefs.setString('user_mobile', _userMobile!);
        if (_profilePhotoUrl != null) await prefs.setString('profile_photo_url', _profilePhotoUrl!);

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _error = data['message'] ?? 'Login failed';
        if (data['errors'] != null) {
          _error = data['errors'].values.first[0];
        }
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateProfile({
    required String name,
    required String email,
    required String mobileNumber,
    String? pin,
    String? photoPath,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiConfig.baseUrl}/owner/profile'),
      );

      request.headers.addAll({
        'Authorization': 'Bearer $_token',
        'Accept': 'application/json',
      });

      request.fields['name'] = name;
      request.fields['email'] = email;
      request.fields['mobile_number'] = mobileNumber;
      if (pin != null && pin.isNotEmpty) {
        request.fields['pin'] = pin;
      }

      if (photoPath != null) {
        request.files.add(await http.MultipartFile.fromPath(
          'profile_photo',
          photoPath,
        ));
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        final user = data['data'];
        _userName = user['name'];
        _userEmail = user['email'];
        _userMobile = user['mobile_number'];
        _profilePhotoUrl = user['profile_photo_url'];

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('user_name', _userName!);
        if (_userEmail != null) await prefs.setString('user_email', _userEmail!);
        if (_userMobile != null) await prefs.setString('user_mobile', _userMobile!);
        if (_profilePhotoUrl != null) await prefs.setString('profile_photo_url', _profilePhotoUrl!);

        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _error = data['message'] ?? 'Failed to update profile';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    _token = null;
    _userName = null;
    _userEmail = null;
    _userMobile = null;
    _profilePhotoUrl = null;
    _isAuthenticated = false;

    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('user_name');
    await prefs.remove('user_email');
    await prefs.remove('user_mobile');
    await prefs.remove('profile_photo_url');
    
    notifyListeners();
  }
}
