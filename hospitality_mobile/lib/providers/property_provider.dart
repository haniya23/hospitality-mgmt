import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class PropertyProvider with ChangeNotifier {
  bool _isLoading = false;
  List<dynamic> _properties = [];
  String? _error;

  bool get isLoading => _isLoading;
  List<dynamic> get properties => _properties;
  String? get error => _error;

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
        final jsonData = jsonDecode(response.body);
        _error = jsonData['message'] ?? 'Failed to upload photo';
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
        final jsonData = jsonDecode(response.body);
        _error = jsonData['message'] ?? 'Failed to upload photo';
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
}
}
