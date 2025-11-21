import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/cart_provider.dart';
import '../models/product_model.dart';

class ProductDetailScreen extends StatefulWidget {
  final ProductModel product;
  const ProductDetailScreen({super.key, required this.product});

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  int qty = 1;

  @override
  Widget build(BuildContext context) {
    final cart = Provider.of<CartProvider>(context);
    final p = widget.product;

    return Scaffold(
      backgroundColor: Colors.white,

      appBar: AppBar(
        title: Text(p.name, overflow: TextOverflow.ellipsis),
        elevation: 0,
      ),

      // --------------------------------------------------------
      // SIMPLE / CLEAN bottom bar (Flipkart style)
      // --------------------------------------------------------
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          border: Border(top: BorderSide(color: Colors.grey.shade300)),
        ),
        child: Row(
          children: [
            // Qty Selector
            Container(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.grey.shade400),
              ),
              child: Row(
                children: [
                  IconButton(
                    onPressed: () {
                      if (qty > 1) setState(() => qty--);
                    },
                    icon: const Icon(Icons.remove),
                  ),
                  Text(
                    qty.toString(),
                    style: const TextStyle(fontWeight: FontWeight.bold),
                  ),
                  IconButton(
                    onPressed: () => setState(() => qty++),
                    icon: const Icon(Icons.add),
                  ),
                ],
              ),
            ),

            const SizedBox(width: 16),

            // ADD TO CART BUTTON
            Expanded(
              child: ElevatedButton(
                onPressed: () {
                  cart.addProduct(p, qty: qty);
                  ScaffoldMessenger.of(context)
                      .showSnackBar(const SnackBar(content: Text("Added to cart")));
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.deepPurpleAccent,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
                child: Text(
                  "Add ₹${(p.price * qty).toStringAsFixed(2)}",
                  style: const TextStyle(fontSize: 16),
                ),
              ),
            ),
          ],
        ),
      ),

      // --------------------------------------------------------
      // BODY
      // --------------------------------------------------------
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Product Image
            AspectRatio(
              aspectRatio: 1,
              child: p.imageUrl == null || p.imageUrl!.isEmpty
                  ? Container(
                      color: Colors.grey.shade200,
                      child: const Icon(Icons.image, size: 80),
                    )
                  : Image.network(
                      p.imageUrl!,
                      fit: BoxFit.cover,
                    ),
            ),

            const SizedBox(height: 16),

            // PRICE
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                "₹${p.price.toStringAsFixed(2)}",
                style: const TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: Colors.deepPurple,
                ),
              ),
            ),

            const SizedBox(height: 6),

            // NAME
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                p.name,
                style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w600),
              ),
            ),

            const SizedBox(height: 12),

            // STOCK STATUS
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                p.stock > 0 ? "In stock (${p.stock} available)" : "Out of stock",
                style: TextStyle(
                  color: p.stock > 0 ? Colors.green : Colors.red,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ),

            const SizedBox(height: 16),

            // DESCRIPTION
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                p.description?.isNotEmpty == true
                    ? p.description!
                    : "No description available.",
                style: const TextStyle(fontSize: 15, height: 1.4),
              ),
            ),

            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }
}
