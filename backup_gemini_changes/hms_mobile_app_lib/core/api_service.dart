import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  String? _baseUrl;
  String? _accessToken;
  String? _clientId;
  String? _clientSecret;

  String? get baseUrl => _baseUrl;
  bool get isAuthenticated => _accessToken != null;

  void configure(String url, String clientId, String clientSecret) {
    _baseUrl = url.endsWith('/') ? url.substring(0, url.length - 1) : url;
    _clientId = clientId;
    _clientSecret = clientSecret;
  }

  Future<bool> login(String username, String password) async {
    if (_baseUrl == null || _clientId == null || _clientSecret == null) {
      throw Exception("API Client not configured. Run configure() first.");
    }

    try {
      final response = await http.post(
        Uri.parse('$_baseUrl/oauth/token'),
        headers: {'Accept': 'application/json'},
        body: {
          'grant_type': 'password',
          'client_id': _clientId,
          'client_secret': _clientSecret,
          'username': username,
          'password': password,
          'scope': '',
        },
      ).timeout(const Duration(seconds: 15));

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        _accessToken = data['access_token'];
        return true;
      }
      return false;
    } catch (e) {
      print("Login error: $e");
      return false;
    }
  }

  Future<Map<String, String>> _headers() async {
    return {
      'Authorization': 'Bearer $_accessToken',
      'Accept': 'application/json',
    };
  }

  Future<List<dynamic>> fetchSales() async {
    if (_baseUrl == null || _accessToken == null) throw Exception("Unauthorized");
    
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/connector/api/sell'),
        headers: await _headers(),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        return result['data'] ?? [];
      }
      throw Exception("Failed to load sales: ${response.statusCode}");
    } catch (e) {
      print("Fetch sales error: $e");
      return [];
    }
  }

  Future<List<dynamic>> fetchBookings() async {
    if (_baseUrl == null || _accessToken == null) throw Exception("Unauthorized");

    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/connector/api/booking'),
        headers: await _headers(),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        return result['data'] ?? [];
      }
      throw Exception("Failed to load bookings: ${response.statusCode}");
    } catch (e) {
      print("Fetch bookings error: $e");
      return [];
    }
  }

  Future<List<dynamic>> fetchRooms() async {
    if (_baseUrl == null || _accessToken == null) throw Exception("Unauthorized");

    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/connector/api/room'),
        headers: await _headers(),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        return result['data'] ?? [];
      }
      throw Exception("Failed to load rooms: ${response.statusCode}");
    } catch (e) {
      print("Fetch rooms error: $e");
      return [];
    }
  }

  Future<Map<String, dynamic>?> fetchProfitLoss() async {
    if (_baseUrl == null || _accessToken == null) throw Exception("Unauthorized");

    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/connector/api/profit-loss-report'),
        headers: await _headers(),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        return result['data'] ?? {};
      }
      return null;
    } catch (e) {
      print("Fetch P&L error: $e");
      return null;
    }
  }

  Future<Map<String, dynamic>?> fetchUserProfile() async {
    if (_baseUrl == null || _accessToken == null) throw Exception("Unauthorized");

    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/connector/api/user/loggedin'),
        headers: await _headers(),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        return result['data'] ?? {};
      }
      return null;
    } catch (e) {
      print("Fetch profile error: $e");
      return null;
    }
  }

  Future<List<dynamic>> fetchExpenses() async {
    if (_baseUrl == null || _accessToken == null) throw Exception("Unauthorized");
    
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/connector/api/expense'),
        headers: await _headers(),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        return result['data'] ?? [];
      }
      throw Exception("Failed to load expenses: ${response.statusCode}");
    } catch (e) {
      print("Fetch expenses error: $e");
      return [];
    }
  }

  Future<Map<String, dynamic>?> fetchBusinessDetails() async {
    if (_baseUrl == null || _accessToken == null) throw Exception("Unauthorized");

    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/connector/api/business-details'),
        headers: await _headers(),
      );

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        return result['data'] ?? {};
      }
      return null;
    } catch (e) {
      print("Fetch business details error: $e");
      return null;
    }
  }

  void logout() {
    _accessToken = null;
  }
}
