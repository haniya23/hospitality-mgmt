import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class GuestProvider with ChangeNotifier {
  bool _isLoading = false;
  List<dynamic> _guests = [];
  String? _error;

  bool get isLoading => _isLoading;
  List<dynamic> get guests => _guests;
  String? get error => _error;

  int _currentPage = 1;
  bool _hasMore = true;
  bool _isMoreLoading = false;

  bool get isMoreLoading => _isMoreLoading;
  bool get hasMore => _hasMore;

  Future<void> fetchGuests({String search = '', bool isRefresh = false}) async {
    if (isRefresh) {
      _isLoading = true;
      _currentPage = 1;
      _guests = [];
      _hasMore = true;
      notifyListeners();
    } else {
      if (!_hasMore || _isMoreLoading) return;
      _isMoreLoading = true;
      notifyListeners();
    }

    _error = null;

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      String url = '${ApiConfig.guestsEndpoint}?page=$_currentPage';
      if (search.isNotEmpty) {
        url += '&search=$search';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          final List newGuests = jsonData['data']['data'];
          final meta = jsonData['data'];
          
          if (isRefresh) {
            _guests = newGuests;
          } else {
            _guests.addAll(newGuests);
          }

          _currentPage++;
          _hasMore = meta['current_page'] < meta['last_page'];
        } else {
          _error = jsonData['message'];
        }
      } else {
        _error = 'Failed to load guests';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      _isMoreLoading = false;
      notifyListeners();
    }
  }

  Future<dynamic> createGuest(Map<String, dynamic> data) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse(ApiConfig.guestsEndpoint),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        return jsonData['data'];
      }
      return null;
    } catch (e) {
      _error = e.toString();
      return null;
    }
  }

  Future<bool> updateGuest(int id, Map<String, dynamic> data) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.put(
        Uri.parse('${ApiConfig.guestsEndpoint}/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        return true;
      } else {
         final jsonData = jsonDecode(response.body);
         _error = jsonData['message'] ?? 'Failed to update';
         return false;
      }
    } catch (e) {
      _error = e.toString();
      return false;
    }
  }
}
