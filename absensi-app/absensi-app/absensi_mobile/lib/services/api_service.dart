import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String _defaultIp = String.fromEnvironment(
    'API_HOST',
    defaultValue: '192.168.100.228',
  );
  static const String _defaultPort = String.fromEnvironment(
    'API_PORT',
    defaultValue: '3000',
  );

  static String _baseUrl = "http://$_defaultIp:$_defaultPort/api";
  static String get baseUrl => _baseUrl;

  static Future<void> initBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    final savedIp = prefs.getString('server_ip');
    final savedPort = prefs.getString('server_port') ?? _defaultPort;
    if (savedIp != null && savedIp.isNotEmpty) {
      _baseUrl = "http://$savedIp:$savedPort/api";
    }
  }

  static Future<void> setServerAddress(String ip,
      {String port = _defaultPort}) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('server_ip', ip);
    await prefs.setString('server_port', port);
    _baseUrl = "http://$ip:$port/api";
  }

  static Future<String> getSavedIp() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('server_ip') ?? _defaultIp;
  }

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  static Future<void> saveSession(
      String token, Map<String, dynamic> user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', token);
    await prefs.setString('user', jsonEncode(user));
  }

  static Future<Map<String, dynamic>?> getUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userStr = prefs.getString('user');
    if (userStr == null) return null;
    return jsonDecode(userStr);
  }

  static Future<List<String>> getPermissions() async {
    final user = await getUser();
    if (user == null || user["permissions"] == null) return [];
    return List<String>.from(user["permissions"]);
  }

  static Future<bool> hasPermission(String kode) async {
    final permissions = await getPermissions();
    return permissions.contains(kode);
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
    await prefs.remove('user');
  }

  static Future<Map<String, String>> _authHeaders() async {
    final token = await getToken();
    return {
      "Content-Type": "application/json",
      if (token != null) "Authorization": "Bearer $token",
    };
  }

  static dynamic _readResponse(http.Response response) {
    dynamic data;
    try {
      data = jsonDecode(response.body);
    } catch (_) {
      data = null;
    }

    if (response.statusCode < 200 || response.statusCode >= 300) {
      final message = data is Map && data['error'] != null
          ? data['error'].toString()
          : 'Permintaan gagal (${response.statusCode})';
      throw Exception(message);
    }
    if (data == null) throw Exception('Respons server tidak valid');
    return data;
  }

  // ---------- AUTH ----------

  static Future<Map<String, dynamic>> login(
      String username, String password) async {
    late final http.Response res;
    try {
      res = await http
          .post(
            Uri.parse("$baseUrl/auth/login"),
            headers: {"Content-Type": "application/json"},
            body: jsonEncode({"username": username, "password": password}),
          )
          .timeout(const Duration(seconds: 10));
    } on Exception {
      throw Exception("Tidak dapat terhubung ke server $baseUrl");
    }
    return Map<String, dynamic>.from(_readResponse(res) as Map);
  }

  static Future<void> register({
    required String username,
    required String nama,
    required String email,
    required String password,
    required String area,
  }) async {
    final res = await http.post(
      Uri.parse("$baseUrl/auth/register"),
      headers: {"Content-Type": "application/json"},
      body: jsonEncode({
        "username": username,
        "nama": nama,
        "email": email,
        "password": password,
        "area": area,
      }),
    );
    _readResponse(res);
  }

  static Future<void> requestPasswordReset(String email) async {
    final res = await http.post(
      Uri.parse("$baseUrl/auth/forgot-password"),
      headers: {"Content-Type": "application/json"},
      body: jsonEncode({"email": email}),
    );
    _readResponse(res);
  }

  static Future<void> resetPassword({
    required String email,
    required String token,
    required String password,
  }) async {
    final res = await http.post(
      Uri.parse("$baseUrl/auth/reset-password"),
      headers: {"Content-Type": "application/json"},
      body: jsonEncode({"email": email, "token": token, "password": password}),
    );
    _readResponse(res);
  }

  // ---------- PRESENSI ----------

  static Future<List<dynamic>> getKaryawan() async {
    final res = await http.get(
      Uri.parse("$baseUrl/presensi/karyawan"),
      headers: await _authHeaders(),
    );
    return List<dynamic>.from(_readResponse(res) as List);
  }

  static Future<void> tambahKaryawan({
    required String nik,
    required String nama,
    String? jabatan,
    required String tipe,
    required String lokasi,
    required int mandorId,
  }) async {
    final res = await http.post(
      Uri.parse("$baseUrl/presensi/karyawan"),
      headers: await _authHeaders(),
      body: jsonEncode({
        "nik": nik,
        "nama": nama,
        "jabatan": jabatan,
        "tipe": tipe,
        "lokasi": lokasi,
        "mandor_id": mandorId,
      }),
    );
    _readResponse(res);
  }

  static Future<void> editKaryawan({
    required int id,
    required String nik,
    required String nama,
    String? jabatan,
  }) async {
    final res = await http.put(
      Uri.parse("$baseUrl/presensi/karyawan/$id"),
      headers: await _authHeaders(),
      body: jsonEncode({"nik": nik, "nama": nama, "jabatan": jabatan}),
    );
    _readResponse(res);
  }

  static Future<void> hapusKaryawan(int id) async {
    final res = await http.delete(
      Uri.parse("$baseUrl/presensi/karyawan/$id"),
      headers: await _authHeaders(),
    );
    _readResponse(res);
  }

  static Future<Map<String, dynamic>> getDashboard() async {
    final res = await http.get(
      Uri.parse("$baseUrl/presensi/dashboard"),
      headers: await _authHeaders(),
    );
    return Map<String, dynamic>.from(_readResponse(res) as Map);
  }

  static Future<void> catatPresensi({
    required int karyawanId,
    required String status,
    String? keterangan,
    double? latitude,
    double? longitude,
    String? fotoPath,
    int? mandorId,
  }) async {
    final token = await getToken();
    final uri = Uri.parse("$baseUrl/presensi/catat");
    final request = http.MultipartRequest("POST", uri);
    request.headers["Authorization"] = "Bearer $token";
    request.fields["karyawan_id"] = karyawanId.toString();
    request.fields["status"] = status;
    if (keterangan != null) request.fields["keterangan"] = keterangan;
    if (latitude != null) request.fields["latitude"] = latitude.toString();
    if (longitude != null) request.fields["longitude"] = longitude.toString();
    if (mandorId != null) request.fields["mandor_id"] = mandorId.toString();
    if (fotoPath != null) {
      request.files.add(await http.MultipartFile.fromPath("foto", fotoPath));
    }

    final streamed = await request.send();
    final res = await http.Response.fromStream(streamed);
    _readResponse(res);
  }

  static Future<List<dynamic>> getLog({
    String? cari,
    String? lokasi,
    String? status,
    String? tanggal,
  }) async {
    final params = <String, String>{};
    if (cari != null && cari.isNotEmpty) params["cari"] = cari;
    if (lokasi != null && lokasi.isNotEmpty) params["lokasi"] = lokasi;
    if (status != null && status.isNotEmpty) params["status"] = status;
    if (tanggal != null && tanggal.isNotEmpty) params["tanggal"] = tanggal;

    final uri =
        Uri.parse("$baseUrl/presensi/log").replace(queryParameters: params);
    final res = await http.get(uri, headers: await _authHeaders());
    return List<dynamic>.from(_readResponse(res) as List);
  }

  static Future<List<dynamic>> getLokasi() async {
    final res = await http.get(
      Uri.parse("$baseUrl/presensi/lokasi"),
      headers: await _authHeaders(),
    );
    return List<dynamic>.from(_readResponse(res) as List);
  }

  static Future<String> getExportUrl({String? lokasi, String? status}) async {
    final token = await getToken();
    final params = <String, String>{"token": token ?? ""};
    if (lokasi != null && lokasi.isNotEmpty) params["lokasi"] = lokasi;
    if (status != null && status.isNotEmpty) params["status"] = status;
    final uri =
        Uri.parse("$baseUrl/presensi/export").replace(queryParameters: params);
    return uri.toString();
  }

  // ---------- KELOLA MANDOR ----------

  static Future<List<dynamic>> getMandorList() async {
    final res = await http.get(
      Uri.parse("$baseUrl/admin/mandor"),
      headers: await _authHeaders(),
    );
    return List<dynamic>.from(_readResponse(res) as List);
  }

  static Future<void> tambahMandor({
    required String nama,
    required String username,
    required String password,
    required String roleKode,
    required String area,
  }) async {
    final res = await http.post(
      Uri.parse("$baseUrl/admin/mandor"),
      headers: await _authHeaders(),
      body: jsonEncode({
        "nama": nama,
        "username": username,
        "password": password,
        "roleKode": roleKode,
        "area": area,
      }),
    );
    _readResponse(res);
  }

  static Future<void> hapusMandor(int id) async {
    final res = await http.delete(
      Uri.parse("$baseUrl/admin/mandor/$id"),
      headers: await _authHeaders(),
    );
    _readResponse(res);
  }

  static Future<List<dynamic>> getAllKaryawan() async {
    final res = await http.get(
      Uri.parse("$baseUrl/admin/karyawan"),
      headers: await _authHeaders(),
    );
    return List<dynamic>.from(_readResponse(res) as List);
  }
}
