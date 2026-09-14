import 'package:flutter/material.dart';
import '../services/api_service.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});
  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _name = TextEditingController();
  final _username = TextEditingController();
  final _email = TextEditingController();
  final _area = TextEditingController();
  final _password = TextEditingController();
  final _confirm = TextEditingController();
  bool _loading = false;
  String? _error;

  Future<void> _submit() async {
    if ([_name, _username, _email, _area, _password, _confirm]
        .any((controller) => controller.text.trim().isEmpty)) {
      setState(() => _error = 'Semua field wajib diisi');
      return;
    }
    if (_password.text.length < 8 || _password.text != _confirm.text) {
      setState(() => _error = 'Password minimal 8 karakter dan harus sama');
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      await ApiService.register(
        username: _username.text.trim(),
        nama: _name.text.trim(),
        email: _email.text.trim(),
        password: _password.text,
        area: _area.text.trim(),
      );
      if (mounted)
        Navigator.pop(context, 'Akun berhasil dibuat. Silakan login.');
    } catch (e) {
      if (mounted)
        setState(() => _error = e.toString().replaceFirst('Exception: ', ''));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Buat akun')),
        body: ListView(padding: const EdgeInsets.all(24), children: [
          const Text('Akun baru dibuat sebagai Mandor Kebun.',
              style: TextStyle(color: Colors.grey)),
          const SizedBox(height: 18),
          for (final field in [
            (_name, 'Nama lengkap'),
            (_username, 'Username'),
            (_email, 'Email'),
            (_area, 'Area kerja'),
            (_password, 'Password'),
            (_confirm, 'Ulangi password'),
          ]) ...[
            TextField(
                controller: field.$1,
                obscureText: field.$2.contains('password'),
                decoration: InputDecoration(
                    labelText: field.$2, border: const OutlineInputBorder())),
            const SizedBox(height: 12),
          ],
          if (_error != null)
            Text(_error!, style: const TextStyle(color: Colors.red)),
          const SizedBox(height: 12),
          FilledButton(
              onPressed: _loading ? null : _submit,
              child: Text(_loading ? 'Menyimpan...' : 'Buat akun')),
        ]),
      );
}
