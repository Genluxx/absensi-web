import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';
import '../services/api_service.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  static const biru = Color(0xFF3B82F6);
  static const panel = Color(0xFF0F172A);
  static const teksSekunder = Color(0xFF94A3B8);

  Map<String, dynamic>? _data;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.getDashboard();
      setState(() => _data = data);
    } catch (_) {
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Center(child: CircularProgressIndicator());
    }

    final total = _data?["totalAnggota"] ?? 0;
    final hadir = _data?["hadirHariIni"] ?? 0;
    final izinSakit = _data?["izinSakit"] ?? 0;
    final alpa = _data?["alpa"] ?? 0;
    final List grafik = _data?["grafik7Hari"] ?? [];

    final now = DateTime.now();
    final jamStr = DateFormat("HH.mm.ss").format(now);
    final tanggalStr = DateFormat("EEEE, d MMMM yyyy", "id_ID").format(now);

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: panel,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFF334155)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Row(
                  children: [
                    CircleAvatar(
                      radius: 22,
                      backgroundColor: biru,
                      child: Icon(Icons.person, color: Colors.white),
                    ),
                    SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("SELAMAT DATANG",
                              style: TextStyle(
                                  color: biru,
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold)),
                          Text(
                              "Sistem Presensi Operasional Kebun & Pabrik Sawit",
                              style:
                                  TextStyle(fontSize: 11, color: teksSekunder)),
                        ],
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFF111827),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text("WAKTU PRESENSI SERVER",
                          style: TextStyle(fontSize: 10, color: teksSekunder)),
                      Icon(Icons.access_time,
                          size: 16, color: Color(0xFF34D399)),
                    ],
                  ),
                ),
                const SizedBox(height: 4),
                Text(jamStr,
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 22,
                        fontWeight: FontWeight.bold)),
                Text(tanggalStr,
                    style: const TextStyle(fontSize: 12, color: teksSekunder)),
              ],
            ),
          ),
          const SizedBox(height: 16),
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            childAspectRatio: 1.6,
            children: [
              _statCard(
                  "TOTAL ANGGOTA TIM", "$total", Icons.people, Colors.blue),
              _statCard(
                  "HADIR HARI INI", "$hadir", Icons.check_circle, Colors.green),
              _statCard("IZIN / SAKIT", "$izinSakit", Icons.event_busy,
                  Colors.orange),
              _statCard("ALPA / MANGKIR", "$alpa", Icons.cancel, Colors.red),
            ],
          ),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: panel,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: const Color(0xFF334155)),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("Grafik Kehadiran Tim Operasional",
                    style:
                        TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                const Text("Statistik presensi Kebun & Pabrik 7 hari terakhir",
                    style: TextStyle(fontSize: 11, color: teksSekunder)),
                const SizedBox(height: 20),
                SizedBox(
                  height: 200,
                  child: grafik.isEmpty
                      ? const Center(
                          child: Text("Belum ada data presensi",
                              style: TextStyle(color: teksSekunder)))
                      : BarChart(
                          BarChartData(
                            gridData: const FlGridData(show: false),
                            borderData: FlBorderData(show: false),
                            titlesData: FlTitlesData(
                              leftTitles: const AxisTitles(
                                  sideTitles: SideTitles(showTitles: false)),
                              topTitles: const AxisTitles(
                                  sideTitles: SideTitles(showTitles: false)),
                              rightTitles: const AxisTitles(
                                  sideTitles: SideTitles(showTitles: false)),
                              bottomTitles: AxisTitles(
                                sideTitles: SideTitles(
                                  showTitles: true,
                                  getTitlesWidget: (value, meta) {
                                    final idx = value.toInt();
                                    if (idx < 0 || idx >= grafik.length) {
                                      return const SizedBox();
                                    }
                                    final tgl = DateTime.tryParse(
                                        grafik[idx]["tanggal"].toString());
                                    final label = tgl != null
                                        ? DateFormat("E", "id_ID").format(tgl)
                                        : "";
                                    return Padding(
                                      padding: const EdgeInsets.only(top: 6),
                                      child: Text(label,
                                          style: const TextStyle(
                                              color: teksSekunder,
                                              fontSize: 9)),
                                    );
                                  },
                                ),
                              ),
                            ),
                            barGroups: List.generate(grafik.length, (i) {
                              final hadirHari = double.tryParse(
                                      grafik[i]["hadir"].toString()) ??
                                  0;
                              final tidakHadirHari = double.tryParse(
                                      grafik[i]["tidak_hadir"].toString()) ??
                                  0;
                              return BarChartGroupData(
                                x: i,
                                barsSpace: 4,
                                barRods: [
                                  BarChartRodData(
                                    toY: hadirHari,
                                    color: const Color(0xFF16A34A),
                                    width: 12,
                                    borderRadius: BorderRadius.circular(3),
                                  ),
                                  BarChartRodData(
                                    toY: tidakHadirHari,
                                    color: const Color(0xFFDC2626),
                                    width: 12,
                                    borderRadius: BorderRadius.circular(3),
                                  ),
                                ],
                              );
                            }),
                          ),
                        ),
                ),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                        width: 12,
                        height: 12,
                        decoration: BoxDecoration(
                            color: const Color(0xFF16A34A),
                            borderRadius: BorderRadius.circular(3))),
                    const SizedBox(width: 6),
                    const Text("Hadir",
                        style: TextStyle(color: teksSekunder, fontSize: 11)),
                    const SizedBox(width: 20),
                    Container(
                        width: 12,
                        height: 12,
                        decoration: BoxDecoration(
                            color: const Color(0xFFDC2626),
                            borderRadius: BorderRadius.circular(3))),
                    const SizedBox(width: 6),
                    const Text("Tidak Hadir",
                        style: TextStyle(color: teksSekunder, fontSize: 11)),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _statCard(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: panel,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFF334155)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
                color: color.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, color: color, size: 18),
          ),
          const Spacer(),
          Text(label,
              style: const TextStyle(
                  fontSize: 9,
                  color: teksSekunder,
                  fontWeight: FontWeight.bold)),
          Text(value,
              style: const TextStyle(
                  color: Colors.white,
                  fontSize: 20,
                  fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }
}
