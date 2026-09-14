import 'package:flutter/material.dart';
import '../services/api_service.dart';

class KaryawanScreen extends StatefulWidget {
  const KaryawanScreen({super.key});

  @override
  State<KaryawanScreen> createState() => _KaryawanScreenState();
}

class _KaryawanScreenState extends State<KaryawanScreen> {
  static const merah = Color(0xFFDC1F35);

  List<dynamic> _karyawan = [];
  List<dynamic> _mandorList = [];
  bool _loading = true;
  bool _canCreate = false;
  bool _canEdit = false;
  bool _canDelete = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final permissions = await ApiService.getPermissions();
      final karyawan = await ApiService.getAllKaryawan();
      final mandor = permissions.contains("mandor.view")
          ? await ApiService.getMandorList()
          : <dynamic>[];
      setState(() {
        _karyawan = karyawan;
        _mandorList = mandor;
        _canCreate = permissions.contains("karyawan.create");
        _canEdit = permissions.contains("karyawan.update");
        _canDelete = permissions.contains("karyawan.delete");
      });
    } catch (_) {
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _hapusKaryawan(int id, String nama) async {
    final konfirmasi = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Hapus Karyawan"),
        content: Text("Yakin hapus $nama?"),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text("Batal")),
          TextButton(
              onPressed: () => Navigator.pop(context, true),
              child: const Text("Hapus", style: TextStyle(color: Colors.red))),
        ],
      ),
    );
    if (konfirmasi != true) return;

    try {
      await ApiService.hapusKaryawan(id);
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Karyawan berhasil dihapus")));
      _load();
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(e.toString().replaceFirst("Exception: ", ""))),
      );
    }
  }

  Future<void> _formKaryawan({Map<String, dynamic>? existing}) async {
    final nikCtrl = TextEditingController(text: existing?["nik"] ?? "");
    final namaCtrl = TextEditingController(text: existing?["nama"] ?? "");
    final jabatanCtrl = TextEditingController(text: existing?["jabatan"] ?? "");
    String tipe = existing?["tipe"] ?? "kebun";
    final lokasiCtrl = TextEditingController(text: existing?["lokasi"] ?? "");
    int? mandorId = existing?["mandor_id"];
    bool saving = false;
    String? error;
    final isEdit = existing != null;

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Padding(
              padding: EdgeInsets.only(
                  left: 20,
                  right: 20,
                  top: 20,
                  bottom: MediaQuery.of(context).viewInsets.bottom + 20),
              child: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(isEdit ? "Edit Karyawan" : "Tambah Karyawan",
                        style: const TextStyle(
                            fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 16),
                    TextField(
                        controller: nikCtrl,
                        decoration: const InputDecoration(
                            labelText: "NIK", border: OutlineInputBorder())),
                    const SizedBox(height: 12),
                    TextField(
                        controller: namaCtrl,
                        decoration: const InputDecoration(
                            labelText: "Nama Lengkap",
                            border: OutlineInputBorder())),
                    const SizedBox(height: 12),
                    TextField(
                        controller: jabatanCtrl,
                        decoration: const InputDecoration(
                            labelText: "Jabatan",
                            border: OutlineInputBorder())),
                    if (!isEdit) ...[
                      const SizedBox(height: 12),
                      DropdownButtonFormField<String>(
                        initialValue: tipe,
                        decoration: const InputDecoration(
                            labelText: "Tipe", border: OutlineInputBorder()),
                        items: const [
                          DropdownMenuItem(
                              value: "kebun", child: Text("Kebun")),
                          DropdownMenuItem(
                              value: "pabrik", child: Text("Pabrik")),
                        ],
                        onChanged: (val) =>
                            setModalState(() => tipe = val ?? "kebun"),
                      ),
                      const SizedBox(height: 12),
                      TextField(
                          controller: lokasiCtrl,
                          decoration: const InputDecoration(
                              labelText: "Lokasi (Divisi/Stasiun)",
                              border: OutlineInputBorder())),
                      const SizedBox(height: 12),
                      DropdownButtonFormField<int>(
                        initialValue: mandorId,
                        decoration: const InputDecoration(
                            labelText: "Mandor Penanggung Jawab",
                            border: OutlineInputBorder()),
                        items: _mandorList
                            .map<DropdownMenuItem<int>>((m) => DropdownMenuItem(
                                value: m["id"],
                                child: Text(m["nama"],
                                    overflow: TextOverflow.ellipsis)))
                            .toList(),
                        onChanged: (val) => setModalState(() => mandorId = val),
                      ),
                    ],
                    if (error != null) ...[
                      const SizedBox(height: 10),
                      Text(error!, style: const TextStyle(color: Colors.red)),
                    ],
                    const SizedBox(height: 16),
                    SizedBox(
                      height: 48,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                            backgroundColor: merah,
                            foregroundColor: Colors.white),
                        onPressed: saving
                            ? null
                            : () async {
                                if (nikCtrl.text.trim().isEmpty ||
                                    namaCtrl.text.trim().isEmpty) {
                                  setModalState(
                                      () => error = "NIK dan nama wajib diisi");
                                  return;
                                }
                                if (!isEdit &&
                                    (lokasiCtrl.text.trim().isEmpty ||
                                        mandorId == null)) {
                                  setModalState(() => error =
                                      "Lokasi dan mandor wajib dipilih");
                                  return;
                                }
                                setModalState(() {
                                  saving = true;
                                  error = null;
                                });
                                try {
                                  if (isEdit) {
                                    await ApiService.editKaryawan(
                                      id: existing["id"],
                                      nik: nikCtrl.text.trim(),
                                      nama: namaCtrl.text.trim(),
                                      jabatan: jabatanCtrl.text.trim(),
                                    );
                                  } else {
                                    await ApiService.tambahKaryawan(
                                      nik: nikCtrl.text.trim(),
                                      nama: namaCtrl.text.trim(),
                                      jabatan: jabatanCtrl.text.trim(),
                                      tipe: tipe,
                                      lokasi: lokasiCtrl.text.trim(),
                                      mandorId: mandorId!,
                                    );
                                  }
                                  if (!context.mounted) return;
                                  Navigator.pop(context);
                                  _load();
                                } catch (e) {
                                  setModalState(() {
                                    saving = false;
                                    error = e
                                        .toString()
                                        .replaceFirst("Exception: ", "");
                                  });
                                }
                              },
                        child: saving
                            ? const SizedBox(
                                width: 20,
                                height: 20,
                                child: CircularProgressIndicator(
                                    strokeWidth: 2, color: Colors.white))
                            : const Text("Simpan"),
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(16),
          color: Colors.white,
          child: Row(
            children: [
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text("Kelola Karyawan",
                        style: TextStyle(
                            fontWeight: FontWeight.bold, fontSize: 15)),
                    Text("Data karyawan seluruh tim",
                        style: TextStyle(fontSize: 11, color: Colors.grey)),
                  ],
                ),
              ),
              if (_canCreate)
                IconButton(
                  onPressed: () => _formKaryawan(),
                  icon: const Icon(Icons.person_add, color: merah),
                ),
            ],
          ),
        ),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _karyawan.isEmpty
                  ? const Center(
                      child: Text("Belum ada data karyawan",
                          style: TextStyle(color: Colors.grey)))
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.builder(
                        padding: const EdgeInsets.all(12),
                        itemCount: _karyawan.length,
                        itemBuilder: (context, i) {
                          final k = _karyawan[i];
                          return Card(
                            margin: const EdgeInsets.only(bottom: 10),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12)),
                            child: ListTile(
                              leading: CircleAvatar(
                                backgroundColor: merah.withValues(alpha: 0.1),
                                child: Text(
                                    (k["nama"] as String)
                                        .substring(0, 1)
                                        .toUpperCase(),
                                    style: const TextStyle(
                                        color: merah,
                                        fontWeight: FontWeight.bold)),
                              ),
                              title: Text(k["nama"],
                                  style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 13)),
                              subtitle: Text(
                                  "${k["nik"]} • ${k["jabatan"] ?? "-"}\n${k["lokasi"]} • Mandor: ${k["mandor_nama"] ?? "-"}",
                                  style: const TextStyle(fontSize: 11)),
                              isThreeLine: true,
                              trailing: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  if (_canEdit)
                                    IconButton(
                                        icon: const Icon(Icons.edit,
                                            size: 18, color: Colors.blue),
                                        onPressed: () =>
                                            _formKaryawan(existing: k)),
                                  if (_canDelete)
                                    IconButton(
                                        icon: const Icon(Icons.delete_outline,
                                            size: 18, color: Colors.red),
                                        onPressed: () =>
                                            _hapusKaryawan(k["id"], k["nama"])),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
                    ),
        ),
      ],
    );
  }
}
