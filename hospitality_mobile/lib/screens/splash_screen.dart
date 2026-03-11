import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../main.dart'; // For AuthWrapper

class SplashScreen extends StatelessWidget {
  const SplashScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Navigate to AuthWrapper (which handles Login vs Home) after 3 seconds
    Future.delayed(const Duration(seconds: 3), () {
      if (context.mounted) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const AuthWrapper()),
        );
      }
    });

    return Scaffold(
      body: Stack(
        children: [
          // Background Image
          Positioned.fill(
            child: Image.asset(
              'assets/stayloops_hero.jpg',
              fit: BoxFit.cover,
            )
                .animate()
                .fadeIn(duration: 800.ms),
          ),

          // Dark Gradient Overlay
          Positioned.fill(
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    Colors.black.withOpacity(0.5),
                    Colors.black.withOpacity(0.2),
                  ],
                  begin: Alignment.bottomCenter,
                  end: Alignment.topCenter,
                ),
              ),
            ),
          ),

          // Logo Animation
          Center(
            child: Text(
              'StayLoops',
              style: const TextStyle(
                fontSize: 42,
                fontWeight: FontWeight.w700,
                color: Colors.white,
                letterSpacing: 1.2,
                fontFamily: 'Outfit', // Assuming use of Outfit font from Google Fonts package if available, else standard
              ),
            )
                .animate()
                .scale(
                  begin: const Offset(0.85, 0.85),
                  end: const Offset(1, 1),
                  duration: 800.ms,
                  curve: Curves.easeOut,
                )
                .fadeIn(duration: 800.ms)
                .then()
                .moveY(
                  begin: 0,
                  end: -6,
                  duration: 1200.ms,
                  curve: Curves.easeInOut,
                )
                .then()
                .moveY(
                  begin: -6,
                  end: 0,
                  duration: 1200.ms,
                  curve: Curves.easeInOut,
                ),
          ),
        ],
      ),
    );
  }
}
