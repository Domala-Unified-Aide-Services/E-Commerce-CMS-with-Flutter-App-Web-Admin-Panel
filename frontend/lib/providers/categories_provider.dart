// lib/providers/categories_provider.dart
import 'package:flutter/material.dart';
import '../models/category_model.dart';
import '../services/api_service.dart';

class CategoriesProvider extends ChangeNotifier {
  final ApiService _api = ApiService();

  List<CategoryModel> _items = [];
  bool _loading = false;
  String? _error;

  List<CategoryModel> get items => List.unmodifiable(_items);
  bool get loading => _loading;
  String? get error => _error;
  int get count => _items.length;

  CategoriesProvider();

  Future<void> loadCategories({bool force = false}) async {
    if (_loading) return;
    if (_items.isNotEmpty && !force) return;

    _loading = true;
    _error = null;
    notifyListeners();

    try {
      final list = await _api.fetchCategories();
      _items = list;
    } catch (e) {
      _error = 'Failed to load categories: $e';
    } finally {
      _loading = false;
      notifyListeners();
    }
  }

  CategoryModel? findById(int id) {
    try {
      return _items.firstWhere((c) => c.id == id);
    } catch (_) {
      return null;
    }
  }

  void clearCache() {
    _items = [];
    notifyListeners();
  }
}
