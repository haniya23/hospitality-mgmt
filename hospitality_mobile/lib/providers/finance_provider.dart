import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class FinanceProvider with ChangeNotifier {
  bool _isLoading = false;
  bool _isSummaryLoading = false;

  Map<String, dynamic>? _financeData; // Transaction list + KPIs
  Map<String, dynamic>? _summaryData; // Dashboard summary (period-based)

  String? _error;

  // Filters for transaction list
  String _selectedPropertyId = 'all';
  DateTime? _startDate;
  DateTime? _endDate;
  int _currentPage = 1;

  // Period for summary dashboard
  String _period = 'month'; // day | week | month

  // -------------------------------------------------------
  // GETTERS
  // -------------------------------------------------------
  bool get isLoading => _isLoading;
  bool get isSummaryLoading => _isSummaryLoading;
  Map<String, dynamic>? get financeData => _financeData;
  Map<String, dynamic>? get summaryData => _summaryData;
  String? get error => _error;
  String get selectedPropertyId => _selectedPropertyId;
  DateTime? get startDate => _startDate;
  DateTime? get endDate => _endDate;
  String get period => _period;
  int get currentPage => _currentPage;

  // Transaction list KPIs
  double get totalRevenue => _financeData?['total_revenue']?.toDouble() ?? 0.0;
  double get totalExpenses =>
      _financeData?['total_expenses']?.toDouble() ?? 0.0;
  double get netProfit => _financeData?['net_profit']?.toDouble() ?? 0.0;
  double get profitMargin => _financeData?['profit_margin']?.toDouble() ?? 0.0;
  double get pendingReceivables =>
      _financeData?['pending_receivables']?.toDouble() ?? 0.0;
  List<dynamic> get transactions =>
      _financeData?['transactions']?['data'] ?? [];
  List<dynamic> get properties => _financeData?['properties'] ?? [];
  int get lastPage => _financeData?['transactions']?['last_page'] ?? 1;
  int get totalTransactions => _financeData?['transactions']?['total'] ?? 0;
  int get fromTransaction => _financeData?['transactions']?['from'] ?? 0;
  int get toTransaction => _financeData?['transactions']?['to'] ?? 0;
  bool get hasNextPage => currentPage < lastPage;
  bool get hasPreviousPage => currentPage > 1;

  // Summary dashboard data
  double get summaryRevenue =>
      _summaryData?['total_revenue']?.toDouble() ?? 0.0;
  double get summaryExpenses =>
      _summaryData?['total_expenses']?.toDouble() ?? 0.0;
  double get summaryNetProfit => _summaryData?['net_profit']?.toDouble() ?? 0.0;
  double get summaryProfitMargin =>
      _summaryData?['profit_margin']?.toDouble() ?? 0.0;
  String get periodLabel => _summaryData?['period_label'] ?? '';
  List<dynamic> get incomeByType => _summaryData?['income_by_type'] ?? [];
  List<dynamic> get accommodationPerformance =>
      _summaryData?['accommodation_performance'] ?? [];
  List<dynamic> get recentIncome => _summaryData?['recent_income'] ?? [];
  List<dynamic> get recentExpenses => _summaryData?['recent_expenses'] ?? [];
  List<dynamic> get summaryProperties => _summaryData?['properties'] ?? [];

  // -------------------------------------------------------
  // FILTERS
  // -------------------------------------------------------
  void setPropertyFilter(String propertyId) {
    _selectedPropertyId = propertyId;
    _currentPage = 1;
    fetchFinanceData();
  }

  void setDateFilter(DateTime? start, DateTime? end) {
    _startDate = start;
    _endDate = end;
    _currentPage = 1;
    fetchFinanceData();
  }

  void clearFilters() {
    _selectedPropertyId = 'all';
    _startDate = null;
    _endDate = null;
    _currentPage = 1;
    fetchFinanceData();
  }

  void setPeriod(String p) {
    _period = p;
    fetchSummary();
  }

  void setSummaryProperty(String pid) {
    _selectedPropertyId = pid;
    fetchSummary();
  }

  // -------------------------------------------------------
  // FETCH TRANSACTION LIST (Income CRUD page)
  // -------------------------------------------------------
  Future<void> fetchFinanceData({int? page}) async {
    _isLoading = true;
    _error = null;
    _currentPage = page ?? _currentPage;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final params = <String, String>{'page': _currentPage.toString()};
      if (_selectedPropertyId != 'all')
        params['property_id'] = _selectedPropertyId;
      if (_startDate != null)
        params['start_date'] = _startDate!.toIso8601String().split('T')[0];
      if (_endDate != null)
        params['end_date'] = _endDate!.toIso8601String().split('T')[0];

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/owner/finance',
      ).replace(queryParameters: params);
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
        _error = 'Error ${response.statusCode}';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> goToPage(int page) async {
    if (page < 1 || page == _currentPage) return;
    await fetchFinanceData(page: page);
  }

  Future<void> nextPage() async {
    if (!hasNextPage) return;
    await goToPage(_currentPage + 1);
  }

  Future<void> previousPage() async {
    if (!hasPreviousPage) return;
    await goToPage(_currentPage - 1);
  }

  // -------------------------------------------------------
  // FETCH SUMMARY (Dashboard page)
  // -------------------------------------------------------
  Future<void> fetchSummary() async {
    _isSummaryLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final params = <String, String>{'period': _period};
      if (_selectedPropertyId != 'all')
        params['property_id'] = _selectedPropertyId;

      final uri = Uri.parse(
        '${ApiConfig.baseUrl}/owner/finance/summary',
      ).replace(queryParameters: params);
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
          _summaryData = jsonData['data'];
        } else {
          _error = jsonData['message'] ?? 'Failed to load summary';
        }
      } else {
        _error = 'Error ${response.statusCode}';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isSummaryLoading = false;
      notifyListeners();
    }
  }

  // -------------------------------------------------------
  // INCOME CRUD
  // -------------------------------------------------------
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
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          await fetchFinanceData();
          await fetchSummary();
          return true;
        } else {
          _error = json['message'] ?? 'Failed to save';
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
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          await fetchFinanceData();
          await fetchSummary();
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
        final json = jsonDecode(response.body);
        if (json['success'] == true) {
          await fetchFinanceData();
          await fetchSummary();
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
