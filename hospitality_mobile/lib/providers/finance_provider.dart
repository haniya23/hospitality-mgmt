import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class FinanceProvider with ChangeNotifier {
  bool _isLoading = false;
  Map<String, dynamic>? _financeData;
  String? _error;
  
  // Filters
  String _selectedPropertyId = 'all';
  DateTime? _startDate;
  DateTime? _endDate;

  bool get isLoading => _isLoading;
  Map<String, dynamic>? get financeData => _financeData;
  String? get error => _error;
  
  String get selectedPropertyId => _selectedPropertyId;
  DateTime? get startDate => _startDate;
  DateTime? get endDate => _endDate;

  double get totalRevenue => _financeData?['total_revenue']?.toDouble() ?? 0.0;
  double get pendingReceivables => _financeData?['pending_receivables']?.toDouble() ?? 0.0;
  List<dynamic> get transactions => _financeData?['transactions']?['data'] ?? [];
  List<dynamic> get properties => _financeData?['properties'] ?? [];

  void setPropertyFilter(String propertyId) {
    _selectedPropertyId = propertyId;
    fetchFinanceData();
  }

  void setDateFilter(DateTime? start, DateTime? end) {
    _startDate = start;
    _endDate = end;
    fetchFinanceData();
  }

  void clearFilters() {
    _selectedPropertyId = 'all';
    _startDate = null;
    _endDate = null;
    fetchFinanceData();
  }

  Future<void> fetchFinanceData() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      
      final queryParams = <String, String>{};
      if (_selectedPropertyId != 'all') {
        queryParams['property_id'] = _selectedPropertyId;
      }
      if (_startDate != null) {
        queryParams['start_date'] = _startDate!.toIso8601String().split('T')[0];
      }
      if (_endDate != null) {
        queryParams['end_date'] = _endDate!.toIso8601String().split('T')[0];
      }

      final uri = Uri.parse('${ApiConfig.baseUrl}/owner/finance').replace(queryParameters: queryParams);
      final response = await http.get(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          _financeData = jsonData['data'];
        } else {
          _error = jsonData['message'] ?? 'Failed to load finance data';
        }
      } else {
        _error = 'Error ${response.statusCode}: Failed to fetch finance';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> createIncomeRecord(Map<String, dynamic> data) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/owner/finance'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          await fetchFinanceData();
          return true;
        } else {
          _error = jsonData['message'] ?? 'Failed to save transaction';
        }
      } else {
        final jsonData = jsonDecode(response.body);
        _error = jsonData['message'] ?? 'Error ${response.statusCode}: Failed to save';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateIncomeRecord(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/owner/finance/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          await fetchFinanceData();
          return true;
        } else {
          _error = jsonData['message'] ?? 'Failed to update transaction';
        }
      } else {
        final jsonData = jsonDecode(response.body);
        _error = jsonData['message'] ?? 'Error ${response.statusCode}: Failed to update';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteIncomeRecord(int id) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/owner/finance/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          await fetchFinanceData();
          return true;
        } else {
          _error = jsonData['message'] ?? 'Failed to delete transaction';
        }
      } else {
        _error = 'Error ${response.statusCode}: Failed to delete';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }
}
