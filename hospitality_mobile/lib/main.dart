import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:provider/provider.dart' as provider;
import 'package:google_fonts/google_fonts.dart';
import 'providers/auth_provider.dart';
import 'providers/riverpod_providers.dart';
import 'screens/login_screen.dart';
import 'screens/main_layout.dart';

void main() {
  runApp(
    const ProviderScope(
      child: MyApp(),
    ),
  );
}

class MyApp extends ConsumerWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // Watch the Riverpod providers to keep the instances alive and updated
    final auth = ref.watch(authProvider);
    final dashboard = ref.watch(dashboardProvider);
    final booking = ref.watch(bookingProvider);
    final property = ref.watch(propertyProvider);
    final b2b = ref.watch(b2bProvider);
    final guest = ref.watch(guestProvider);

    return provider.MultiProvider(
      providers: [
        provider.ChangeNotifierProvider<AuthProvider>.value(value: auth),
        provider.ChangeNotifierProvider.value(value: dashboard),
        provider.ChangeNotifierProvider.value(value: booking),
        provider.ChangeNotifierProvider.value(value: property),
        provider.ChangeNotifierProvider.value(value: b2b),
        provider.ChangeNotifierProvider.value(value: guest),
      ],
      child: MaterialApp(
        title: 'Hospitality Management',
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          useMaterial3: true,
          colorScheme: ColorScheme.fromSeed(
            seedColor: Colors.blue,
            primary: Colors.blue,
            secondary: Colors.blueAccent,
          ),
          textTheme: GoogleFonts.poppinsTextTheme(),
        ),
        home: const AuthWrapper(),
      ),
    );
  }
}

class AuthWrapper extends ConsumerWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final auth = ref.watch(authProvider);
    
    if (auth.isAuthenticated) {
      return const MainLayout();
    }
    return const LoginScreen();
  }
}
