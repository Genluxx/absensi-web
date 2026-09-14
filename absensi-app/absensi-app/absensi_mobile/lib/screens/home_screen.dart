import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'login_screen.dart';
import 'dashboard_screen.dart';
import 'input_presensi_screen.dart';
import 'log_screen.dart';
import 'mandor_screen.dart';
import 'karyawan_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen>
    with SingleTickerProviderStateMixin {
  static const biru = Color(0xFF2563EB);
  static const latar = Color(0xFF020817);
  static const teksSekunder = Color(0xFF94A3B8);

  TabController? _tabController;
  Map<String, dynamic>? _user;
  List<String> _permissions = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadUser();
  }

  Future<void> _loadUser() async {
    final user = await ApiService.getUser();
    final permissions = await ApiService.getPermissions();

    setState(() {
      _user = user;
      _permissions = permissions;
      _tabController = TabController(length: _hitungJumlahTab(), vsync: this);
      _loading = false;
    });
  }

  bool _punya(String kode) => _permissions.contains(kode);

  bool get _bisaKelolaKaryawan =>
      _punya("karyawan.view") ||
      _punya("karyawan.create") ||
      _punya("karyawan.update") ||
      _punya("karyawan.delete");

  int _hitungJumlahTab() {
    int jumlah = 1; // Dashboard selalu ada
    if (_permissions.contains("presensi.input")) jumlah++;
    if (_permissions.contains("presensi.view.own") ||
        _permissions.contains("presensi.view.all")) {
      jumlah++;
    }
    if (_bisaKelolaKaryawan) {
      jumlah++;
    }
    if (_permissions.contains("mandor.view")) jumlah++;
    return jumlah;
  }

  Future<void> _logout() async {
    await ApiService.logout();
    if (!mounted) return;
    Navigator.of(context).pushAndRemoveUntil(
      MaterialPageRoute(builder: (_) => const LoginScreen()),
      (route) => false,
    );
  }

  String _labelRole(String? roleKode) {
    switch (roleKode) {
      case "super_admin":
        return "Super Admin";
      case "admin_hr":
        return "Admin HR";
      case "mandor_kebun":
        return "Mandor Kebun";
      case "mandor_pabrik":
        return "Mandor Pabrik";
      default:
        return roleKode ?? "-";
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_loading || _tabController == null) {
      return const Scaffold(
        backgroundColor: latar,
        body: Center(child: CircularProgressIndicator(color: biru)),
      );
    }

    final nama = _user?["nama"] ?? "...";
    final roleKode = _user?["roleKode"] ?? _user?["role"];
    final role = _labelRole(roleKode);
    final area = _user?["area"] ?? "Head Office / All";

    // Bangun daftar tab & halaman sesuai permission yang dipunya
    final List<Tab> tabs = [const Tab(text: "Dashboard")];
    final List<Widget> views = [const DashboardScreen()];

    if (_punya("presensi.input")) {
      tabs.add(const Tab(text: "Input Presensi"));
      views.add(const InputPresensiScreen());
    }
    if (_punya("presensi.view.own") || _punya("presensi.view.all")) {
      tabs.add(const Tab(text: "Log & Laporan"));
      views.add(const LogScreen());
    }
    if (_bisaKelolaKaryawan) {
      tabs.add(const Tab(text: "Kelola Karyawan"));
      views.add(const KaryawanScreen());
    }
    if (_punya("mandor.view")) {
      tabs.add(const Tab(text: "Kelola Mandor"));
      views.add(const MandorScreen());
    }

    return Scaffold(
      backgroundColor: latar,
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
              child: Row(
                children: [
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                        color: biru, borderRadius: BorderRadius.circular(10)),
                    child: const Icon(Icons.fingerprint,
                        color: Colors.white, size: 22),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Text("SAP.HRIS",
                                style: TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 15)),
                            const SizedBox(width: 6),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 6, vertical: 1),
                              decoration: BoxDecoration(
                                color: const Color(0xFF1E293B),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: const Text("Sawita",
                                  style: TextStyle(
                                      color: teksSekunder, fontSize: 10)),
                            ),
                          ],
                        ),
                        Text("Role: $role | $area",
                            style: const TextStyle(
                                fontSize: 11, color: teksSekunder)),
                      ],
                    ),
                  ),
                  CircleAvatar(
                    radius: 16,
                    backgroundColor: biru,
                    child: Text(
                      nama.isNotEmpty ? nama[0].toUpperCase() : "?",
                      style: const TextStyle(
                          color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                  IconButton(
                      onPressed: _logout,
                      icon: const Icon(Icons.logout,
                          color: teksSekunder, size: 20)),
                ],
              ),
            ),
            TabBar(
              controller: _tabController,
              isScrollable: tabs.length > 3,
              labelColor: Colors.white,
              unselectedLabelColor: teksSekunder,
              indicatorColor: biru,
              labelStyle:
                  const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
              tabs: tabs,
            ),
            Expanded(
              child: TabBarView(
                controller: _tabController,
                children: views,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
