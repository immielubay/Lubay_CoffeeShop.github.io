import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'colors.dart';
import 'home.dart';
import 'cart.dart';
import 'profile.dart';
import 'login.dart';

class OrdersPage extends StatefulWidget {
  final String userEmail;
  final int userId;

  const OrdersPage({super.key, required this.userEmail, required this.userId});

  @override
  State<OrdersPage> createState() => _OrdersPageState();
}

class _OrdersPageState extends State<OrdersPage> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  int _selectedIndex = 2;

  List<Map<String, dynamic>> orders = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 5, vsync: this);
    fetchOrders();
  }

  Future<void> fetchOrders() async {
    setState(() => isLoading = true);
    try {
      final response = await http.get(Uri.parse(
          "http://cselec1.atwebpages.com/CoffeeShop/get_orders.php?user_id=${widget.userId}"));
      final data = jsonDecode(response.body);

      if (data["success"] == true) {
        setState(() {
          orders = List<Map<String, dynamic>>.from(data["orders"]);
          isLoading = false;
        });
      } else {
        setState(() => isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(data["message"] ?? "Failed to fetch orders")),
        );
      }
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Error: $e")),
      );
    }
  }

  List<Map<String, dynamic>> filterOrders(String status) {
    if (status == "All") return orders;
    return orders.where((o) => o["status"] == status.toLowerCase()).toList();
  }

  void markAsReceived(int orderId) async {
    try {
      final response = await http.post(
        Uri.parse("http://cselec1.atwebpages.com/CoffeeShop/mark_received.php"),
        body: {"order_id": orderId.toString()},
      );
      final data = jsonDecode(response.body);

      if (data["success"] == true) {
        fetchOrders();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text("Order marked as received!"),
            backgroundColor: AppColors.gold,
          ),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Error: $e")),
      );
    }
  }

  void _onItemTapped(int index) {
    setState(() => _selectedIndex = index);
    switch (index) {
      case 0:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
              builder: (_) =>
                  DashboardPage(userEmail: widget.userEmail, userId: widget.userId)),
        );
        break;
      case 1:
        Navigator.push(
          context,
          MaterialPageRoute(
              builder: (_) =>
                  CartPage(userEmail: widget.userEmail, userId: widget.userId)),
        );
        break;
      case 2:
        break;
      case 3:
        Navigator.push(
          context,
          MaterialPageRoute(
              builder: (_) =>
                  ProfilePage(userEmail: widget.userEmail, userId: widget.userId)),
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

  Widget buildOrderList(String status) {
    final filtered = filterOrders(status);

    if (isLoading) {
      return const Center(child: CircularProgressIndicator(color: AppColors.gold));
    }

    if (filtered.isEmpty) {
      return const Center(
        child: Text(
          "No orders found",
          style: TextStyle(color: Colors.white70, fontSize: 16),
        ),
      );
    }

    return ListView.builder(
      itemCount: filtered.length,
      itemBuilder: (context, index) {
        final order = filtered[index];
        final items = List<Map<String, dynamic>>.from(order["items"]);
        final isShipped = order["status"].toString().toLowerCase() == "shipped";

        return Card(
          color: Colors.white.withOpacity(0.9),
          margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
          child: ExpansionTile(
            title: Text(
              "${order["order_code"]} - ${order["status"].toString().toUpperCase()}",
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
            children: [
              ...items.map((item) {
                return ListTile(
                  leading: item["image"] != null && item["image"] != ""
                      ? Image.network(item["image"], width: 50, height: 50, fit: BoxFit.cover)
                      : const Icon(Icons.broken_image, size: 50),
                  title: Text(item["product_name"]),
                  subtitle: Text("Qty: ${item["quantity"]} | ₱${item["price"]}"),
                );
              }).toList(),
              if (isShipped)
                Padding(
                  padding: const EdgeInsets.only(bottom: 8.0),
                  child: TextButton.icon(
                    icon: const Icon(Icons.check, color: Colors.green),
                    label: const Text("Mark as Received", style: TextStyle(color: Colors.green)),
                    onPressed: () => markAsReceived(order["id"]),
                  ),
                ),
            ],
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.darkGray,
      appBar: AppBar(
        backgroundColor: AppColors.black,
        centerTitle: true,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          "My Orders",
          style: TextStyle(color: AppColors.gold, fontWeight: FontWeight.bold),
        ),
        bottom: TabBar(
          controller: _tabController,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          indicatorColor: AppColors.gold,
          tabs: const [
            Tab(text: "All"),
            Tab(text: "Unpaid"),
            Tab(text: "Paid"),
            Tab(text: "Shipped"),
            Tab(text: "Received"),
          ],
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(gradient: AppColors.mainGradient),
        child: TabBarView(
          controller: _tabController,
          children: [
            buildOrderList("All"),
            buildOrderList("unpaid"),
            buildOrderList("paid"),
            buildOrderList("shipped"),
            buildOrderList("received"),
          ],
        ),
      ),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: _onItemTapped,
        backgroundColor: AppColors.black,
        selectedItemColor: AppColors.gold,
        unselectedItemColor: Colors.white70,
        type: BottomNavigationBarType.fixed,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home), label: "Home"),
          BottomNavigationBarItem(icon: Icon(Icons.shopping_cart), label: "Cart"),
          BottomNavigationBarItem(icon: Icon(Icons.receipt_long), label: "Orders"),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: "Profile"),
          BottomNavigationBarItem(icon: Icon(Icons.logout), label: "Logout"),
        ],
      ),
    );
  }
}
