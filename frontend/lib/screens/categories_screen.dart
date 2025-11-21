// lib/screens/categories_screen.dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/categories_provider.dart';
import '../widgets/category_tile.dart';
import 'product_list_screen.dart';


class CategoriesScreen extends StatefulWidget {
  const CategoriesScreen({super.key});

  @override
  State<CategoriesScreen> createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  late CategoriesProvider _catProv;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _catProv = Provider.of<CategoriesProvider>(context, listen: false);
      _catProv.loadCategories();
    });
  }

  @override
  Widget build(BuildContext context) {
    final prov = Provider.of<CategoriesProvider>(context);

    return Scaffold(
      appBar: AppBar(title: const Text('Categories')),
      body: Builder(builder: (ctx) {
        if (prov.loading) {
          return const Center(child: CircularProgressIndicator());
        }
        if (prov.error != null) {
          return ListView(children: [Padding(padding: const EdgeInsets.all(20), child: Text(prov.error!, textAlign: TextAlign.center))]);
        }
        final items = prov.items;
        if (items.isEmpty) {
          return const Center(child: Padding(padding: EdgeInsets.all(20), child: Text('No categories found')));
        }

        return ListView.separated(
          padding: const EdgeInsets.all(12),
          itemCount: items.length,
          separatorBuilder: (_, __) => const SizedBox(height: 8),
          itemBuilder: (c, i) {
            final cat = items[i];
            return CategoryTile(
              title: cat.name,
              subtitle: cat.description ?? '',
              onTap: () {
  Navigator.push(
    context,
    MaterialPageRoute(
      builder: (_) => ProductListScreen(
        categoryId: cat.id,
        categoryName: cat.name,
      ),
    ),
  );
},

            );
          },
        );
      }),
    );
  }
}
