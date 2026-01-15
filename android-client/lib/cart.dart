import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'colors.dart';
import 'home.dart';
import 'orders.dart';
import 'profile.dart';
import 'login.dart';

class CartPage extends StatefulWidget {
  final String userEmail;
  final int userId;

  const CartPage({super.key, required this.userEmail, required this.userId});

  @override
  State<CartPage> createState() => _CartPageState();
}

class _CartPageState extends State<CartPage> with SingleTickerProviderStateMixin {
  List<Map<String, dynamic>> cartItems = [];
  int _currentIndex = 1;
  bool isLoading = true;
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 400),
    );
    fetchCartItems();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  /// 🟤 Fetch cart items from server
  Future<void> fetchCartItems() async {
    setState(() => isLoading = true);

    try {
      final response = await http.post(
        Uri.parse("http://cselec1.atwebpages.com/CoffeeShop/get_cart.php"),
        body: {"user_id": widget.userId.toString()},
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data["success"] == true) {
          setState(() {
            cartItems = List<Map<String, dynamic>>.from(data["cart"]);
          });
          _controller.forward(); // animate
        } else {
          debugPrint("Fetch error: ${data["message"]}");
        }
      } else {
        debugPrint("Server error: ${response.statusCode}");
      }
    } catch (e) {
      debugPrint("Error fetching cart: $e");
    } finally {
      setState(() => isLoading = false);
    }
  }

  /// 🟤 Remove item locally
  void removeItem(int index) {
    setState(() {
      cartItems.removeAt(index);
    });
  }

  /// 🟤 Checkout all items
  Future<void> checkout() async {
    if (cartItems.isEmpty) return;

    double totalAmount = cartItems.fold(
      0,
          (sum, item) => sum + double.parse(item["item_price"].toString()),
    );

    try {
      final response = await http.post(
        Uri.parse("http://cselec1.atwebpages.com/CoffeeShop/create_orders.php"),
        headers: {"Content-Type": "application/json"},
        body: jsonEncode({
          "user_id": widget.userId,
          "total_amount": totalAmount,
          "items": cartItems // send all products!
              .map((item) => {
            "product_id": item["product_id"],
            "quantity": 1, // or item["qty"]
            "price": item["item_price"]
          })
              .toList(),
        }),
      );

      final data = json.decode(response.body);

      if (data["success"] == true) {
        setState(() => cartItems.clear());
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text("✅ Order placed successfully!"),
            backgroundColor: Colors.green,
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("❌ ${data["message"]}"),
            backgroundColor: Colors.redAccent,
          ),
        );
      }
    } catch (e) {
      debugPrint("Checkout error: $e");
    }
  }

  /// 🟤 Navigation bar tap handling
  void onTabTapped(int index) {
    setState(() => _currentIndex = index);
    switch (index) {
      case 0:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) =>
                DashboardPage(userEmail: widget.userEmail, userId: widget.userId),
          ),
        );
        break;
      case 1:
        break; // Cart
      case 2:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) =>
                OrdersPage(userEmail: widget.userEmail, userId: widget.userId),
          ),
        );
        break;
      case 3:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (_) =>
                ProfilePage(userEmail: widget.userEmail, userId: widget.userId),
          ),
        );
        break;
      case 4:
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (_) => const LoginPage()),
              (route) => false,
        );
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    double total = cartItems.fold(
      0,
          (sum, item) => sum + double.parse(item["item_price"].toString()),
    );

    return Scaffold(
      backgroundColor: AppColors.darkGray,
      appBar: AppBar(
        backgroundColor: AppColors.black,
        centerTitle: true,
        iconTheme: const IconThemeData(
          color: Colors.white, // 🟢 Makes back arrow white
        ),
        title: const Text(
          "My Cart",
          style: TextStyle(color: AppColors.gold, fontWeight: FontWeight.bold),
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(gradient: AppColors.mainGradient),
        child: isLoading
            ? const Center(
          child: CircularProgressIndicator(color: AppColors.gold),
        )
            : cartItems.isEmpty
            ? const Center(
          child: Text(
            "Your cart is empty ☕",
            style: TextStyle(color: Colors.white70, fontSize: 18),
          ),
        )
            : ListView.builder(
          padding: const EdgeInsets.all(16),
          itemCount: cartItems.length,
          itemBuilder: (context, index) {
            final item = cartItems[index];
            return ScaleTransition(
              scale: CurvedAnimation(
                parent: _controller,
                curve: Curves.easeOutBack,
              ),
              child: Card(
                color: Colors.white.withOpacity(0.9),
                margin: const EdgeInsets.symmetric(vertical: 8),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                child: ListTile(
                  leading: Image.network(
                    item["item_image"] ?? "",
                    height: 50,
                    errorBuilder: (context, _, __) => const Icon(
                      Icons.local_cafe,
                      color: AppColors.gold,
                      size: 30,
                    ),
                  ),
                  title: Text(
                    item["item_name"],
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                    ),
                  ),
                  subtitle: Text(
                    "₱${item["item_price"]}",
                    style: const TextStyle(color: Colors.black87),
                  ),
                  trailing: IconButton(
                    icon: const Icon(
                      Icons.delete,
                      color: Colors.redAccent,
                    ),
                    onPressed: () => removeItem(index),
                  ),
                ),
              ),
            );
          },
        ),
      ),
      bottomSheet: cartItems.isEmpty
          ? null
          : Container(
        padding: const EdgeInsets.all(16),
        decoration: const BoxDecoration(
          color: Colors.black87,
          borderRadius: BorderRadius.only(
            topLeft: Radius.circular(20),
            topRight: Radius.circular(20),
          ),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              "Total: ₱${total.toStringAsFixed(2)}",
              style: const TextStyle(
                color: AppColors.gold,
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.gold,
                padding: const EdgeInsets.symmetric(
                  vertical: 12,
                  horizontal: 20,
                ),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              onPressed: checkout,
              child: const Text(
                "Checkout",
                style: TextStyle(
                  color: AppColors.darkGray,
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        onTap: onTabTapped,
        selectedItemColor: AppColors.gold,
        unselectedItemColor: Colors.white70,
        backgroundColor: AppColors.black,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.shopping_cart), label: 'Cart'),
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long), label: "Orders"),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profile'),
          BottomNavigationBarItem(icon: Icon(Icons.logout), label: 'Logout'),
        ],
      ),
    );
  }
}
