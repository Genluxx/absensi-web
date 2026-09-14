import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'home_screen.dart';
import 'register_screen.dart';
import 'forgot_password_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _usernameCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  bool _loading = false;
  bool _obscure = true;
  String? _error;

  static const biru = Color(0xFF2563EB);
  static const latar = Color(0xFF020817);
  static const panel = Color(0xFF0F172A);
  static const teksSekunder = Color(0xFF94A3B8);

  Future<void> _login() async {
    if (_usernameCtrl.text.trim().isEmpty || _passCtrl.text.isEmpty) {
      setState(() => _error = "Username dan password wajib diisi");
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final data =
          await ApiService.login(_usernameCtrl.text.trim(), _passCtrl.text);
      await ApiService.saveSession(data["token"], data["user"]);
      if (!mounted) return;
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (_) => const HomeScreen()),
      );
    } catch (e) {
      setState(() => _error = e.toString().replaceFirst("Exception: ", ""));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _quickLogin(String username, String password) {
    _usernameCtrl.text = username;
    _passCtrl.text = password;
    _login();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: latar,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Container(
              padding: const EdgeInsets.all(28),
              constraints: const BoxConstraints(maxWidth: 420),
              decoration: BoxDecoration(
                color: panel,
                borderRadius: BorderRadius.circular(28),
                border: Border.all(color: const Color(0xFF334155)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Row(
                    children: [
                      Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.12),
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: const Icon(Icons.fingerprint,
                            color: Colors.white, size: 28),
                      ),
                      const SizedBox(width: 12),
                      const Expanded(
                        child: Text(
                          "SAP.HRIS",
                          style: TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                              color: Colors.white),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    "SAWITA",
                    style: TextStyle(
                      color: teksSekunder,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                      letterSpacing: 1,
                    ),
                  ),
                  const SizedBox(height: 28),
                  const Text("USERNAME",
                      style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 12)),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _usernameCtrl,
                    style: const TextStyle(color: Colors.white),
                    decoration: InputDecoration(
                      hintText: "Masukkan username",
                      hintStyle: const TextStyle(color: teksSekunder),
                      prefixIcon:
                          const Icon(Icons.person_outline, color: teksSekunder),
                      filled: true,
                      fillColor: const Color(0xFF111827),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: biru, width: 2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 18),
                  const Text("PASSWORD",
                      style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 12)),
                  const SizedBox(height: 6),
                  TextField(
                    controller: _passCtrl,
                    style: const TextStyle(color: Colors.white),
                    obscureText: _obscure,
                    decoration: InputDecoration(
                      hintText: "Masukkan password",
                      hintStyle: const TextStyle(color: teksSekunder),
                      prefixIcon:
                          const Icon(Icons.lock_outline, color: teksSekunder),
                      suffixIcon: IconButton(
                        icon: Icon(
                            _obscure ? Icons.visibility_off : Icons.visibility,
                            color: teksSekunder),
                        onPressed: () => setState(() => _obscure = !_obscure),
                      ),
                      filled: true,
                      fillColor: const Color(0xFF111827),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: Color(0xFF334155)),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: biru, width: 2),
                      ),
                    ),
                  ),
                  if (_error != null) ...[
                    const SizedBox(height: 14),
                    Text(_error!,
                        style: const TextStyle(color: Colors.red),
                        textAlign: TextAlign.center),
                  ],
                  const SizedBox(height: 24),
                  SizedBox(
                    height: 52,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: biru,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14)),
                      ),
                      onPressed: _loading ? null : _login,
                      child: _loading
                          ? const SizedBox(
                              width: 22,
                              height: 22,
                              child: CircularProgressIndicator(
                                  strokeWidth: 2, color: Colors.white))
                          : const Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text("Masuk ke Dashboard",
                                    style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold)),
                                SizedBox(width: 8),
                                Icon(Icons.arrow_forward, size: 18),
                              ],
                            ),
                    ),
                  ),
                  const SizedBox(height: 14),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      TextButton(
                        onPressed: _loading
                            ? null
                            : () async {
                                final message = await Navigator.push<String>(
                                    context,
                                    MaterialPageRoute(
                                        builder: (_) =>
                                            const ForgotPasswordScreen()));
                                if (message != null && mounted)
                                  setState(() => _error = message);
                              },
                        child: const Text('Lupa password?'),
                      ),
                      TextButton(
                        onPressed: _loading
                            ? null
                            : () async {
                                final message = await Navigator.push<String>(
                                    context,
                                    MaterialPageRoute(
                                        builder: (_) =>
                                            const RegisterScreen()));
                                if (message != null && mounted)
                                  setState(() => _error = message);
                              },
                        child: const Text('Buat akun'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  const Divider(color: Color(0xFF334155)),
                  const SizedBox(height: 12),
                  const Text(
                    "Akun untuk testing:",
                    textAlign: TextAlign.center,
                    style: TextStyle(
                        color: teksSekunder,
                        fontSize: 12,
                        fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 10),
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    alignment: WrapAlignment.center,
                    children: [
                      _demoButton("Super Admin", "superadmin", "superadmin123",
                          const Color(0xFF172554), const Color(0xFF93C5FD)),
                      _demoButton("Admin HR", "adminhr", "password123",
                          const Color(0xFF1E293B), const Color(0xFFCBD5E1)),
                      _demoButton("Mandor Kebun", "mandorkebun1", "password123",
                          const Color(0xFF164E63), const Color(0xFF67E8F9)),
                      _demoButton(
                          "Mandor Pabrik",
                          "mandorpabrik1",
                          "password123",
                          const Color(0xFF172554),
                          const Color(0xFF93C5FD)),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _demoButton(
      String label, String username, String password, Color bg, Color fg) {
    return OutlinedButton(
      style: OutlinedButton.styleFrom(
        backgroundColor: bg,
        foregroundColor: fg,
        side: BorderSide.none,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
      onPressed: _loading ? null : () => _quickLogin(username, password),
      child: Text(label,
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
    );
  }
}
