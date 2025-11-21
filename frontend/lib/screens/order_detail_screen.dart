import 'package:flutter/material.dart';
import '../services/api_service.dart';

class OrderDetailScreen extends StatefulWidget {
  final int orderId;

  const OrderDetailScreen({super.key, required this.orderId});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  bool loading = true;
  String? error;
  Map<String, dynamic>? order;
  List<dynamic> items = [];

  @override
  void initState() {
    super.initState();
    _loadOrder();
  }

  Future<void> _loadOrder() async {
    setState(() {
      loading = true;
      error = null;
    });

    try {
      final api = ApiService();
      final data = await api.fetchOrderDetails(widget.orderId);

      setState(() {
        order = data['order'];
        items = data['items'] ?? [];
        loading = false;
      });
    } catch (e) {
      setState(() {
        error = "Failed to load order: $e";
        loading = false;
      });
    }
  }

  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'delivered':
        return Colors.green;
      case 'shipped':
        return Colors.blue;
      case 'cancelled':
        return Colors.red;
      default:
        return Colors.orange; // pending
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Order #${widget.orderId}")),
      body: loading
          ? const Center(child: CircularProgressIndicator())
          : error != null
              ? Center(child: Text(error!))
              : RefreshIndicator(
                  onRefresh: _loadOrder,
                  child: ListView(
                    padding: const EdgeInsets.all(16),
                    children: [
                      // ------------------------------
                      // ORDER SUMMARY
                      // ------------------------------
                      Card(
                        elevation: 2,
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("Order Summary",
                                  style: Theme.of(context)
                                      .textTheme
                                      .titleMedium
                                      ?.copyWith(fontWeight: FontWeight.bold)),

                              const SizedBox(height: 10),

                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  const Text("Order ID:",
                                      style: TextStyle(color: Colors.grey)),
                                  Text("${order?['id'] ?? ''}"),
                                ],
                              ),

                              const SizedBox(height: 6),

                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  const Text("Total:",
                                      style: TextStyle(color: Colors.grey)),
                                  Text(
                                    "₹${order?['total_price'] ?? 0}",
                                    style: const TextStyle(
                                        fontWeight: FontWeight.bold),
                                  ),
                                ],
                              ),

                              const SizedBox(height: 6),

                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  const Text("Status:",
                                      style: TextStyle(color: Colors.grey)),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 10, vertical: 5),
                                    decoration: BoxDecoration(
                                      color: _statusColor(
                                              order?['status'] ?? 'pending')
                                          .withOpacity(0.15),
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: Text(
                                      (order?['status'] ?? 'pending')
                                          .toUpperCase(),
                                      style: TextStyle(
                                        color: _statusColor(
                                            order?['status'] ?? 'pending'),
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                ],
                              ),

                              const SizedBox(height: 6),

                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  const Text("Date:",
                                      style: TextStyle(color: Colors.grey)),
                                  Text(order?['created_at'] ?? ''),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),

                      const SizedBox(height: 20),

                      // ------------------------------
                      // ORDER ITEMS
                      // ------------------------------
                      Text("Items",
                          style: Theme.of(context)
                              .textTheme
                              .titleMedium
                              ?.copyWith(fontWeight: FontWeight.bold)),
                      const SizedBox(height: 10),

                      ...items.map((item) {
                        final name = item['product_name'] ?? "Product ${item['product_id']}";
                        final qty = item['quantity'];
                        final price = item['price'];

                        return Card(
                          elevation: 1,
                          child: ListTile(
                            title: Text(name),
                            subtitle: Text("Qty: $qty"),
                            trailing: Text("₹${price * qty}",
                                style: const TextStyle(
                                    fontWeight: FontWeight.bold)),
                          ),
                        );
                      }).toList(),

                      if (items.isEmpty)
                        const Padding(
                          padding: EdgeInsets.all(20),
                          child: Center(child: Text("No items found.")),
                        ),
                    ],
                  ),
                ),
    );
  }
}
