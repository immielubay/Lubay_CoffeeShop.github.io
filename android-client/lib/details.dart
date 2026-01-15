import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'colors.dart';
import 'home.dart';
import 'login.dart';
import 'cart.dart';
import 'orders.dart';
import 'profile.dart';

class DetailsPage extends StatelessWidget {
  final String userEmail;
  final int userId;
  final String itemName;
  final String itemDescription;
  final double itemPrice;
  final String? itemImage;

  const DetailsPage({
    super.key,
    required this.userEmail,
    required this.userId,
    required this.itemName,
    required this.itemDescription,
    required this.itemPrice,
    this.itemImage,
  });

  Future<void> buyNow(BuildContext context) async {
    try {
      final response = await http.post(
        Uri.parse("http://cselec1.atwebpages.com/CoffeeShop/create_orders.php"),
        body: {
          "user_id": userId.toString(),
          "total_amount": itemPrice.toString(),
          "product_name": itemName,
          "quantity": "1",
          "price": itemPrice.toString(),
          "image": itemImage ?? "",
        },
      );

      final data = jsonDecode(response.body);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            data["success"] == true
                ? "Order placed successfully!"
                : (data["message"] ?? "Order failed."),
          ),
        ),
      );

      if (data["success"] == true) {
        // Optionally navigate to orders page
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => OrdersPage(userEmail: userEmail, userId: userId),
          ),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Error: $e")),
      );
    }
  }

  Future<void> addToCart(BuildContext context) async {
    try {
      final response = await http.post(
        Uri.parse("http://cselec1.atwebpages.com/CoffeeShop/add_to_cart.php"),
        body: {
          "user_id": userId.toString(),
          "item_name": itemName,
          "item_price": itemPrice.toString(),
          "item_image": itemImage ?? "",
        },
      );

      final data = jsonDecode(response.body);

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            data["success"] == true
                ? "Added to cart!"
                : (data["message"] ?? "Failed to add."),
          ),
        ),
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Error: $e")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.darkGray,
      appBar: AppBar(
        backgroundColor: AppColors.black,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: Row(
          children: [
            Image.asset("assets/logo.png", height: 40, width: 40),
            const SizedBox(width: 10),
            const Text(
              "Midnight Brew",
              style: TextStyle(
                color: AppColors.gold,
                fontWeight: FontWeight.bold,
                fontSize: 22,
              ),
            ),
          ],
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(gradient: AppColors.mainGradient),
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 20),
              Container(
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.15),
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black45.withOpacity(0.5),
                      blurRadius: 12,
                      spreadRadius: 2,
                    ),
                  ],
                ),
                padding: const EdgeInsets.all(20),
                child: ClipOval(
                  child: itemImage != null
                      ? Image.network(
                    itemImage!,
                    height: 150,
                    width: 150,
                    fit: BoxFit.cover,
                  )
                      : Container(
                    height: 150,
                    width: 150,
                    decoration: const BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.white24,
                    ),
                    child: const Icon(Icons.broken_image,
                        size: 60, color: Colors.white70),
                  ),
                ),
              ),
              const SizedBox(height: 20),
              Text(
                itemName,
                style: const TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
              const SizedBox(height: 10),
              Text(
                "₱${itemPrice.toStringAsFixed(2)}",
                style: const TextStyle(fontSize: 20, color: Colors.white70),
              ),
              const SizedBox(height: 30),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.gold,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  icon: const Icon(Icons.shopping_cart, color: AppColors.darkGray),
                  label: const Text(
                    "Add to Cart",
                    style: TextStyle(
                      color: AppColors.darkGray,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  onPressed: () => addToCart(context),
                ),
              ),
              const SizedBox(height: 12),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  icon: const Icon(Icons.payments, color: AppColors.black),
                  label: const Text(
                    "Buy Now",
                    style: TextStyle(
                      color: AppColors.black,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  onPressed: () => buyNow(context),
                ),
              ),
              const SizedBox(height: 40),
            ],
          ),
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        backgroundColor: AppColors.black,
        selectedItemColor: AppColors.gold,
        unselectedItemColor: Colors.white,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: "Home"),
          BottomNavigationBarItem(icon: Icon(Icons.shopping_cart), label: "Cart"),
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long), label: "Orders"),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: "Profile"),
          BottomNavigationBarItem(icon: Icon(Icons.logout), label: "Logout"),
        ],
        onTap: (index) {
          if (index == 0) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => DashboardPage(userEmail: userEmail, userId: userId)));
          } else if (index == 1) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => CartPage(userEmail: userEmail, userId: userId)));
          } else if (index == 2) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => OrdersPage(userEmail: userEmail, userId: userId)));
          } else if (index == 3) {
            Navigator.push(context, MaterialPageRoute(builder: (_) => ProfilePage(userEmail: userEmail, userId: userId)));
          } else if (index == 4) {
            Navigator.pushAndRemoveUntil(
              context,
              MaterialPageRoute(builder: (_) => const LoginPage()),
                  (route) => false,
            );
          }
        },
      ),
    );
  }
}
