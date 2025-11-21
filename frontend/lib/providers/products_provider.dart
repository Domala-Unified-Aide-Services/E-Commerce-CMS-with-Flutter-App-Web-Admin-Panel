import 'package:flutter/material.dart';
import '../models/product_model.dart';
import '../services/api_service.dart';

class ProductsProvider extends ChangeNotifier {
  final ApiService _api = ApiService();

  List<ProductModel> _items = [];
  bool _loading = false;
  String? _error;

  // New sections
  List<ProductModel> trending = [];
  List<ProductModel> recent = [];
  List<ProductModel> recommended = [];

  // categoryId → products[]
  Map<int, List<ProductModel>> productsByCategory = {};

  List<ProductModel> get items => List.unmodifiable(_items);
  bool get loading => _loading;
  String? get error => _error;
  int get count => _items.length;

  ProductsProvider();

  Future<void> loadProducts({bool force = false}) async {
    if (_loading) return;
    if (_items.isNotEmpty && !force) return;

    _loading = true;
    _error = null;
    notifyListeners();

    try {
      final list = await _api.fetchProducts();
      _items = list;

      _prepareSections();
    } catch (e) {
      _error = 'Failed to load products: $e';
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  // -------------------------------------------------------------
  // ⭐ Create computed sections for Option-C UI
  // -------------------------------------------------------------
  void _prepareSections() {
    // Trending = expensive → cheap (simulating popularity)
    trending = [..._items]
      ..sort((a, b) => b.price.compareTo(a.price));
    trending = trending.take(10).toList();

    // Recent = newest created_at
    recent = [..._items];
    recent.sort((a, b) {
      final da = DateTime.tryParse(a.createdAt ?? '') ?? DateTime(2000);
      final db = DateTime.tryParse(b.createdAt ?? '') ?? DateTime(2000);
      return db.compareTo(da);
    });
    recent = recent.take(10).toList();

    // Recommended = random 10
    recommended = [..._items]..shuffle();
    recommended = recommended.take(10).toList();

    // Group by category
    productsByCategory = {};
    for (var p in _items) {
      final id = p.categoryId ?? 0;
      if (!productsByCategory.containsKey(id)) {
        productsByCategory[id] = [];
      }
      productsByCategory[id]!.add(p);
    }
  }

  // -------------------------------------------------------------
  // Find a product safely
  // -------------------------------------------------------------
  ProductModel? findById(int id) {
    try {
      return _items.firstWhere((p) => p.id == id);
    } catch (_) {
      return null;
    }
  }

  // Clear all cached data
  void clearCache() {
    _items = [];
    trending = [];
    recent = [];
    recommended = [];
    productsByCategory = {};
    notifyListeners();
  }
}
