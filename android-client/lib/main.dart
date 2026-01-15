import 'package:flutter/material.dart';
import 'login.dart';
import 'colors.dart';

void main() {
  runApp(const CoffeeShopApp());
}

class CoffeeShopApp extends StatelessWidget {
  const CoffeeShopApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Coffee Shop App',
      theme: ThemeData(
        scaffoldBackgroundColor: AppColors.darkGray,
        fontFamily: 'Poppins',
        colorScheme: ColorScheme.fromSeed(seedColor: AppColors.brown),
        useMaterial3: true,
      ),
      home: const LoginPage(),
    );
  }
}
