import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/products_provider.dart';
import '../models/product_model.dart';
import '../widgets/product_card.dart';


class ProductListScreen extends StatefulWidget {
  final int? categoryId;
  final String? categoryName;

  const ProductListScreen({
    super.key,
    this.categoryId,
    this.categoryName,
  });

  @override
  State<ProductListScreen> createState() => _ProductListScreenState();
}

class _ProductListScreenState extends State<ProductListScreen> {
  List<ProductModel> filtered = [];
  String search = "";
  String sortBy = "relevance"; // default

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final products = Provider.of<ProductsProvider>(context, listen: false).items;
      _applyFilter(products);
    });
  }

  void _applyFilter(List<ProductModel> products) {
    List<ProductModel> list = [];

    list = products.where((p) {
      if (widget.categoryId != null && p.categoryId != widget.categoryId) return false;
      if (search.isNotEmpty && !p.name.toLowerCase().contains(search.toLowerCase())) return false;
      return true;
    }).toList();

    // -------------------------
    // SORTING LOGIC (B2)
    // -------------------------
    switch (sortBy) {
      case "low":
        list.sort((a, b) => a.price.compareTo(b.price));
        break;
      case "high":
        list.sort((a, b) => b.price.compareTo(a.price));
        break;
      case "new":
        list.sort((a, b) {
          final da = DateTime.tryParse(a.createdAt ?? "") ?? DateTime(2000);
          final db = DateTime.tryParse(b.createdAt ?? "") ?? DateTime(2000);
          return db.compareTo(da);
        });
        break;
      case "relevance":
      default:
        break;
    }

    setState(() {
      filtered = list;
    });
  }

  @override
  Widget build(BuildContext context) {
    final productsProv = Provider.of<ProductsProvider>(context);
    final products = productsProv.items;

    _applyFilter(products);

    return Scaffold(
      backgroundColor: Colors.grey.shade100,
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
        title: Text(widget.categoryName ?? "Products"),
      ),

      body: Column(
        children: [
          // -------------------------
          // SEARCH + SORT ROW
          // -------------------------
          Container(
            color: Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            child: Row(
              children: [
                // SEARCH BAR
                Expanded(
                  child: TextField(
                    decoration: InputDecoration(
                      prefixIcon: const Icon(Icons.search),
                      hintText: "Search...",
                      filled: true,
                      fillColor: Colors.grey.shade200,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: BorderSide.none,
                      ),
                    ),
                    onChanged: (v) {
                      search = v;
                      _applyFilter(products);
                    },
                  ),
                ),

                const SizedBox(width: 10),

                // SORT DROPDOWN
                DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: sortBy,
                    items: const [
                      DropdownMenuItem(value: "relevance", child: Text("Relevance")),
                      DropdownMenuItem(value: "low", child: Text("Price ↑")),
                      DropdownMenuItem(value: "high", child: Text("Price ↓")),
                      DropdownMenuItem(value: "new", child: Text("Newest")),
                    ],
                    onChanged: (v) {
                      setState(() {
                        sortBy = v!;
                        _applyFilter(products);
                      });
                    },
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 8),

          // -------------------------
          // PRODUCT GRID
          // -------------------------
          Expanded(
            child: filtered.isEmpty
                ? const Center(child: Text("No products found"))
                : GridView.builder(
                    padding: const EdgeInsets.all(12),
                    itemCount: filtered.length,
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 0.66,
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                    ),
                    itemBuilder: (context, i) {
                      final p = filtered[i];
                      return GestureDetector(
                        onTap: () => Navigator.pushNamed(context, '/product', arguments: p),
                        child: ProductCard(
                          product: p,
                          imageUrl: p.imageUrl ?? "",
                          onAdd: () {},
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}
