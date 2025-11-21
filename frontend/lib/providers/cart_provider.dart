// lib/providers/cart_provider.dart
import 'package:flutter/material.dart';
import '../models/product_model.dart';

class CartItem {
  final ProductModel product;
  int qty;
  CartItem({required this.product, this.qty = 1});

  double get lineTotal => product.price * qty;
}

class CartProvider extends ChangeNotifier {
  final List<CartItem> _items = [];

  List<CartItem> get items => List.unmodifiable(_items);

  int get totalItems => _items.fold<int>(0, (s, it) => s + it.qty);

  double get totalAmount => _items.fold<double>(0.0, (s, it) => s + it.lineTotal);

  void addProduct(ProductModel p, {int qty = 1}) {
    final idx = _items.indexWhere((it) => it.product.id == p.id);
    if (idx >= 0) {
      _items[idx].qty += qty;
    } else {
      _items.add(CartItem(product: p, qty: qty));
    }
    notifyListeners();
  }

  void removeProduct(int productId) {
    _items.removeWhere((it) => it.product.id == productId);
    notifyListeners();
  }

  void setQty(int productId, int qty) {
    final idx = _items.indexWhere((it) => it.product.id == productId);
    if (idx >= 0) {
      if (qty <= 0) {
        _items.removeAt(idx);
      } else {
        _items[idx].qty = qty;
      }
      notifyListeners();
    }
  }

  void clear() {
    _items.clear();
    notifyListeners();
  }
}
