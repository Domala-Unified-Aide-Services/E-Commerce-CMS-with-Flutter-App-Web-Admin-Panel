class CategoryModel {
  final int id;
  final String name;
  final String? description;
  final String? image;      // <-- matches DB column name
  final String? createdAt;

  CategoryModel({
    required this.id,
    required this.name,
    this.description,
    this.image,
    this.createdAt,
  });

  static int _toInt(dynamic v) {
    if (v is int) return v;
    if (v is double) return v.toInt();
    if (v is String) {
      return int.tryParse(v) ?? (double.tryParse(v)?.toInt() ?? 0);
    }
    return 0;
  }

  factory CategoryModel.fromJson(Map<String, dynamic> json) {
    return CategoryModel(
      id: _toInt(json['id']),
      name: json['name']?.toString() ?? 'Unnamed',
      description: json['description']?.toString(),
      image: json['image']?.toString(), // <-- GETS THE CORRECT FIELD
      createdAt: json['created_at']?.toString(),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'description': description,
        'image': image,       // <-- Correct
        'created_at': createdAt,
      };
}
