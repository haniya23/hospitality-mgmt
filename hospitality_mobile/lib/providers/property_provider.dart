import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class PropertyProvider with ChangeNotifier {
  bool _isLoading = false;
  List<dynamic> _properties = [];
  String? _error;

  Map<String, dynamic>? _dashboardData;
  Map<String, dynamic>? get dashboardData => _dashboardData;

  List<dynamic> _categories = [];
  List<dynamic> _predefinedTypes = [];
  List<dynamic> _amenities = [];

  List<dynamic> get categories => _categories;
  List<dynamic> get predefinedTypes => _predefinedTypes;
  List<dynamic> get amenities => _amenities;

  bool get isLoading => _isLoading;
  List<dynamic> get properties => _properties;
  String? get error => _error;

  Future<void> fetchPropertyDashboard(int propertyId) async {
    _isLoading = true;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/owner/properties/$propertyId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          _dashboardData = data['data'];
        } else {
            _error = data['message'] ?? 'Failed to load dashboard data';
        }
      } else {
        _error = 'Failed to load dashboard data: ${response.statusCode}';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchProperties() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.get(
        Uri.parse(ApiConfig.propertiesEndpoint),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          _properties = jsonData['data'];
        } else {
          _error = jsonData['message'] ?? 'Failed to load properties';
        }
      } else {
        _error = 'Failed to load properties';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
  Future<bool> updateProperty(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.put(
        Uri.parse('${ApiConfig.propertiesEndpoint}/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        await fetchProperties();
        return true;
      } else {
        final jsonData = jsonDecode(response.body);
        _error = jsonData['message'] ?? 'Failed to update property';
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> toggleStatus(int id) async {
    // Optimistic update
    final index = _properties.indexWhere((p) => p['id'] == id);
    if (index != -1) {
      final currentStatus = _properties[index]['status'];
      _properties[index]['status'] = currentStatus == 'active' ? 'inactive' : 'active';
      notifyListeners();
    }

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.patch(
        Uri.parse('${ApiConfig.propertiesEndpoint}/$id/status'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        // Success, maybe sync with server data
        return true;
      } else {
        // Revert on failure
        if (index != -1) {
           _fetchPropertiesWithoutLoading(); // Refresh to be safe
        }
        return false;
      }
    } catch (e) {
      if (index != -1) _fetchPropertiesWithoutLoading();
      return false;
    }
  }

  Future<void> _fetchPropertiesWithoutLoading() async {
     try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.get(
        Uri.parse(ApiConfig.propertiesEndpoint),
        headers: { 'Authorization': 'Bearer $token', 'Accept': 'application/json' },
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          _properties = jsonData['data'];
          notifyListeners();
        }
      } 
    } catch (_) {}
  }

  Future<bool> uploadPropertyPhoto(int id, String filePath) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      var request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiConfig.propertiesEndpoint}/$id/photos'),
      );
      request.headers.addAll({
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });
      request.files.add(await http.MultipartFile.fromPath('photos[]', filePath));

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 200) {
        await fetchProperties();
        return true;
      } else {
         try {
          final jsonData = jsonDecode(response.body);
          _error = jsonData['message'] ?? 'Failed to upload photo';
        } catch (e) {
          if (response.statusCode == 413) {
            _error = 'File too large (Server limit)';
          } else {
             _error = 'Server Error: ${response.statusCode}';
          }
        }
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> deletePropertyPhoto(int id, int photoId) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.delete(
        Uri.parse('${ApiConfig.propertiesEndpoint}/$id/photos/$photoId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        await fetchProperties();
        return true;
      } else {
        final jsonData = jsonDecode(response.body);
        _error = jsonData['message'] ?? 'Failed to delete photo';
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> uploadAccommodationPhoto(int propertyId, int accommodationId, String filePath) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      var request = http.MultipartRequest(
        'POST',
        Uri.parse('${ApiConfig.propertiesEndpoint}/$propertyId/accommodations/$accommodationId/photos'),
      );
      request.headers.addAll({
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });
      request.files.add(await http.MultipartFile.fromPath('photos[]', filePath));

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);

      if (response.statusCode == 200) {
        await fetchProperties();
        return true;
      } else {
         try {
          final jsonData = jsonDecode(response.body);
          _error = jsonData['message'] ?? 'Failed to upload photo';
        } catch (e) {
          if (response.statusCode == 413) {
            _error = 'File too large (Server limit)';
          } else {
             _error = 'Server Error: ${response.statusCode}';
          }
        }
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> deleteAccommodationPhoto(int propertyId, int accommodationId, int photoId) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.delete(
        Uri.parse('${ApiConfig.propertiesEndpoint}/$propertyId/accommodations/$accommodationId/photos/$photoId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        await fetchProperties();
        return true;
      } else {
        final jsonData = jsonDecode(response.body);
        _error = jsonData['message'] ?? 'Failed to delete photo';
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchCategories() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/owner/property-categories'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          _categories = data['data'];
          notifyListeners();
        }
      }
    } catch (_) {}
  }

  Future<void> fetchPredefinedTypes() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/owner/predefined-accommodation-types'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          _predefinedTypes = data['data'];
          notifyListeners();
        }
      }
    } catch (_) {}
  }

  Future<void> fetchAmenities() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/owner/amenities'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          _amenities = data['data'];
          notifyListeners();
        }
      }
    } catch (_) {}
  }

  Future<bool> addProperty(String name, int categoryId, String description) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/owner/properties'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'name': name,
          'property_category_id': categoryId,
          'description': description,
        }),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          await fetchProperties();
          return true;
        } else {
          _error = data['message'] ?? 'Failed to create property';
          return false;
        }
      } else {
        final data = jsonDecode(response.body);
        _error = data['message'] ?? 'Server error: ${response.statusCode}';
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> addAccommodation({
    required int propertyId,
    required String customName,
    required int predefinedTypeId,
    required double basePrice,
    required int maxOccupancy,
    required double size,
    required String description,
    required List<int> amenityIds,
  }) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/owner/properties/$propertyId/accommodations'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'custom_name': customName,
          'predefined_type_id': predefinedTypeId,
          'base_price': basePrice,
          'max_occupancy': maxOccupancy,
          'size': size,
          'description': description,
          'amenities': amenityIds,
        }),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          await fetchProperties();
          return true;
        } else {
          _error = data['message'] ?? 'Failed to create accommodation';
          return false;
        }
      } else {
        final data = jsonDecode(response.body);
        _error = data['message'] ?? 'Server error: ${response.statusCode}';
        return false;
      }
    } catch (e) {
      _error = 'Connection error: $e';
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
