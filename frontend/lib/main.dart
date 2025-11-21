import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

// Providers
import 'providers/products_provider.dart';
import 'providers/categories_provider.dart';
import 'providers/cart_provider.dart';

// Screens
import 'screens/home_screen.dart';
import 'screens/login_screen.dart';
import 'screens/register_screen.dart';
import 'screens/categories_screen.dart';
import 'screens/product_list_screen.dart';
import 'screens/product_detail_screen.dart';
import 'screens/cart_screen.dart';
import 'screens/orders_screen.dart';
import 'screens/order_detail_screen.dart';
import 'screens/profile_screen.dart';

// Checkout Flow
import 'screens/address_screen.dart';
import 'screens/payment_screen.dart';
import 'screens/checkout_screen.dart';
import 'screens/order_success_screen.dart';
import 'screens/payment_success_screen.dart';

import 'models/product_model.dart';

void main() {
  runApp(const CustomerApp());
}

class CustomerApp extends StatelessWidget {
  const CustomerApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => ProductsProvider()),
        ChangeNotifierProvider(create: (_) => CategoriesProvider()),
        ChangeNotifierProvider(create: (_) => CartProvider()),
      ],
      child: MaterialApp(
        debugShowCheckedModeBanner: false,
        title: "E-Commerce Customer App",

        // ---------- FIXED UI THEME ----------
        theme: ThemeData(
          primarySwatch: Colors.deepPurple,
          scaffoldBackgroundColor: Colors.white,
          appBarTheme: const AppBarTheme(
            backgroundColor: Colors.white,
            foregroundColor: Colors.black87,
            elevation: 0,
          ),
          bottomNavigationBarTheme: const BottomNavigationBarThemeData(
            backgroundColor: Colors.white,
            selectedItemColor: Colors.deepPurple,
            unselectedItemColor: Colors.grey,
            showSelectedLabels: true,
            showUnselectedLabels: true,
          ),
          elevatedButtonTheme: ElevatedButtonThemeData(
  style: ElevatedButton.styleFrom(
    foregroundColor: Colors.white,    // <-- text color
    backgroundColor: Colors.deepPurple,
    textStyle: const TextStyle(
      fontWeight: FontWeight.bold,
    ),
    padding: const EdgeInsets.symmetric(vertical: 14),
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(10),
    ),
  ),
),

        ),

        initialRoute: '/login',

        // ---------- STATIC ROUTES ----------
        routes: {
          '/login': (_) => const LoginScreen(),
          '/register': (_) => const RegisterScreen(),

          '/home': (_) => const HomeScreen(),
          '/categories': (_) => const CategoriesScreen(),
          '/profile': (_) => const ProfileScreen(),

          '/cart': (_) => const CartScreen(),
          '/orders': (_) => const OrdersScreen(),

          '/address': (_) => const AddressScreen(),
          '/checkout': (_) => const CheckoutScreen(),
          '/paymentSuccess': (_) => const PaymentSuccessScreen(),
        },

        // ---------- ROUTES WITH ARGUMENTS ----------
        onGenerateRoute: (settings) {
          if (settings.name == '/productList') {
            final catId = settings.arguments as int;
            return MaterialPageRoute(
              builder: (_) => ProductListScreen(categoryId: catId),
            );
          }

          if (settings.name == '/product') {
            final product = settings.arguments as ProductModel;
            return MaterialPageRoute(
              builder: (_) => ProductDetailScreen(product: product),
            );
          }

          if (settings.name == '/orderDetail') {
            final id = settings.arguments as int;
            return MaterialPageRoute(
              builder: (_) => OrderDetailScreen(orderId: id),
            );
          }

          if (settings.name == '/payment') {
            final total = settings.arguments as double;
            return MaterialPageRoute(
              builder: (_) => PaymentScreen(totalAmount: total),
            );
          }

          if (settings.name == '/orderSuccess') {
            final orderId = settings.arguments as int;
            return MaterialPageRoute(
              builder: (_) => OrderSuccessScreen(orderId: orderId),
            );
          }

          return null;
        },
      ),
    );
  }
}
