import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';

class LogScreen extends StatefulWidget {
  const LogScreen({super.key});
  @override
  State<LogScreen> createState() => _LogScreenState();
}

class _LogScreenState extends State<LogScreen> {
  static const latar = Color(0xFF020817);
  static const panel = Color(0xFF0F172A);
  static const sekunder = Color(0xFFCBD5E1);
  final _cariCtrl = TextEditingController();
  List<dynamic> _data = [], _lokasiList = [];
  bool _loading = true;
  String? _statusFilter, _lokasiFilter;

  @override
  void initState() {
    super.initState();
    _loadLokasi();
    _load();
  }

  Future<void> _loadLokasi() async {
    try {
      final lokasi = await ApiService.getLokasi();
      if (mounted) setState(() => _lokasiList = lokasi);
    } catch (_) {}
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.getLog(
          cari: _cariCtrl.text.trim(),
          status: _statusFilter,
          lokasi: _lokasiFilter);
      if (mounted) setState(() => _data = data);
    } catch (_) {
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _exportCsv() async {
    final uri = Uri.parse(await ApiService.getExportUrl(
        lokasi: _lokasiFilter, status: _statusFilter));
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  Color _statusColor(String status) {
    switch (status) {
      case 'Hadir':
        return Colors.green;
      case 'Telat':
        return Colors.orange;
      case 'Izin':
      case 'Sakit':
        return Colors.blue;
      case 'Alpa':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  InputDecoration _decoration({String? hint, Widget? prefix, Widget? suffix}) =>
      InputDecoration(
        isDense: true,
        hintText: hint,
        hintStyle: const TextStyle(color: sekunder),
        prefixIcon: prefix,
        suffixIcon: suffix,
        filled: true,
        fillColor: const Color(0xFF111827),
        enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(10),
            borderSide: const BorderSide(color: Color(0xFF334155))),
        focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(10),
            borderSide: const BorderSide(color: Color(0xFF3B82F6), width: 2)),
      );

  Widget _detail(String label, String value) => Padding(
        padding: const EdgeInsets.only(bottom: 4),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          SizedBox(
              width: 90,
              child: Text(label,
                  style: const TextStyle(color: sekunder, fontSize: 11))),
          const Text(': ', style: TextStyle(color: sekunder, fontSize: 11)),
          Expanded(
              child: Text(value,
                  style: const TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      fontWeight: FontWeight.w500))),
        ]),
      );

  void _lihatBukti(dynamic item) => showDialog(
      context: context,
      builder: (_) => AlertDialog(
            title: Text(item['nama_karyawan'] ?? '-'),
            content: Text(item['latitude'] != null
                ? 'GPS: ${item['latitude']}, ${item['longitude']}'
                : 'Tidak ada GPS'),
            actions: [
              TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Tutup'))
            ],
          ));

  @override
  Widget build(BuildContext context) => Container(
        color: latar,
        child: Column(children: [
          Container(
              color: panel,
              padding: const EdgeInsets.all(16),
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(children: [
                      const Expanded(
                          child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                            Text('Log & Histori Presensi',
                                style: TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 15)),
                            Text('Filter cepat & pencarian NIK',
                                style:
                                    TextStyle(color: sekunder, fontSize: 11)),
                          ])),
                      ElevatedButton.icon(
                          onPressed: _exportCsv,
                          icon: const Icon(Icons.file_download, size: 16),
                          label: const Text('Export CSV'),
                          style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.blue,
                              foregroundColor: Colors.white)),
                    ]),
                    const SizedBox(height: 12),
                    TextField(
                        controller: _cariCtrl,
                        style: const TextStyle(color: Colors.white),
                        onSubmitted: (_) => _load(),
                        decoration: _decoration(
                            hint: 'Ketik nama atau NIK...',
                            prefix: const Icon(Icons.search, color: sekunder),
                            suffix: IconButton(
                                onPressed: _load,
                                icon: const Icon(Icons.arrow_forward,
                                    color: Colors.white)))),
                    const SizedBox(height: 10),
                    Row(children: [
                      Expanded(
                          child: DropdownButtonFormField<String?>(
                              initialValue: _lokasiFilter,
                              isExpanded: true,
                              style: const TextStyle(color: Colors.white),
                              decoration: _decoration(),
                              items: [
                                const DropdownMenuItem(
                                    value: null,
                                    child: Text('Semua Divisi',
                                        style: TextStyle(color: Colors.white))),
                                ..._lokasiList.map((l) => DropdownMenuItem(
                                    value: l as String,
                                    child: Text(l,
                                        style: const TextStyle(
                                            color: Colors.white))))
                              ],
                              onChanged: (v) {
                                setState(() => _lokasiFilter = v);
                                _load();
                              })),
                      const SizedBox(width: 8),
                      Expanded(
                          child: DropdownButtonFormField<String?>(
                              initialValue: _statusFilter,
                              isExpanded: true,
                              style: const TextStyle(color: Colors.white),
                              decoration: _decoration(),
                              items: const [
                                DropdownMenuItem(
                                    value: null,
                                    child: Text('Semua Status',
                                        style: TextStyle(color: Colors.white))),
                                DropdownMenuItem(
                                    value: 'Hadir',
                                    child: Text('Hadir',
                                        style: TextStyle(color: Colors.white))),
                                DropdownMenuItem(
                                    value: 'Telat',
                                    child: Text('Telat',
                                        style: TextStyle(color: Colors.white))),
                                DropdownMenuItem(
                                    value: 'Izin',
                                    child: Text('Izin',
                                        style: TextStyle(color: Colors.white))),
                                DropdownMenuItem(
                                    value: 'Sakit',
                                    child: Text('Sakit',
                                        style: TextStyle(color: Colors.white))),
                                DropdownMenuItem(
                                    value: 'Alpa',
                                    child: Text('Alpa',
                                        style: TextStyle(color: Colors.white)))
                              ],
                              onChanged: (v) {
                                setState(() => _statusFilter = v);
                                _load();
                              })),
                    ]),
                  ])),
          Expanded(
              child: _loading
                  ? const Center(child: CircularProgressIndicator())
                  : _data.isEmpty
                      ? const Center(
                          child: Text('Tidak ada data presensi',
                              style: TextStyle(color: sekunder)))
                      : RefreshIndicator(
                          onRefresh: _load,
                          child: ListView.builder(
                              padding: const EdgeInsets.all(12),
                              itemCount: _data.length,
                              itemBuilder: (_, i) {
                                final item = _data[i];
                                final date = DateTime.tryParse(
                                    item['tanggal'].toString());
                                final color = _statusColor(item['status']);
                                return Card(
                                    color: panel,
                                    child: Padding(
                                        padding: const EdgeInsets.all(14),
                                        child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Row(children: [
                                                Expanded(
                                                    child: Text(
                                                        item['absen_id'] ?? '-',
                                                        style: const TextStyle(
                                                            color: sekunder,
                                                            fontSize: 10))),
                                                Text(item['status'],
                                                    style: TextStyle(
                                                        color: color,
                                                        fontWeight:
                                                            FontWeight.bold))
                                              ]),
                                              const SizedBox(height: 6),
                                              Text(item['nama_karyawan'] ?? '-',
                                                  style: const TextStyle(
                                                      color: Colors.white,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                      fontSize: 15)),
                                              const Divider(
                                                  color: Color(0xFF334155)),
                                              _detail(
                                                  'Tanggal',
                                                  date == null
                                                      ? '-'
                                                      : DateFormat('dd/MM/yyyy')
                                                          .format(date)),
                                              _detail('Divisi/Blok',
                                                  item['lokasi'] ?? '-'),
                                              _detail('Jam Masuk',
                                                  item['jam_masuk'] ?? '-'),
                                              _detail('Keterangan',
                                                  item['keterangan'] ?? '-'),
                                              _detail('Mandor',
                                                  item['nama_mandor'] ?? '-'),
                                              OutlinedButton.icon(
                                                  onPressed: () =>
                                                      _lihatBukti(item),
                                                  icon: const Icon(
                                                      Icons.camera_alt,
                                                      size: 14),
                                                  label: const Text(
                                                      'Lihat Bukti (Foto & GPS)')),
                                            ])));
                              }))),
          if (_data.isNotEmpty)
            Container(
                color: panel,
                width: double.infinity,
                padding: const EdgeInsets.all(10),
                child: Text('Menampilkan ${_data.length} data',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: sekunder, fontSize: 11))),
        ]),
      );
}
