import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../constants.dart';
import '../providers/products_provider.dart';
import '../providers/categories_provider.dart';
import '../providers/cart_provider.dart';
import '../widgets/product_card.dart';
import '../models/product_model.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<ProductsProvider>(context, listen: false).loadProducts();
      Provider.of<CategoriesProvider>(context, listen: false).loadCategories();
    });
  }
 // add at the top of the file with other imports

String _fullUrl(String raw) {
  if (raw == null || raw.isEmpty) return '';
  if (raw.startsWith("http://") || raw.startsWith("https://")) return raw;
  // normalize:
  final normalized = raw.startsWith('/') ? raw.substring(1) : raw;
  return BASE_URL + normalized; // BASE_URL ends with '/'
}


  @override
  Widget build(BuildContext context) {
    final productsProv = Provider.of<ProductsProvider>(context);
    final categoriesProv = Provider.of<CategoriesProvider>(context);
    final cart = Provider.of<CartProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text("Shop"),
        elevation: 0,
        centerTitle: true,
      ),

      floatingActionButton: Column(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          FloatingActionButton(
            heroTag: "orders",
            onPressed: () => Navigator.pushNamed(context, '/orders'),
            child: const Icon(Icons.receipt_long),
          ),
          const SizedBox(height: 12),

          if (cart.totalItems > 0)
            FloatingActionButton.extended(
              heroTag: "cart",
              onPressed: () => Navigator.pushNamed(context, '/cart'),
              label: Text("${cart.totalItems} items"),
              icon: const Icon(Icons.shopping_cart),
            ),
        ],
      ),

      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 0,
        onTap: (i) {
          if (i == 1) Navigator.pushNamed(context, '/categories');
          if (i == 2) Navigator.pushNamed(context, '/profile');
          if (i == 3) Navigator.pushNamed(context, '/cart');
        },
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: "Home"),
          BottomNavigationBarItem(icon: Icon(Icons.category), label: "Categories"),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: "Profile"),
          BottomNavigationBarItem(icon: Icon(Icons.shopping_cart), label: "Cart"),
        ],
      ),

      body: RefreshIndicator(
        onRefresh: () => productsProv.loadProducts(force: true),
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [

              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                child: Text("Shop by Category",
                    style: Theme.of(context).textTheme.titleLarge),
              ),

              categoriesProv.loading
                  ? const Center(child: CircularProgressIndicator())
                  : GridView.builder(
                      shrinkWrap: true,
                      itemCount: categoriesProv.items.length,
                      physics: const NeverScrollableScrollPhysics(),
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 3,
                        crossAxisSpacing: 16,
                        mainAxisSpacing: 16,
                        childAspectRatio: 0.85,
                      ),
                      itemBuilder: (_, i) {
                        final cat = categoriesProv.items[i];
                        final img = cat.image ?? "";

                        return GestureDetector(
                          onTap: () {
                            Navigator.pushNamed(
                              context,
                              '/productList',
                              arguments: cat.id,   // FIXED HERE ✔
                            );
                          },
                          child: Column(
                            children: [
                              CircleAvatar(
                                radius: 34,
                                backgroundColor: Colors.grey.shade300,
                                backgroundImage:
                                    img.isNotEmpty ? NetworkImage(_fullUrl(img)) : null,
                                child: img.isEmpty
                                    ? const Icon(Icons.category, size: 30, color: Colors.black54)
                                    : null,
                              ),
                              const SizedBox(height: 6),
                              Text(
                                cat.name,
                                textAlign: TextAlign.center,
                                style: const TextStyle(fontSize: 13),
                                maxLines: 2,
                              ),
                            ],
                          ),
                        );
                      },
                    ),

              const SizedBox(height: 16),

              _sectionTitle("Recommended"),
              _horizontalSlider(productsProv.recommended),

              _sectionTitle("Trending"),
              _horizontalSlider(productsProv.trending),

              _sectionTitle("New Arrivals"),
              _horizontalSlider(productsProv.recent),

              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _sectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      child: Text(
        title,
        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18),
      ),
    );
  }

  Widget _horizontalSlider(List<ProductModel> list) {
    if (list.isEmpty) {
      return const Padding(
        padding: EdgeInsets.all(16),
        child: Text("No products available."),
      );
    }

    return SizedBox(
      height: 260,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        separatorBuilder: (_, __) => const SizedBox(width: 12),
        itemCount: list.length,
        itemBuilder: (_, i) {
          final p = list[i];
          final img = p.imageUrl ?? "";

          return SizedBox(
            width: 160,
            child: ProductCard(
              product: p,
              imageUrl: _fullUrl(img),

              onAdd: () {
                Provider.of<CartProvider>(context, listen: false).addProduct(p);
              },
              onTap: () {
                Navigator.pushNamed(context, '/product', arguments: p);
              },
            ),
          );
        },
      ),
    );
  }
}
