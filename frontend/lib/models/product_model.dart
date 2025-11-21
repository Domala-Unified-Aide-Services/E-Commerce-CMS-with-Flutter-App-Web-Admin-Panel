// lib/models/product_model.dart
class ProductModel {
  final int id;
  final String name;
  final String? description;
  final double price;
  final int? categoryId;
  final int stock;
  final String? imageUrl;
  final String? createdAt;

  ProductModel({
    required this.id,
    required this.name,
    this.description,
    required this.price,
    this.categoryId,
    required this.stock,
    this.imageUrl,
    this.createdAt,
  });

  // safe int parser
  static int? _toInt(dynamic v) {
    if (v == null) return null;
    if (v is int) return v;
    if (v is double) return v.toInt();
    if (v is String) {
      final parsed = int.tryParse(v);
      if (parsed != null) return parsed;
      final d = double.tryParse(v);
      if (d != null) return d.toInt();
    }
    return null;
  }

  // safe double parser
  static double _toDouble(dynamic v) {
    if (v == null) return 0.0;
    if (v is double) return v;
    if (v is int) return v.toDouble();
    if (v is String) {
      return double.tryParse(v) ?? 0.0;
    }
    return 0.0;
  }

  factory ProductModel.fromJson(Map<String, dynamic> json) {
    final id = _toInt(json['id']) ?? 0;
    final price = _toDouble(json['price']);
    final categoryId = _toInt(json['category_id']);
    final stock = _toInt(json['stock']) ?? 0;

    // image field may be named image, image_url, or filepath — attempt common keys
    String? image;
    if (json['image_url'] != null) image = json['image_url'].toString();
    else if (json['image'] != null) image = json['image'].toString();
    else if (json['filepath'] != null) image = json['filepath'].toString();

    return ProductModel(
      id: id,
      name: json['name']?.toString() ?? 'Unnamed',
      description: json['description']?.toString(),
      price: price,
      categoryId: categoryId,
      stock: stock,
      imageUrl: image,
      createdAt: json['created_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'description': description,
        'price': price,
        'category_id': categoryId,
        'stock': stock,
        'image_url': imageUrl,
        'created_at': createdAt,
      };
}
