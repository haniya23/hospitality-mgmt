class ApiConfig {
  // User specified Valet URL
  static const String baseUrl = 'http://hospitality-mgmt.test/api'; 
  
  static const String loginEndpoint = '$baseUrl/mobile-login';
  static const String dashboardEndpoint = '$baseUrl/owner/dashboard';
  static const String bookingsEndpoint = '$baseUrl/owner/bookings';
  static const String bookingCountsEndpoint = '$baseUrl/owner/bookings/counts';
  static const String propertiesEndpoint = '$baseUrl/owner/properties';
  static const String b2bEndpoint = '$baseUrl/owner/b2b';
  static const String guestsEndpoint = '$baseUrl/owner/guests';
}
