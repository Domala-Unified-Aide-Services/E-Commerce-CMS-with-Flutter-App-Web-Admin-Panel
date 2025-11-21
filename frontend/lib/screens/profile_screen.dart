import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  String username = "";
  String email = "";

  @override
  void initState() {
    super.initState();
    _loadUser();
  }

  Future<void> _loadUser() async {
    final sp = await SharedPreferences.getInstance();
    setState(() {
      username = sp.getString("user_name") ?? "User";
      email = sp.getString("user_email") ?? "Email not found";
    });
  }

  Future<void> _logout() async {
    final sp = await SharedPreferences.getInstance();
    await sp.clear();

    if (mounted) {
      Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
    }
  }

  Widget _tile(IconData icon, String title, VoidCallback onTap) {
    return ListTile(
      leading: Icon(icon, size: 28),
      title: Text(title, style: const TextStyle(fontSize: 16)),
      trailing: const Icon(Icons.arrow_forward_ios, size: 16),
      onTap: onTap,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Profile"),
        centerTitle: true,
        elevation: 0,
      ),

      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [

          // ---------------------------------------------------------
          // USER HEADER
          // ---------------------------------------------------------
          Center(
            child: Column(
              children: [
                const CircleAvatar(
                  radius: 45,
                  backgroundColor: Colors.deepPurpleAccent,
                  child: Icon(Icons.person, size: 55, color: Colors.white),
                ),
                const SizedBox(height: 12),
                Text(
                  username,
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                ),
                Text(
                  email,
                  style: const TextStyle(fontSize: 14, color: Colors.grey),
                ),
              ],
            ),
          ),

          const SizedBox(height: 30),

          // ---------------------------------------------------------
          // MENU ITEMS
          // ---------------------------------------------------------
          _tile(Icons.location_on, "My Address", () {
            Navigator.pushNamed(context, '/address');
          }),

          _tile(Icons.receipt_long, "My Orders", () {
            Navigator.pushNamed(context, '/orders');
          }),

          _tile(Icons.shopping_cart, "My Cart", () {
            Navigator.pushNamed(context, '/cart');
          }),

          const SizedBox(height: 20),

          // ---------------------------------------------------------
          // LOGOUT
          // ---------------------------------------------------------
          ElevatedButton(
            onPressed: _logout,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.redAccent,
              padding: const EdgeInsets.symmetric(vertical: 14),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            child: const Text(
              "Logout",
              style: TextStyle(fontSize: 16, color: Colors.white),
            ),
          ),
        ],
      ),
    );
  }
}
