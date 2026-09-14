import 'dart:io';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import '../services/api_service.dart';

class InputPresensiScreen extends StatefulWidget {
  const InputPresensiScreen({super.key});

  @override
  State<InputPresensiScreen> createState() => _InputPresensiScreenState();
}

class _InputPresensiScreenState extends State<InputPresensiScreen> {
  static const merah = Color(0xFFDC1F35);

  List<dynamic> _karyawan = [];
  bool _loading = true;
  bool _saving = false;
  Position? _posisi;
  String _filterTipe = "semua";

  final Map<int, String> _status = {};
  final Map<int, XFile?> _foto = {};
  final Map<int, TextEditingController> _keteranganCtrl = {};

  @override
  void initState() {
    super.initState();
    _load();
    _ambilLokasi();
  }

  Future<void> _ambilLokasi() async {
    try {
      bool enabled = await Geolocator.isLocationServiceEnabled();
      if (!enabled) return;
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) {
        return;
      }
      final pos = await Geolocator.getCurrentPosition(desiredAccuracy: LocationAccuracy.high);
      setState(() => _posisi = pos);
    } catch (_) {}
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.getKaryawan();
      for (var k in data) {
        _status[k["id"]] = "Hadir";
        _keteranganCtrl[k["id"]] = TextEditingController();
      }
      setState(() => _karyawan = data);
    } catch (_) {
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  List<dynamic> get _karyawanTerfilter {
    if (_filterTipe == "semua") return _karyawan;
    return _karyawan.where((k) => k["tipe"] == _filterTipe).toList();
  }

  Future<void> _ambilFoto(int karyawanId) async {
    final picker = ImagePicker();
    final foto = await picker.pickImage(source: ImageSource.camera, imageQuality: 70);
    if (foto != null) setState(() => _foto[karyawanId] = foto);
  }

  Future<void> _simpanSemua() async {
    setState(() => _saving = true);
    int berhasil = 0;
    int gagal = 0;

    for (var k in _karyawanTerfilter) {
      final id = k["id"] as int;
      try {
        await ApiService.catatPresensi(
          karyawanId: id,
          status: _status[id] ?? "Hadir",
          keterangan: _keteranganCtrl[id]?.text,
          latitude: _posisi?.latitude,
          longitude: _posisi?.longitude,
          fotoPath: _foto[id]?.path,
        );
        berhasil++;
      } catch (_) {
        gagal++;
      }
    }

    setState(() => _saving = false);
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text("Berhasil simpan $berhasil presensi${gagal > 0 ? ', $gagal gagal' : ''}")),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Center(child: CircularProgressIndicator());
    }

    final list = _karyawanTerfilter;

    return Column(
      children: [
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(16),
          color: Colors.white,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text("Form Input Presensi Tim", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              const Text("Presensi kolektif dengan foto & GPS", style: TextStyle(fontSize: 11, color: Colors.grey)),
              const SizedBox(height: 10),
              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<String>(
                      initialValue: _filterTipe,
                      isDense: true,
                      isExpanded: true,
                      decoration: InputDecoration(
                        contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      items: const [
                        DropdownMenuItem(value: "semua", child: Text("Semua Lokasi Operasional", style: TextStyle(fontSize: 12))),
                        DropdownMenuItem(value: "kebun", child: Text("Kebun Sawit", style: TextStyle(fontSize: 12))),
                        DropdownMenuItem(value: "pabrik", child: Text("Pabrik Kelapa Sawit (PKS)", style: TextStyle(fontSize: 12))),
                      ],
                      onChanged: (val) => setState(() => _filterTipe = val ?? "semua"),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
                    decoration: BoxDecoration(
                      color: _posisi != null ? Colors.green.shade50 : Colors.red.shade50,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.location_on, size: 14, color: _posisi != null ? Colors.green : Colors.red),
                        const SizedBox(width: 4),
                        Text(_posisi != null ? "GPS Aktif" : "GPS Off",
                            style: TextStyle(fontSize: 11, color: _posisi != null ? Colors.green.shade800 : Colors.red.shade800)),
                      ],
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
        Expanded(
          child: list.isEmpty
              ? Center(
                  child: Text(
                    _karyawan.isEmpty ? "Belum ada karyawan di tim kamu.\nHubungi Admin HR untuk menambahkan." : "Tidak ada karyawan di lokasi ini",
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.grey),
                  ),
                )
              : ListView.builder(
                  padding: const EdgeInsets.all(12),
                  itemCount: list.length,
                  itemBuilder: (context, i) {
                    final k = list[i];
                    final id = k["id"] as int;
                    final foto = _foto[id];

                    return Card(
                      margin: const EdgeInsets.only(bottom: 10),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      child: Padding(
                        padding: const EdgeInsets.all(12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                CircleAvatar(
                                  backgroundColor: merah.withValues(alpha: 0.1),
                                  child: Text((k["nama"] as String).substring(0, 1).toUpperCase(), style: const TextStyle(color: merah, fontWeight: FontWeight.bold)),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(k["nama"], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                                      Text("${k["nik"]} • ${k["jabatan"] ?? "-"}", style: TextStyle(fontSize: 11, color: Colors.grey.shade600)),
                                    ],
                                  ),
                                ),
                                OutlinedButton.icon(
                                  onPressed: () => _ambilFoto(id),
                                  icon: Icon(foto != null ? Icons.check_circle : Icons.camera_alt, size: 16, color: foto != null ? Colors.green : null),
                                  label: Text(foto != null ? "OK" : "Foto HP", style: const TextStyle(fontSize: 11)),
                                  style: OutlinedButton.styleFrom(padding: const EdgeInsets.symmetric(horizontal: 10)),
                                ),
                              ],
                            ),
                            if (foto != null) ...[
                              const SizedBox(height: 8),
                              ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: Image.file(File(foto.path), height: 100, width: double.infinity, fit: BoxFit.cover),
                              ),
                            ],
                            const SizedBox(height: 10),
                            Row(
                              children: [
                                const Text("Status:", style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold)),
                                const SizedBox(width: 8),
                                Expanded(
                                  child: DropdownButtonFormField<String>(
                                    initialValue: _status[id],
                                    isDense: true,
                                    decoration: const InputDecoration(contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 8), border: OutlineInputBorder()),
                                    items: ["Hadir", "Telat", "Izin", "Sakit", "Alpa"]
                                        .map((s) => DropdownMenuItem(value: s, child: Text(s, style: const TextStyle(fontSize: 12))))
                                        .toList(),
                                    onChanged: (val) => setState(() => _status[id] = val ?? "Hadir"),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 8),
                            TextField(
                              controller: _keteranganCtrl[id],
                              style: const TextStyle(fontSize: 12),
                              decoration: const InputDecoration(isDense: true, hintText: "Catatan/Hasil Panen...", contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 10), border: OutlineInputBorder()),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
        ),
        if (list.isNotEmpty)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: Colors.white, boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 8, offset: const Offset(0, -2))]),
            child: SizedBox(
              height: 48,
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(backgroundColor: merah, foregroundColor: Colors.white),
                onPressed: _saving ? null : _simpanSemua,
                icon: _saving
                    ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Icon(Icons.save, size: 18),
                label: Text(_saving ? "Menyimpan..." : "Simpan Presensi Tim"),
              ),
            ),
          ),
      ],
    );
  }
}