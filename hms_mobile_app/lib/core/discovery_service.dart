import 'dart:io';
import 'package:http/http.dart' as http;
import 'dart:convert';

class DiscoveryService {
  static Future<List<String>> getSubnets() async {
    List<String> subnets = [];
    try {
      final interfaces = await NetworkInterface.list(
        includeLinkLocal: false,
        type: InternetAddressType.IPv4,
      );
      for (var interface in interfaces) {
        for (var address in interface.addresses) {
          final ip = address.address;
          final parts = ip.split('.');
          if (parts.length == 4) {
            if (parts[0] == '127') continue;
            subnets.add('${parts[0]}.${parts[1]}.${parts[2]}');
          }
        }
      }
    } catch (e) {
      print("Error getting subnets: $e");
    }
    // Common subnets and emulator fallback
    if (subnets.isEmpty) {
      subnets.addAll(['192.168.1', '192.168.0', '192.168.8', '192.168.10', '192.168.137']);
    }
    return subnets.toSet().toList();
  }

  static Future<Map<String, dynamic>?> discoverLocalServer() async {
    final subnets = await getSubnets();
    final List<String> candidateUrls = [];
    
    candidateUrls.add("http://10.0.2.2:8888"); // Emulator host loopback IP
    
    for (var subnet in subnets) {
      for (int i = 1; i <= 254; i++) {
        candidateUrls.add("http://$subnet.$i:8888");
      }
    }

    const int batchSize = 40;
    for (int i = 0; i < candidateUrls.length; i += batchSize) {
      final end = i + batchSize > candidateUrls.length ? candidateUrls.length : i + batchSize;
      final batch = candidateUrls.sublist(i, end);
      
      final futures = batch.map((url) async {
        try {
          final response = await http.get(
            Uri.parse('$url/api/discover'),
          ).timeout(const Duration(milliseconds: 900));
          
          if (response.statusCode == 200) {
            final data = json.decode(response.body);
            if (data['status'] == 'success' && data['app'] == 'aadhira_erp') {
              return {
                'url': url,
                'business_name': data['business_name'] ?? 'Aadhira ERP',
                'client_id': data['client_id'] ?? '2',
                'client_secret': data['client_secret'] ?? '',
              };
            }
          }
        } catch (_) {}
        return null;
      });

      final results = await Future.wait(futures);
      for (var result in results) {
        if (result != null) {
          return result;
        }
      }
    }
    return null;
  }
}
