// lib/services/api_service.dart
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../constants.dart';
import '../models/product_model.dart';
import '../models/category_model.dart';

class ApiService {
  ApiService._private();
  static final ApiService _instance = ApiService._private();
  factory ApiService() => _instance;

  String get _base => '${BASE_URL.replaceAll(RegExp(r'/$'), '')}/api';

  // --------------------------
  // SAFE JSON DECODE
  // Always returns a strongly typed Map<String,dynamic> or null
  // --------------------------
  Map<String, dynamic>? _safeDecodeMap(String body) {
    try {
      final decoded = jsonDecode(body);
      if (decoded is Map) {
        return Map<String, dynamic>.from(decoded);
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  dynamic _safeDecode(String body) {
    try {
      return jsonDecode(body);
    } catch (_) {
      return null;
    }
  }

  // --------------------------
  // AUTH HEADERS
  // --------------------------
  Future<Map<String, String>> _authHeaders([String? token]) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (token != null && token.isNotEmpty) {
      headers['Authorization'] = 'Bearer $token';
      return headers;
    }

    final sp = await SharedPreferences.getInstance();
    final stored = sp.getString(PREF_AUTH_TOKEN) ??
        sp.getString('jwt_token') ??
        '';

    if (stored.isNotEmpty) headers['Authorization'] = 'Bearer $stored';
    return headers;
  }

  // ======================================================
  // AUTH: REGISTER
  // ======================================================
  Future<Map<String, dynamic>> register(
    String username,
    String email,
    String password, {
    String role = 'user',
  }) async {
    final uri = Uri.parse('$_base/auth/register');
    final resp = await http.post(
      uri,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'username': username,
        'email': email,
        'password': password,
        'role': role
      }),
    );

    final decoded = _safeDecodeMap(resp.body);

    if (decoded != null) return decoded;
    throw Exception('Register failed: ${resp.statusCode} ${resp.body}');
  }

  // ======================================================
  // AUTH: LOGIN
  // ======================================================
  Future<Map<String, dynamic>> login(String email, String password) async {
    final uri = Uri.parse('$_base/auth/login');
    final resp = await http.post(
      uri,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );

    final decoded = _safeDecodeMap(resp.body);
    if (decoded == null) {
      throw Exception("Invalid login response: ${resp.body}");
    }

    if (resp.statusCode >= 200 && resp.statusCode < 300) {
      final sp = await SharedPreferences.getInstance();

      final token = decoded['token']?.toString() ?? '';
      if (token.isNotEmpty) await sp.setString(PREF_AUTH_TOKEN, token);

      final user = decoded['user'];
      if (user is Map) {
        final u = Map<String, dynamic>.from(user);

        final id = int.tryParse(u['id'].toString()) ?? 0;
        await sp.setInt(PREF_USER_ID, id);

        await sp.setString(PREF_USER_EMAIL, u['email'] ?? '');
        await sp.setString(PREF_USER_NAME, u['username'] ?? '');
      }

      return decoded;
    }

    throw Exception("Login failed: ${resp.body}");
  }

  // ======================================================
  // PRODUCTS LIST
  // ======================================================
  Future<List<ProductModel>> fetchProducts() async {
    final uri = Uri.parse('$_base/products');
    final headers = await _authHeaders();

    final resp = await http.get(uri, headers: headers);
    if (resp.statusCode != 200) {
      throw Exception('fetchProducts failed: ${resp.statusCode} ${resp.body}');
    }

    final decoded = _safeDecode(resp.body);

    final items = (decoded is Map && decoded['data'] is List)
        ? decoded['data']
        : (decoded is List ? decoded : []);

    return items
        .map<ProductModel>(
            (m) => ProductModel.fromJson(Map<String, dynamic>.from(m)))
        .toList();
  }

  // ======================================================
  // SINGLE PRODUCT
  // ======================================================
  Future<ProductModel?> fetchProduct(int id) async {
    final uri = Uri.parse('$_base/products/$id');
    final headers = await _authHeaders();

    final resp = await http.get(uri, headers: headers);
    if (resp.statusCode != 200) return null;

    final decoded = _safeDecode(resp.body);

    final data = (decoded is Map && decoded['data'] != null)
        ? decoded['data']
        : decoded;

    if (data is Map) {
      return ProductModel.fromJson(Map<String, dynamic>.from(data));
    }

    return null;
  }

  // ======================================================
  // CATEGORIES
  // ======================================================
  Future<List<CategoryModel>> fetchCategories() async {
    final uri = Uri.parse('$_base/categories');
    final headers = await _authHeaders();

    final resp = await http.get(uri, headers: headers);

    if (resp.statusCode != 200) {
      throw Exception(
          'fetchCategories failed: ${resp.statusCode} ${resp.body}');
    }

    final decoded = _safeDecode(resp.body);

    final items = (decoded is Map && decoded['data'] is List)
        ? decoded['data']
        : (decoded is List ? decoded : []);

    return items
        .map<CategoryModel>(
            (m) => CategoryModel.fromJson(Map<String, dynamic>.from(m)))
        .toList();
  }

  // ======================================================
  // PLACE ORDER
  // ======================================================
  Future<Map<String, dynamic>> placeOrder(
    Map<String, dynamic> payload, {
    String? token,
  }) async {
    final uri = Uri.parse('$_base/orders');
    final headers = await _authHeaders(token);

    final resp = await http.post(
      uri,
      headers: headers,
      body: jsonEncode(payload),
    );

    final decoded = _safeDecodeMap(resp.body);

    if (resp.statusCode >= 200 && resp.statusCode < 300) {
      return decoded ?? {'status': 'success'};
    }

    throw Exception(
        'placeOrder failed: ${resp.statusCode}\nResponse: ${resp.body}');
  }

  // ======================================================
  // FETCH USER ORDERS
  // ======================================================
  Future<List<dynamic>> fetchUserOrders() async {
    final uri = Uri.parse('$_base/orders');
    final headers = await _authHeaders();

    final resp = await http.get(uri, headers: headers);

    final decoded = _safeDecodeMap(resp.body);
    if (decoded != null && decoded['data'] is List) {
      return decoded['data'];
    }

    throw Exception("Failed to fetch orders: ${resp.body}");
  }

  // ======================================================
  // ORDER DETAIL
  // ======================================================
  Future<Map<String, dynamic>> fetchOrderDetails(int id) async {
    final uri = Uri.parse('$_base/orders/$id');
    final headers = await _authHeaders();

    final resp = await http.get(uri, headers: headers);

    final decoded = _safeDecodeMap(resp.body);
    if (resp.statusCode == 200 && decoded != null) {
      return decoded;
    }

    throw Exception('Failed to fetch order detail: ${resp.body}');
  }

  // ======================================================
  // FILE UPLOAD
  // ======================================================
  Future<Map<String, dynamic>> uploadFile(
    File file, {
    String fieldName = 'file',
  }) async {
    final uri = Uri.parse('$_base/upload');
    final request = http.MultipartRequest('POST', uri);

    final sp = await SharedPreferences.getInstance();
    final token = sp.getString(PREF_AUTH_TOKEN) ?? '';
    if (token.isNotEmpty) {
      request.headers['Authorization'] = 'Bearer $token';
    }

    final mimeType = lookupMimeType(file.path) ?? 'application/octet-stream';
    final parts = mimeType.split('/');

    request.files.add(
      await http.MultipartFile.fromPath(
        fieldName,
        file.path,
        contentType:
            MediaType(parts[0], parts.length > 1 ? parts[1] : 'octet-stream'),
      ),
    );

    final streamed = await request.send();
    final resp = await http.Response.fromStream(streamed);

    final decoded = _safeDecodeMap(resp.body);
    if (resp.statusCode >= 200 && resp.statusCode < 300) {
      return decoded ?? {};
    }

    throw Exception('uploadFile failed: ${resp.statusCode} ${resp.body}');
  }

  // ======================================================
  // MIME LOOKUP
  // ======================================================
  String? lookupMimeType(String path) {
    final ext = path.split('.').last.toLowerCase();
    switch (ext) {
      case 'png':
      case 'jpg':
      case 'jpeg':
        return 'image/$ext';
      case 'gif':
        return 'image/gif';
      case 'webp':
        return 'image/webp';
      case 'pdf':
        return 'application/pdf';
      default:
        return null;
    }
  }
}
