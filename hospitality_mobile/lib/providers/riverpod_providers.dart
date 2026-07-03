import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'auth_provider.dart';
import 'dashboard_provider.dart';
import 'booking_provider.dart';
import 'property_provider.dart';
import 'b2b_provider.dart';
import 'guest_provider.dart';
import 'finance_provider.dart';
import 'expense_provider.dart';

final authProvider = ChangeNotifierProvider<AuthProvider>((ref) {
  return AuthProvider()..initAuth();
});

final dashboardProvider = ChangeNotifierProvider<DashboardProvider>((ref) {
  return DashboardProvider();
});

final bookingProvider = ChangeNotifierProvider<BookingProvider>((ref) {
  return BookingProvider();
});

final propertyProvider = ChangeNotifierProvider<PropertyProvider>((ref) {
  return PropertyProvider();
});

final b2bProvider = ChangeNotifierProvider<B2bProvider>((ref) {
  return B2bProvider();
});

final guestProvider = ChangeNotifierProvider<GuestProvider>((ref) {
  return GuestProvider();
});

final financeProvider = ChangeNotifierProvider<FinanceProvider>((ref) {
  return FinanceProvider();
});

final expenseProvider = ChangeNotifierProvider<ExpenseProvider>((ref) {
  return ExpenseProvider();
});
