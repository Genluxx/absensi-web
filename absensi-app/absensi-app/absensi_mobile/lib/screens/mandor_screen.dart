import 'package:flutter/material.dart';
import '../services/api_service.dart';

class MandorScreen extends StatefulWidget {
  const MandorScreen({super.key});

  @override
  State<MandorScreen> createState() => _MandorScreenState();
}

class _MandorScreenState extends State<MandorScreen> {
  static const merah = Color(0xFFDC1F35);

  List<dynamic> _mandorList = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final data = await ApiService.getMandorList();
      setState(() => _mandorList = data);
    } catch (_) {
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _hapusMandor(int id, String nama) async {
    final konfirmasi = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Hapus Mandor"),
        content: Text("Yakin hapus akun $nama?"),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: const Text("Batal")),
          TextButton(onPressed: () => Navigator.pop(context, true), child: const Text("Hapus", style: TextStyle(color: Colors.red))),
        ],
      ),
    );
    if (konfirmasi != true) return;

    try {
      await ApiService.hapusMandor(id);
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Mandor berhasil dihapus")));
      _load();
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString().replaceFirst("Exception: ", ""))));
    }
  }

  Future<void> _tambahMandorForm() async {
    final namaCtrl = TextEditingController();
    final usernameCtrl = TextEditingController();
    final passwordCtrl = TextEditingController();
    final areaCtrl = TextEditingController();
    String roleKode = "mandor_kebun";
    bool saving = false;
    String? error;

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Padding(
              padding: EdgeInsets.only(left: 20, right: 20, top: 20, bottom: MediaQuery.of(context).viewInsets.bottom + 20),
              child: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    const Text("Tambah Akun Mandor", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                    const SizedBox(height: 16),
                    TextField(controller: namaCtrl, decoration: const InputDecoration(labelText: "Nama Lengkap", border: OutlineInputBorder())),
                    const SizedBox(height: 12),
                    TextField(controller: usernameCtrl, decoration: const InputDecoration(labelText: "Username", border: OutlineInputBorder())),
                    const SizedBox(height: 12),
                    TextField(controller: passwordCtrl, obscureText: true, decoration: const InputDecoration(labelText: "Password", border: OutlineInputBorder())),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<String>(
                      initialValue: roleKode,
                      decoration: const InputDecoration(labelText: "Role", border: OutlineInputBorder()),
                      items: const [
                        DropdownMenuItem(value: "mandor_kebun", child: Text("Mandor Kebun")),
                        DropdownMenuItem(value: "mandor_pabrik", child: Text("Mandor Pabrik")),
                      ],
                      onChanged: (val) => setModalState(() => roleKode = val ?? "mandor_kebun"),
                    ),
                    const SizedBox(height: 12),
                    TextField(controller: areaCtrl, decoration: const InputDecoration(labelText: "Area/Wilayah Kerja", border: OutlineInputBorder(), hintText: "Contoh: Divisi III - Blok C")),
                    if (error != null) ...[
                      const SizedBox(height: 10),
                      Text(error!, style: const TextStyle(color: Colors.red)),
                    ],
                    const SizedBox(height: 16),
                    SizedBox(
                      height: 48,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(backgroundColor: merah, foregroundColor: Colors.white),
                        onPressed: saving
                            ? null
                            : () async {
                                if ([namaCtrl.text, usernameCtrl.text, passwordCtrl.text, areaCtrl.text].any((e) => e.trim().isEmpty)) {
                                  setModalState(() => error = "Semua field wajib diisi");
                                  return;
                                }
                                setModalState(() { saving = true; error = null; });
                                try {
                                  await ApiService.tambahMandor(
                                    nama: namaCtrl.text.trim(),
                                    username: usernameCtrl.text.trim(),
                                    password: passwordCtrl.text,
                                    roleKode: roleKode,
                                    area: areaCtrl.text.trim(),
                                  );
                                  if (!context.mounted) return;
                                  Navigator.pop(context);
                                  _load();
                                } catch (e) {
                                  setModalState(() {
                                    saving = false;
                                    error = e.toString().replaceFirst("Exception: ", "");
                                  });
                                }
                              },
                        child: saving
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : const Text("Simpan Mandor"),
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
                    Text("Kelola Mandor", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                    Text("Akun Mandor Kebun & Mandor Pabrik", style: TextStyle(fontSize: 11, color: Colors.grey)),
                  ],
                ),
              ),
              IconButton(onPressed: _tambahMandorForm, icon: const Icon(Icons.person_add, color: merah)),
            ],
          ),
        ),
        Expanded(
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _mandorList.isEmpty
                  ? const Center(child: Text("Belum ada akun mandor", style: TextStyle(color: Colors.grey)))
                  : RefreshIndicator(
                      onRefresh: _load,
                      child: ListView.builder(
                        padding: const EdgeInsets.all(12),
                        itemCount: _mandorList.length,
                        itemBuilder: (context, i) {
                          final m = _mandorList[i];
                          final isKebun = m["role_kode"] == "mandor_kebun";
                          return Card(
                            margin: const EdgeInsets.only(bottom: 10),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            child: ListTile(
                              leading: CircleAvatar(
                                backgroundColor: (isKebun ? Colors.green : Colors.blue).withValues(alpha: 0.1),
                                child: Text((m["nama"] as String).substring(0, 1).toUpperCase(), style: TextStyle(color: isKebun ? Colors.green : Colors.blue, fontWeight: FontWeight.bold)),
                              ),
                              title: Text(m["nama"], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                              subtitle: Text("${m["username"]} • ${isKebun ? "Mandor Kebun" : "Mandor Pabrik"}\n${m["area"] ?? "-"}", style: const TextStyle(fontSize: 11)),
                              isThreeLine: true,
                              trailing: IconButton(icon: const Icon(Icons.delete_outline, color: Colors.red, size: 20), onPressed: () => _hapusMandor(m["id"], m["nama"])),
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