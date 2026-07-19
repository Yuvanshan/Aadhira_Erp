import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class AppConfig {
  // ---------------------------------------------------------------------------
  // SERVER CONFIGURATION VARIABLES (DYNAMIC)
  // ---------------------------------------------------------------------------
  
  // Default active values
  static String serverUrl = "http://192.168.8.153:8888";
  static String clientId = "2";
  static String clientSecret = "79GOzjI3O2Iv5F1Kkp8gHMH7CUdZGeULzIDX5WQM";
  static String activeBusinessName = "Primary Business";

  // List of all saved businesses
  static List<Map<String, String>> savedBusinesses = [];

  // Load from local storage
  static Future<void> load() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      serverUrl = prefs.getString('server_url') ?? "http://192.168.8.153:8888";
      clientId = prefs.getString('client_id') ?? "2";
      clientSecret = prefs.getString('client_secret') ?? "79GOzjI3O2Iv5F1Kkp8gHMH7CUdZGeULzIDX5WQM";
      activeBusinessName = prefs.getString('active_business_name') ?? "Primary Business";

      final savedStr = prefs.getString('saved_businesses');
      if (savedStr != null) {
        final list = json.decode(savedStr) as List<dynamic>;
        savedBusinesses = list.map((item) => Map<String, String>.from(item as Map)).toList();
      } else {
        // Seed default initial profile
        savedBusinesses = [
          {
            'name': 'Primary Business',
            'url': 'http://192.168.8.153:8888',
            'client_id': '2',
            'client_secret': '79GOzjI3O2Iv5F1Kkp8gHMH7CUdZGeULzIDX5WQM',
          }
        ];
      }
    } catch (e) {
      print("Error loading configurations: $e");
    }
  }

  // Save/Update a business profile
  static Future<void> saveProfile({
    required String name,
    required String url,
    required String id,
    required String secret,
  }) async {
    final newProfile = {
      'name': name,
      'url': url,
      'client_id': id,
      'client_secret': secret,
    };

    // Filter duplicates by name or URL
    savedBusinesses.removeWhere((item) => item['name'] == name || item['url'] == url);
    savedBusinesses.add(newProfile);

    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('saved_businesses', json.encode(savedBusinesses));
      await activateProfile(newProfile);
    } catch (e) {
      print("Error saving profile: $e");
    }
  }

  // Set a profile as the active one
  static Future<void> activateProfile(Map<String, String> profile) async {
    serverUrl = profile['url'] ?? '';
    clientId = profile['client_id'] ?? '';
    clientSecret = profile['client_secret'] ?? '';
    activeBusinessName = profile['name'] ?? '';

    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('server_url', serverUrl);
      await prefs.setString('client_id', clientId);
      await prefs.setString('client_secret', clientSecret);
      await prefs.setString('active_business_name', activeBusinessName);
    } catch (e) {
      print("Error activating profile: $e");
    }
  }

  // Delete a business profile
  static Future<void> deleteProfile(String name) async {
    savedBusinesses.removeWhere((item) => item['name'] == name);
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('saved_businesses', json.encode(savedBusinesses));
      
      if (savedBusinesses.isNotEmpty) {
        await activateProfile(savedBusinesses.first);
      } else {
        // Fall back to empty state defaults
        activeBusinessName = "No Profile";
        await prefs.setString('active_business_name', activeBusinessName);
      }
    } catch (e) {
      print("Error deleting profile: $e");
    }
  }

  // Save function for backward compatibility
  static Future<void> save({required String url, required String id, required String secret}) async {
    await saveProfile(name: "Discovered Business", url: url, id: id, secret: secret);
  }
}
