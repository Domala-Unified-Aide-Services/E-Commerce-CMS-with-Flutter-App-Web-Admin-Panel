import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class AddressScreen extends StatefulWidget {
  const AddressScreen({super.key});

  @override
  State<AddressScreen> createState() => _AddressScreenState();
}

class _AddressScreenState extends State<AddressScreen> {
  final TextEditingController addressController = TextEditingController();
  bool loading = false;

  @override
  void initState() {
    super.initState();
    _loadSavedAddress();
  }

  Future<void> _loadSavedAddress() async {
    final sp = await SharedPreferences.getInstance();
    addressController.text = sp.getString("user_address") ?? "";
  }

  Future<void> _saveAddress() async {
    if (addressController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text("Please enter address")));
      return;
    }

    setState(() => loading = true);

    final sp = await SharedPreferences.getInstance();
    await sp.setString("user_address", addressController.text.trim());

    setState(() => loading = false);

    // 👉 Navigate to checkout screen
    Navigator.pushNamed(context, "/checkout");
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Delivery Address")),

      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text("Enter your delivery address:",
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600)),

            const SizedBox(height: 12),

            TextField(
              controller: addressController,
              maxLines: 3,
              decoration: InputDecoration(
                hintText: "House no, Area, City, Pincode",
                filled: true,
                fillColor: Colors.white,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
            ),

            const SizedBox(height: 20),

            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: loading ? null : _saveAddress,
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  backgroundColor: Colors.deepPurpleAccent,
                ),
                child: loading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text(
                        "Save & Continue",
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                      ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
