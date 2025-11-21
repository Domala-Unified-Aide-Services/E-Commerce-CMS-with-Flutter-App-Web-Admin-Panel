import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/cart_provider.dart';
import '../services/api_service.dart';

class PaymentScreen extends StatefulWidget {
  final double totalAmount;
  const PaymentScreen({super.key, required this.totalAmount});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen> {
  bool _loading = false;
  String? _error;

  Future<void> _placeOrder(String method) async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final cart = Provider.of<CartProvider>(context, listen: false);
      final api = ApiService();

      final payload = {
        "total_price": widget.totalAmount,
        "items": cart.items
            .map((item) => {
                  "product_id": item.product.id,
                  "price": item.product.price,
                  "quantity": item.qty,
                })
            .toList(),
      };

      final response = await api.placeOrder(payload);
      final orderId = response["order_id"];

      cart.clear();

      if (mounted) {
        Navigator.pushNamed(
          context,
          '/orderSuccess',
          arguments: orderId,
        );
      }
    } catch (e) {
      setState(() => _error = "Failed: $e");
    } finally {
      setState(() => _loading = false);
    }
  }

  Widget paymentTile({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: _loading ? null : onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        margin: const EdgeInsets.symmetric(vertical: 6),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.grey.shade300),
        ),
        child: Row(
          children: [
            Icon(icon, size: 28, color: Colors.deepPurple),
            const SizedBox(width: 16),
            Text(title, style: const TextStyle(fontSize: 16)),
            const Spacer(),
            const Icon(Icons.arrow_forward_ios, size: 16),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final cart = Provider.of<CartProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text("Select Payment"),
        elevation: 0,
      ),

      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Total Amount
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.deepPurple.withOpacity(0.08),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                children: [
                  const Text(
                    "Payable Amount:",
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  const Spacer(),
                  Text(
                    "₹${widget.totalAmount.toStringAsFixed(2)}",
                    style: const TextStyle(
                      fontSize: 20,
                      color: Colors.deepPurple,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 16),

            if (_error != null)
              Padding(
                padding: const EdgeInsets.only(bottom: 10),
                child: Text(
                  _error!,
                  style: const TextStyle(color: Colors.red),
                ),
              ),

            const Text(
              "Choose Payment Method",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),

            const SizedBox(height: 12),

            // COD Option
            paymentTile(
              icon: Icons.money,
              title: "Cash on Delivery (COD)",
              onTap: () => _placeOrder("COD"),
            ),

            // Online mock UPI
            paymentTile(
              icon: Icons.account_balance_wallet,
              title: "UPI / Wallet (Mock Payment)",
              onTap: () async {
                showDialog(
                  context: context,
                  builder: (_) => AlertDialog(
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    title: const Text("Mock Payment"),
                    content: const Text("Simulating UPI payment..."),
                  ),
                );

                await Future.delayed(const Duration(seconds: 2));
                Navigator.pop(context);

                _placeOrder("UPI");
              },
            ),

            // Card mock
            paymentTile(
              icon: Icons.credit_card,
              title: "Debit/Credit Card (Mock Payment)",
              onTap: () async {
                showDialog(
                  context: context,
                  builder: (_) => const AlertDialog(
                    title: Text("Mock Card Payment"),
                    content: Text("Simulating card payment..."),
                  ),
                );

                await Future.delayed(const Duration(seconds: 2));
                Navigator.pop(context);

                _placeOrder("Card");
              },
            ),

            const Spacer(),

            if (_loading)
              const Center(child: CircularProgressIndicator()),
          ],
        ),
      ),
    );
  }
}
