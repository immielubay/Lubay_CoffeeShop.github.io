import 'package:flutter/material.dart';
import 'colors.dart';

class CafeDashboard extends StatelessWidget {
  const CafeDashboard({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.darkGray,
      appBar: AppBar(
        title: const Text(
          "Café Dashboard",
          style: TextStyle(color: AppColors.gold, fontWeight: FontWeight.bold),
        ),
        backgroundColor: AppColors.black,
        centerTitle: true,
      ),
      body: Container(
        decoration: const BoxDecoration(gradient: AppColors.mainGradient),
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 12),
              const Text(
                "Welcome back, Barista!",
                style: TextStyle(
                  color: AppColors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 24),

              /// SALES OVERVIEW
              _buildSectionTitle("Sales Overview"),
              _buildInfoCards([
                _infoCard(Icons.attach_money, "Today’s Sales", "\$1,245"),
                _infoCard(Icons.trending_up, "This Week", "\$8,760"),
                _infoCard(Icons.calendar_today, "This Month", "\$31,420"),
              ]),
              const SizedBox(height: 20),

              /// TOP SELLING ITEMS
              _buildSectionTitle("Top Selling Items"),
              _buildListItems([
                {"name": "Iced Latte", "sales": "120 sold"},
                {"name": "Cappuccino", "sales": "95 sold"},
                {"name": "Mocha", "sales": "80 sold"},
              ]),
              const SizedBox(height: 20),

              /// INVENTORY STATUS
              _buildSectionTitle("Inventory Status"),
              _buildProgress("Coffee Beans", 0.8),
              _buildProgress("Milk", 0.6),
              _buildProgress("Cups", 0.4),
              const SizedBox(height: 20),

              /// CUSTOMER INSIGHTS
              _buildSectionTitle("Customer Insights"),
              _buildInfoCards([
                _infoCard(Icons.people, "Customers Today", "86"),
                _infoCard(Icons.favorite, "Returning Customers", "42%"),
                _infoCard(Icons.star, "Average Rating", "4.7 ★"),
              ]),
              const SizedBox(height: 20),

              /// STAFF OVERVIEW
              _buildSectionTitle("Staff Overview"),
              _buildListItems([
                {"name": "Liza – Barista", "sales": "Shift: Morning"},
                {"name": "Carlo – Cashier", "sales": "Shift: Afternoon"},
                {"name": "Mia – Server", "sales": "Shift: Evening"},
              ]),
              const SizedBox(height: 20),

              /// ORDERS & DELIVERIES
              _buildSectionTitle("Orders & Deliveries"),
              _buildInfoCards([
                _infoCard(Icons.receipt_long, "Pending Orders", "12"),
                _infoCard(Icons.check_circle, "Completed", "43"),
                _infoCard(Icons.timer, "Avg. Prep Time", "6 min"),
              ]),
              const SizedBox(height: 20),

              /// REVENUE BREAKDOWN
              _buildSectionTitle("Revenue Breakdown"),
              _buildListItems([
                {"name": "Dine-in", "sales": "54%"},
                {"name": "Takeout", "sales": "32%"},
                {"name": "Delivery", "sales": "14%"},
              ]),
              const SizedBox(height: 30),

              /// PROMOTIONS
              _buildSectionTitle("Active Promotions"),
              _buildListItems([
                {"name": "Buy 1 Get 1 Free – Latte", "sales": "Ends Oct 31"},
                {"name": "10% Off on Cappuccino", "sales": "All Week"},
              ]),
              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }

  // Section Title
  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Text(
        title,
        style: const TextStyle(
          color: AppColors.gold,
          fontSize: 20,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  // Row of Cards
  Widget _buildInfoCards(List<Widget> cards) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: cards
            .map((card) => Padding(
          padding: const EdgeInsets.only(right: 12),
          child: card,
        ))
            .toList(),
      ),
    );
  }

  // Single Card
  Widget _infoCard(IconData icon, String title, String value) {
    return Container(
      width: 150,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.darkGray.withOpacity(0.9),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.3),
            blurRadius: 6,
            offset: const Offset(2, 4),
          )
        ],
      ),
      child: Column(
        children: [
          Icon(icon, color: AppColors.gold, size: 36),
          const SizedBox(height: 12),
          Text(
            title,
            style: const TextStyle(
              color: AppColors.white,
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 6),
          Text(
            value,
            style: const TextStyle(
              color: AppColors.gold,
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  // Simple list (for items or staff)
  Widget _buildListItems(List<Map<String, String>> items) {
    return Column(
      children: items
          .map(
            (item) => Container(
          margin: const EdgeInsets.symmetric(vertical: 6),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: AppColors.darkGray.withOpacity(0.8),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(item["name"]!,
                  style: const TextStyle(
                      color: AppColors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.w500)),
              Text(item["sales"]!,
                  style: const TextStyle(
                      color: AppColors.gold,
                      fontWeight: FontWeight.bold)),
            ],
          ),
        ),
      )
          .toList(),
    );
  }

  // Inventory Progress Bar
  Widget _buildProgress(String item, double value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(item,
              style: const TextStyle(color: AppColors.white, fontSize: 16)),
          const SizedBox(height: 6),
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: LinearProgressIndicator(
              value: value,
              minHeight: 10,
              backgroundColor: AppColors.black,
              color: AppColors.gold,
            ),
          ),
        ],
      ),
    );
  }
}
