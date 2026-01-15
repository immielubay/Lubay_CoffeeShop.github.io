import 'package:flutter/material.dart';

class AppColors {
  static const Color black = Color(0xFF000000);
  static const Color darkGray = Color(0xFF282A3A);
  static const Color brown = Color(0xFF735F32);
  static const Color gold = Color(0xFFC69749);
  static const Color white = Color(0xFFFFFFFF);

  static const LinearGradient mainGradient = LinearGradient(
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
    colors: [darkGray, brown, gold],
  );
}
