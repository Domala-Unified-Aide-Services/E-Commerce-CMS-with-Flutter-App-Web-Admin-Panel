// lib/widgets/category_tile.dart
import 'package:flutter/material.dart';

class CategoryTile extends StatelessWidget {
  final String title;
  final String subtitle;
  final VoidCallback? onTap;

  const CategoryTile({
    super.key,
    required this.title,
    required this.subtitle,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      child: ListTile(
        onTap: onTap,
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.bold)),
        subtitle: subtitle.isNotEmpty ? Text(subtitle, maxLines: 1, overflow: TextOverflow.ellipsis) : null,
        trailing: const Icon(Icons.chevron_right),
      ),
    );
  }
}
