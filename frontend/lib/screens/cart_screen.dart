import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/cart_provider.dart';
import '../models/product_model.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final cart = Provider.of<CartProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text("Your Cart"),
        elevation: 0,
      ),

      bottomNavigationBar: cart.items.isEmpty
          ? const SizedBox.shrink()
          : Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border(top: BorderSide(color: Colors.grey.shade300)),
              ),
              child: SafeArea(
                child: ElevatedButton(
                  onPressed: () {
                    /// ⛳ FIRST STEP → GO TO ADDRESS SCREEN
                    Navigator.pushNamed(context, '/address');
                  },
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    backgroundColor: Colors.deepPurpleAccent,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: Text(
                    "Proceed to Checkout (₹${cart.totalAmount.toStringAsFixed(2)})",
                    style: const TextStyle(fontSize: 16),
                  ),
                ),
              ),
            ),

      body: cart.items.isEmpty
          ? const Center(child: Text("Your cart is empty"))
          : ListView.builder(
              padding: const EdgeInsets.all(12),
              itemCount: cart.items.length,
              itemBuilder: (_, i) {
                final item = cart.items[i];
                final p = item.product;

                return Card(
                  elevation: 1,
                  child: ListTile(
                    leading: (p.imageUrl != null && p.imageUrl!.isNotEmpty)
                        ? Image.network(
                            p.imageUrl!,
                            width: 60,
                            height: 60,
                            fit: BoxFit.cover,
                          )
                        : const Icon(Icons.image, size: 40),

                    title: Text(p.name),
                    subtitle: Text("₹${p.price.toStringAsFixed(2)}"),

                    trailing: SizedBox(
                      width: 120,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.end,
                        children: [
                          IconButton(
                            icon: const Icon(Icons.remove_circle_outline),
                            onPressed: () {
                              if (item.qty > 1) {
                                cart.setQty(p.id, item.qty - 1);
                              } else {
                                cart.removeProduct(p.id);
                              }
                            },
                          ),
                          Text(item.qty.toString(),
                              style: const TextStyle(
                                  fontWeight: FontWeight.bold, fontSize: 16)),
                          IconButton(
                            icon: const Icon(Icons.add_circle_outline),
                            onPressed: () {
                              cart.setQty(p.id, item.qty + 1);
                            },
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
    );
  }
}
