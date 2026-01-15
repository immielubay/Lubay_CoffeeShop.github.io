  import 'package:flutter/material.dart';
  import 'colors.dart';
  import 'details.dart';
  import 'login.dart';
  import 'cart.dart';
  import 'orders.dart';
  import 'profile.dart';
  import 'dart:convert';
  import 'package:http/http.dart' as http;
  
  class DashboardPage extends StatefulWidget {
    final String userEmail;
    final int userId;
  
    const DashboardPage({super.key, required this.userEmail, required this.userId});
  
    @override
    State<DashboardPage> createState() => _DashboardPageState();
  }
  
  class _DashboardPageState extends State<DashboardPage> {
    bool isExpanded = false;
    String searchQuery = "";
    int _selectedIndex = 0;
  
    final ScrollController _drinkScroll = ScrollController();
    final ScrollController _breadScroll = ScrollController();
  
    List<Map<String, dynamic>> drinks = [];
    List<Map<String, dynamic>> breads = [];
  
    @override
    void initState() {
      super.initState();
      fetchMenu(); // Load menu from server
    }
  
    Future<void> fetchMenu() async {
      final url = Uri.parse("http://cselec1.atwebpages.com/CoffeeShop/get_menu.php");
  
      try {
        final response = await http.get(url);
  
        if (response.statusCode == 200) {
          final List items = jsonDecode(response.body);
  
          setState(() {
            drinks = items
                .where((item) => item["category"].toString().trim().toLowerCase() == "drinks")
                .toList()
                .cast<Map<String, dynamic>>();
  
            breads = items
                .where((item) => item["category"].toString().trim().toLowerCase() == "breads")
                .toList()
                .cast<Map<String, dynamic>>();
          });
        } else {
          setState(() {
            drinks = [];
            breads = [];
          });
        }
      } catch (e) {
        setState(() {
          drinks = [];
          breads = [];
        });
      }
    }
  
    void _onItemTapped(int index) {
      setState(() => _selectedIndex = index);
      switch (index) {
        case 0:
          break;
        case 1:
          Navigator.push(context, MaterialPageRoute(builder: (_) =>
              CartPage(userEmail: widget.userEmail, userId: widget.userId)));
          break;
        case 2:
          Navigator.push(context, MaterialPageRoute(builder: (_) =>
              OrdersPage(userEmail: widget.userEmail, userId: widget.userId)));
          break;
        case 3:
          Navigator.push(context, MaterialPageRoute(builder: (_) =>
              ProfilePage(userEmail: widget.userEmail, userId: widget.userId)));
          break;
        case 4:
          Navigator.pushAndRemoveUntil(
              context, MaterialPageRoute(builder: (_) => const LoginPage()), (
              route) => false);
          break;
      }
    }
  
    @override
    Widget build(BuildContext context) {
      final query = searchQuery.toLowerCase();
      final filteredDrinks = drinks.where((item) =>
          item["name"].toString().toLowerCase().contains(query)).toList();
      final filteredBreads = breads.where((item) =>
          item["name"].toString().toLowerCase().contains(query)).toList();
      final showDrinks = filteredDrinks.isNotEmpty;
      final showBreads = filteredBreads.isNotEmpty;
  
      return Scaffold(
        backgroundColor: AppColors.darkGray,
        body: CustomScrollView(
          slivers: [
            SliverToBoxAdapter(
              child: Container(
                decoration: BoxDecoration(
                  gradient: AppColors.mainGradient,
                  borderRadius: const BorderRadius.only(
                      bottomLeft: Radius.circular(40),
                      bottomRight: Radius.circular(40)),
                ),
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(24, 60, 24, 24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(children: [
                        Image.asset("assets/logo.png", height: 50,
                            width: 50,
                            fit: BoxFit.contain),
                        const SizedBox(width: 10),
                        const Text("Midnight Brew",
                            style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: Colors.white)),
                      ]),
                      const SizedBox(height: 15),
                      Container(
                        decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.9),
                            borderRadius: BorderRadius.circular(30)),
                        child: TextField(
                          onChanged: (value) =>
                              setState(() => searchQuery = value),
                          decoration: const InputDecoration(
                            hintText: "Search your favorite coffee or bread...",
                            border: InputBorder.none,
                            prefixIcon: Icon(Icons.search, color: Colors.black54),
                            contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                          ),
                        ),
                      ),
                      const SizedBox(height: 30),
                      Row(
                        children: [
                          Expanded(
                            flex: 5,
                            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: const [
                              Text("Your Daily Dose \nof Warmth ☕",
                                  style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Colors.white, height: 1.2)),
                              SizedBox(height: 15),
                              Text("Discover handcrafted brews and pastries made with love.",
                                  style: TextStyle(fontSize: 16, color: Colors.white70)),
                            ]),
                          ),
                          Expanded(
                            flex: 5,
                            child: Align(
                              alignment: Alignment.topRight,
                              child: ClipRRect(
                                  borderRadius: BorderRadius.circular(20),
                                  child: Image.asset("assets/coffee.png", height: 200, fit: BoxFit.cover)),
                            ),
                          ),
                        ],
                      ),
                      AnimatedCrossFade(
                        firstChild: const SizedBox.shrink(),
                        secondChild: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              const SizedBox(height: 40),
                              const Text(
                                "Welcome to Midnight Brew! Enjoy the cozy ambiance with freshly brewed coffee, artisan bread, and sweet pastries made daily.",
                                style: TextStyle(fontSize: 16, color: Colors.white70),
                              ),
                              const SizedBox(height: 20),
                              Image.asset("assets/coffee_love.png", height: 180, fit: BoxFit.cover),
                              const SizedBox(height: 20),
                            ]),
                        crossFadeState: isExpanded ? CrossFadeState.showSecond : CrossFadeState.showFirst,
                        duration: const Duration(milliseconds: 300),
                      ),
                      Center(
                        child: IconButton(
                          icon: Icon(isExpanded ? Icons.keyboard_arrow_up : Icons.keyboard_arrow_down, color: AppColors.gold, size: 32),
                          onPressed: () => setState(() => isExpanded = !isExpanded),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 25),
                child: Row(
                    mainAxisAlignment: MainAxisAlignment.center, children: const [
                  Icon(Icons.menu_book, color: Colors.white, size: 26),
                  SizedBox(width: 8),
                  Text("Menu", style: TextStyle(color: Colors.white, fontSize: 26, fontWeight: FontWeight.bold)),
                ]),
              ),
            ),
            if (showDrinks) _buildCategorySection("Drinks", filteredDrinks, _drinkScroll),
            if (showBreads) _buildCategorySection("Breads", filteredBreads, _breadScroll),
            if (!showDrinks && !showBreads)
              const SliverToBoxAdapter(
                child: Center(
                  child: Padding(
                    padding: EdgeInsets.all(40),
                    child: Text("No items found 😢", style: TextStyle(color: Colors.white70, fontSize: 18)),
                  ),
                ),
              ),
            const SliverToBoxAdapter(
              child: Padding(
                padding: EdgeInsets.only(top: 40, bottom: 60),
                child: Center(
                  child: Text("\"Where every sip feels like home.\"",
                      style: TextStyle(color: Colors.white70, fontStyle: FontStyle.italic, fontSize: 14)),
                ),
              ),
            ),
          ],
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
  
    Widget _buildCategorySection(String title, List<Map<String, dynamic>> items,
        ScrollController controller) {
      return SliverToBoxAdapter(
        child: StatefulBuilder(
          builder: (context, setStateArrow) {
            bool showLeft = false;
            bool showRight = true;
  
            if (!controller.hasListeners) {
              controller.addListener(() {
                final maxScroll = controller.position.maxScrollExtent;
                final offset = controller.offset;
  
                final leftVisible = offset > 0;
                final rightVisible = offset < maxScroll;
  
                if (showLeft != leftVisible || showRight != rightVisible) {
                  setStateArrow(() {
                    showLeft = leftVisible;
                    showRight = rightVisible;
                  });
                }
              });
            }
  
            return Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 12.0),
                  child: Row(children: [
                    Icon(title == "Drinks" ? Icons.local_cafe : Icons.bakery_dining,
                        color: Colors.white, size: 22),
                    const SizedBox(width: 8),
                    Text(title,
                        style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold)),
                  ]),
                ),
                SizedBox(
                  height: 210,
                  child: Stack(
                    children: [
                      ListView.separated(
                        controller: controller,
                        scrollDirection: Axis.horizontal,
                        padding: const EdgeInsets.only(left: 16, right: 16),
                        itemCount: items.length,
                        separatorBuilder: (_, __) => const SizedBox(width: 16),
                        itemBuilder: (context, index) {
                          final item = items[index];
                          final imageUrl = item['image'];
  
                          return GestureDetector(
                            onTap: () => Navigator.push(
                                context,
                                MaterialPageRoute(
                                    builder: (_) => DetailsPage(
                                      userEmail: widget.userEmail,
                                      userId: widget.userId,
                                      itemName: item["name"],
                                      itemDescription: item["description"],
                                      itemPrice: double.tryParse(item["price"].toString()) ?? 0.0,
                                      itemImage: imageUrl,
                                    ))),
                            child: Container(
                              width: 150,
                              decoration: BoxDecoration(
                                color: AppColors.gold,
                                borderRadius: BorderRadius.circular(20),
                                boxShadow: const [
                                  BoxShadow(color: Colors.black26, blurRadius: 6, offset: Offset(2, 2))
                                ],
                              ),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  ClipRRect(
                                    borderRadius: BorderRadius.circular(12),
                                    child: Image.network(
                                      imageUrl,  // use the full URL from the database
                                      height: 80,
                                      fit: BoxFit.cover,
                                      loadingBuilder: (context, child, loadingProgress) {
                                        if (loadingProgress == null) return child;
                                        return const SizedBox(
                                          height: 80,
                                          child: Center(child: CircularProgressIndicator(color: Colors.white70)),
                                        );
                                      },
                                      errorBuilder: (context, error, stackTrace) =>
                                      const Icon(Icons.broken_image, size: 80, color: Colors.white70),
                                    ),
                                  ),
                                  const SizedBox(height: 10),
                                  Text(item["name"],
                                      style: const TextStyle(
                                          color: AppColors.darkGray, fontSize: 16, fontWeight: FontWeight.bold)),
                                  const SizedBox(height: 5),
                                  Text("\$${double.tryParse(item["price"].toString())?.toStringAsFixed(2) ?? '0.00'}",
                                      style: const TextStyle(color: AppColors.darkGray, fontSize: 14)),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                      if (showLeft)
                        Align(
                          alignment: Alignment.centerLeft,
                          child: Container(
                            decoration: BoxDecoration(color: Colors.black38, shape: BoxShape.circle),
                            child: IconButton(
                              icon: const Icon(Icons.arrow_left, color: Colors.white, size: 32),
                              onPressed: () {
                                controller.animateTo(
                                  (controller.offset - 200).clamp(0.0, controller.position.maxScrollExtent),
                                  duration: const Duration(milliseconds: 300),
                                  curve: Curves.easeInOut,
                                );
                              },
                            ),
                          ),
                        ),
                      if (showRight)
                        Align(
                          alignment: Alignment.centerRight,
                          child: Container(
                            decoration: BoxDecoration(color: Colors.black38, shape: BoxShape.circle),
                            child: IconButton(
                              icon: const Icon(Icons.arrow_right, color: Colors.white, size: 32),
                              onPressed: () {
                                controller.animateTo(
                                  (controller.offset + 200).clamp(0.0, controller.position.maxScrollExtent),
                                  duration: const Duration(milliseconds: 300),
                                  curve: Curves.easeInOut,
                                );
                              },
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              ],
            );
          },
        ),
      );
    }
  }
