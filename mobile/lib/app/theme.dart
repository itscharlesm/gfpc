import 'package:flutter/material.dart';

class AppTheme {
  static const Color primaryRed = Color(0xFFFF1A1A);
  static const Color white = Colors.white;
  static const Color black = Color(0xFF1C1C1C);
  static const Color gray = Color(0xFF8A8A8A);
  static const Color lightGray = Color(0xFFF5F5F5);
  static const Color borderGray = Color(0xFFE0E0E0);

  // CLEAN FLAT CONTAINER (matches input fields)
  static BoxDecoration cardDecoration = BoxDecoration(
    color: white,
    borderRadius: BorderRadius.circular(12),
    border: Border.all(
      color: borderGray,
      width: 1,
    ),
  );

  // LIGHT SECTION (for backgrounds)
  static BoxDecoration softCardDecoration = BoxDecoration(
    color: lightGray,
    borderRadius: BorderRadius.circular(12),
  );

  static BoxDecoration borderedCardDecoration = BoxDecoration(
    color: white,
    borderRadius: BorderRadius.circular(14),
    border: Border.all(
      color: borderGray,
      width: 1,
    ),
  );

  static BoxDecoration headerDecoration = BoxDecoration(
    color: white,
    border: Border(
      bottom: BorderSide(
        color: borderGray.withOpacity(0.8),
        width: 1,
      ),
    ),
    boxShadow: [
      BoxShadow(
        color: Colors.black.withOpacity(0.025),
        blurRadius: 8,
        offset: const Offset(0, 2),
      ),
    ],
  );

  static ThemeData lightTheme = ThemeData(
    scaffoldBackgroundColor: white,

    colorScheme: ColorScheme.fromSeed(
      seedColor: primaryRed,
      primary: primaryRed,
      secondary: black,
      surface: white,
    ),

    textTheme: const TextTheme(
      titleLarge: TextStyle(
        fontSize: 26,
        fontWeight: FontWeight.bold,
        color: black,
      ),
      bodyMedium: TextStyle(
        fontSize: 16,
        color: black,
      ),
      bodySmall: TextStyle(
        fontSize: 14,
        color: gray,
      ),
    ),

    // PRIMARY BUTTON
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: primaryRed,
        foregroundColor: white,
        minimumSize: const Size(double.infinity, 50),
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
        ),
        textStyle: const TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.w700,
        ),
      ),
    ),

    // SECONDARY BUTTON
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        backgroundColor: white,
        foregroundColor: const Color.fromARGB(255, 7, 1, 1),
        minimumSize: const Size(double.infinity, 50),
        side: const BorderSide(
          color: primaryRed,
          width: 1.4,
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
        ),
        textStyle: const TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.w700,
        ),
      ),
    ),

    // TEXT BUTTON
    textButtonTheme: TextButtonThemeData(
      style: TextButton.styleFrom(
        foregroundColor: primaryRed,
        textStyle: const TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w600,
        ),
      ),
    ),

    // INPUT FIELDS
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: lightGray,
      contentPadding: const EdgeInsets.symmetric(
        horizontal: 16,
        vertical: 14,
      ),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide.none,
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide.none,
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(
          color: black,
          width: 1.5,
        ),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(
          color: Colors.red,
          width: 1.5,
        ),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(
          color: Colors.red,
          width: 2,
        ),
      ),
      labelStyle: const TextStyle(
        color: gray,
      ),
      hintStyle: const TextStyle(
        color: Color(0xFF9E9E9E),
      ),
    ),

    appBarTheme: const AppBarTheme(
      backgroundColor: primaryRed,
      foregroundColor: white,
      elevation: 0,
      centerTitle: true,
    ),
  );
}

class LoadingButton extends StatelessWidget {
  final bool isLoading;
  final VoidCallback? onPressed;
  final Widget child;

  const LoadingButton({
    super.key,
    required this.isLoading,
    required this.onPressed,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: ElevatedButton(
        onPressed: isLoading ? null : onPressed,
        child: isLoading
            ? const SizedBox(
                height: 20,
                width: 20,
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                ),
              )
            : child,
      ),
    );
  }
}
