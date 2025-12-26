import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/api_config.dart';

class BookingProvider with ChangeNotifier {
  bool _isLoading = false;
  List<dynamic> _bookings = [];
  Map<String, int> _counts = {'all': 0, 'pending': 0, 'confirmed': 0, 'cancelled': 0};
  String? _error;
  String _currentStatus = 'all';

  bool get isLoading => _isLoading;
  List<dynamic> get bookings => _bookings;
  Map<String, int> get counts => _counts;
  String? get error => _error;
  String get currentStatus => _currentStatus;
  List<dynamic> get checkIns => _checkIns;
  List<dynamic> get checkOuts => _checkOuts;

  List<dynamic> _checkIns = [];
  List<dynamic> _checkOuts = [];

  Future<void> fetchCheckIns(String date, {bool showAll = false}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      // Check-ins -> Confirmed bookings arriving on this date or later
      String url = '${ApiConfig.bookingsEndpoint}?status=confirmed';
      if (showAll) {
        url += '&check_in_date_from=$date';
      } else {
        url += '&check_in_date=$date';
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
          _checkIns = jsonData['data']['data'];
        } else {
          _error = jsonData['message'];
        }
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchCheckOuts(String date, {bool showAll = false}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      // Check-outs -> Checked-in bookings departing on this date or later
      String url = '${ApiConfig.bookingsEndpoint}?status=checked_in';
      if (showAll) {
        url += '&check_out_date_from=$date';
      } else {
        url += '&check_out_date=$date';
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
          _checkOuts = jsonData['data']['data'];
        } else {
          _error = jsonData['message'];
        }
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchBookings({String status = 'all'}) async {
    _isLoading = true;
    _currentStatus = status;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final url = '${ApiConfig.bookingsEndpoint}?status=$status';

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
          _bookings = jsonData['data']['data']; 
          fetchCounts(); // Refresh counts
        } else {
          _error = jsonData['message'] ?? 'Failed to load bookings';
        }
      } else {
        _error = 'Failed to load bookings';
      }
    } catch (e) {
      _error = 'Connection error: $e';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchCounts() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.get(
        Uri.parse(ApiConfig.bookingCountsEndpoint),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        if (jsonData['success'] == true) {
          _counts = Map<String, int>.from(jsonData['data']);
          notifyListeners();
        }
      }
    } catch (e) {
      print('Error fetching counts: $e');
    }
  }

  Future<bool> createBooking(Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.post(
        Uri.parse(ApiConfig.bookingsEndpoint),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        await fetchBookings(status: 'all');
        return true;
      } else {
        final errorData = jsonDecode(response.body);
        _error = errorData['message'] ?? 'Failed to create booking';
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
  Future<bool> updateBookingStatus(int id, String status, {String? reason}) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final url = '${ApiConfig.bookingsEndpoint}/$id/status';

      final response = await http.patch(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'status': status,
          if (reason != null) 'reason': reason,
        }),
      );

      if (response.statusCode == 200) {
        await fetchBookings(status: _currentStatus);
        await fetchCounts();
        return true;
      } else {
         final errorData = jsonDecode(response.body);
        _error = errorData['message'] ?? 'Failed to update status';
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

  Future<String?> getInvoiceUrl(int bookingId) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    // Note: Append token to query if PDF download needs auth in browser
    return '${ApiConfig.bookingsEndpoint}/$bookingId/invoice?token=$token'; 
  }

  Future<bool> editBooking(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final response = await http.put(
        Uri.parse('${ApiConfig.bookingsEndpoint}/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        await fetchBookings(status: 'all');
        await fetchCounts();
        return true;
      } else {
        final errorData = jsonDecode(response.body);
        _error = errorData['message'] ?? 'Failed to update booking';
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

  Future<bool> performCheckIn(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final url = '${ApiConfig.bookingsEndpoint}/$id/check-in';

      final response = await http.post(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        await fetchBookings(status: 'all');
        await fetchCounts();
        return true;
      } else {
        final errorData = jsonDecode(response.body);
        _error = errorData['message'] ?? 'Failed to check in guest';
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

  Future<bool> performCheckOut(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    notifyListeners();
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final url = '${ApiConfig.bookingsEndpoint}/$id/check-out';

      final response = await http.post(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        await fetchBookings(status: 'all');
        await fetchCounts();
        return true;
      } else {
        final errorData = jsonDecode(response.body);
        _error = errorData['message'] ?? 'Failed to check out guest';
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
