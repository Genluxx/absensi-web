import 'package:flutter/material.dart';
import '../services/api_service.dart';

class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});
  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  final _email = TextEditingController();
  final _token = TextEditingController();
  final _password = TextEditingController();
  bool _loading = false;
  String? _message;
  String? _error;

  Future<void> _send() async {
    if (_email.text.trim().isEmpty)
      return setState(() => _error = 'Email wajib diisi');
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      await ApiService.requestPasswordReset(_email.text.trim());
      if (mounted)
        setState(() => _message =
            'Link reset sudah dikirim ke email. Masukkan token dari link jika reset melalui aplikasi.');
    } catch (e) {
      if (mounted)
        setState(() => _error = e.toString().replaceFirst('Exception: ', ''));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _reset() async {
    if (_token.text.trim().isEmpty || _password.text.length < 8)
      return setState(
          () => _error = 'Token dan password minimal 8 karakter wajib diisi');
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      await ApiService.resetPassword(
          email: _email.text.trim(),
          token: _token.text.trim(),
          password: _password.text);
      if (mounted) Navigator.pop(context, 'Password berhasil diubah.');
    } catch (e) {
      if (mounted)
        setState(() => _error = e.toString().replaceFirst('Exception: ', ''));
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Lupa password')),
        body: ListView(padding: const EdgeInsets.all(24), children: [
          const Text('Masukkan email akun untuk menerima link reset.'),
          const SizedBox(height: 16),
          TextField(
              controller: _email,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(
                  labelText: 'Email', border: OutlineInputBorder())),
          const SizedBox(height: 12),
          FilledButton(
              onPressed: _loading ? null : _send,
              child: const Text('Kirim link reset')),
          const Divider(height: 32),
          TextField(
              controller: _token,
              decoration: const InputDecoration(
                  labelText: 'Token dari link email',
                  border: OutlineInputBorder())),
          const SizedBox(height: 12),
          TextField(
              controller: _password,
              obscureText: true,
              decoration: const InputDecoration(
                  labelText: 'Password baru', border: OutlineInputBorder())),
          const SizedBox(height: 12),
          OutlinedButton(
              onPressed: _loading ? null : _reset,
              child: const Text('Simpan password baru')),
          if (_message != null)
            Padding(
                padding: const EdgeInsets.only(top: 12),
                child: Text(_message!,
                    style: const TextStyle(color: Colors.green))),
          if (_error != null)
            Padding(
                padding: const EdgeInsets.only(top: 12),
                child:
                    Text(_error!, style: const TextStyle(color: Colors.red))),
        ]),
      );
}
