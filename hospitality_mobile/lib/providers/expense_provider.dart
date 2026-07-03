import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class ExpenseProvider with ChangeNotifier {
  bool _isLoading = false;
  Map<String, dynamic>? _expenseData;
  List<dynamic> _categories = [];
  String? _error;

  // Filters
  String _selectedPropertyId = 'all';
  String? _selectedCategoryId;
  DateTime? _startDate;
  DateTime? _endDate;

  bool get isLoading => _isLoading;
  List<dynamic> get expenses => (_expenseData?['data'] as List?) ?? [];
  List<dynamic> get categories => _categories;
  String? get error => _error;
  String get selectedPropertyId => _selectedPropertyId;
  String? get selectedCategoryId => _selectedCategoryId;
  DateTime? get startDate => _startDate;
  DateTime? get endDate => _endDate;

  bool get hasActiveFilters =>
      _selectedPropertyId != 'all' ||
      _selectedCategoryId != null ||
      _startDate != null;

  // -------------------------------------------------------
  // FILTERS
  // -------------------------------------------------------
  void setPropertyFilter(String v) {
    _selectedPropertyId = v;
    fetchExpenses();
  }

  void setCategoryFilter(String? v) {
    _selectedCategoryId = v;
    fetchExpenses();
  }

  void setDateFilter(DateTime? start, DateTime? end) {
    _startDate = start;
    _endDate = end;
    fetchExpenses();
  }

  void clearFilters() {
    _selectedPropertyId = 'all';
    _selectedCategoryId = null;
    _startDate = null;
    _endDate = null;
    fetchExpenses();
  }

  // -------------------------------------------------------
  // FETCH
  // -------------------------------------------------------
  Future<void> fetchExpenses() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final params = <String, String>{};
      if (_selectedPropertyId != 'all') params['property_id'] = _selectedPropertyId;
      if (_selectedCategoryId != null) params['category_id'] = _selectedCategoryId!;
      if (_startDate != null) params['start_date'] = _startDate!.toIso8601String().split('T')[0];
      if (_endDate != null) params['end_date'] = _endDate!.toIso8601String().split('T')[0];

      final uri = Uri.parse('${ApiConfig.baseUrl}/owner/expenses').replace(queryParameters: params);
      final response = await http.get(uri, headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });

      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          _expenseData = json['data'];
        } else {
          _error = json['message'] ?? 'Failed to load expenses';
        }
      } else {
        _error = 'Error ${response.statusCode}';
      }
    } catch (e) {
      _error = 'Connection error: $e';
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
        Uri.parse('${ApiConfig.baseUrl}/owner/expense-categories'),
        headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
      );
      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          _categories = json['data'] as List;
          notifyListeners();
        }
      }
    } catch (_) {}
  }

  // -------------------------------------------------------
  // CRUD
  // -------------------------------------------------------
  Future<bool> createExpense(Map<String, dynamic> data) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/owner/expenses'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(data),
      );
      if (response.statusCode == 200 || response.statusCode == 201) {
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          await fetchExpenses();
          return true;
        } else {
          _error = json['message'] ?? 'Failed to create expense';
        }
      } else {
        final json = jsonDecode(response.body);
        _error = json['message'] ?? 'Error ${response.statusCode}';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> updateExpense(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}/owner/expenses/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(data),
      );
      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          await fetchExpenses();
          return true;
        } else {
          _error = json['message'] ?? 'Failed to update';
        }
      } else {
        final json = jsonDecode(response.body);
        _error = json['message'] ?? 'Error ${response.statusCode}';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
    return false;
  }

  Future<bool> deleteExpense(int id) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}/owner/expenses/$id'),
        headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
      );
      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          await fetchExpenses();
          return true;
        } else {
          _error = json['message'] ?? 'Failed to delete';
        }
      } else {
        _error = 'Error ${response.statusCode}';
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
