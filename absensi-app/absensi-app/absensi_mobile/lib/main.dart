import 'package:flutter/material.dart';
import 'screens/login_screen.dart';
import 'screens/home_screen.dart';
import 'services/api_service.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  try {
    await ApiService.initBaseUrl();
  } catch (_) {
    // Gunakan alamat default jika penyimpanan lokal belum tersedia.
  }
  final hasSession =
      await ApiService.getToken() != null && await ApiService.getUser() != null;
  runApp(AbsensiApp(hasSession: hasSession));
}

class AbsensiApp extends StatelessWidget {
  const AbsensiApp({super.key, this.hasSession = false});

  final bool hasSession;

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Absensi Sinar Alam',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFFDC1F35)),
        useMaterial3: true,
      ),
      home: hasSession ? const HomeScreen() : const LoginScreen(),
    );
  }
}
