const express = require("express");
const multer = require("multer");
const path = require("path");
const { pool } = require("../db");
const { verifyToken, requirePermission } = require("../middleware");

const router = express.Router();

const storage = multer.diskStorage({
    destination: (req, file, cb) =>
        cb(null, path.join(__dirname, "..", "uploads")),
    filename: (req, file, cb) => {
        const ext = path.extname(file.originalname) || ".jpg";
        cb(null, `presensi_${req.body.karyawan_id || "x"}_${Date.now()}${ext}`);
    },
});
const upload = multer({ storage });

// ---------- DAFTAR KARYAWAN TIM ----------
router.get("/karyawan", verifyToken, async (req, res) => {
    try {
        const scope =
            req.user.role === "admin_hr"
                ? { where: "", params: [] }
                : { where: "WHERE k.mandor_id = ?", params: [req.user.id] };

        const [rows] = await pool.query(
            `SELECT k.*, u.nama AS mandor_nama FROM karyawan k
       JOIN users u ON u.id = k.mandor_id
       ${scope.where}
       ORDER BY k.nama ASC`,
            scope.params,
        );
        res.json(rows);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- DASHBOARD (statistik tim) ----------
router.get("/dashboard", verifyToken, async (req, res) => {
    try {
        const isAdmin = ["admin_hr", "super_admin"].includes(req.user.role);
        const filterKaryawan = isAdmin ? "" : "WHERE mandor_id = ?";
        const paramsKaryawan = isAdmin ? [] : [req.user.id];

        const [[{ total }]] = await pool.query(
            `SELECT COUNT(*) AS total FROM karyawan ${filterKaryawan}`,
            paramsKaryawan,
        );

        const filterPresensi = isAdmin
            ? "WHERE p.tanggal = CURDATE()"
            : "WHERE p.tanggal = CURDATE() AND k.mandor_id = ?";
        const paramsPresensi = isAdmin ? [] : [req.user.id];

        const [statusRows] = await pool.query(
            `SELECT p.status, COUNT(*) AS jumlah FROM presensi p
       JOIN karyawan k ON k.id = p.karyawan_id
       ${filterPresensi}
       GROUP BY p.status`,
            paramsPresensi,
        );

        let hadir = 0,
            izinSakit = 0,
            alpa = 0;
        statusRows.forEach((r) => {
            if (r.status === "Hadir" || r.status === "Telat") hadir += r.jumlah;
            else if (r.status === "Izin" || r.status === "Sakit")
                izinSakit += r.jumlah;
            else if (r.status === "Alpa") alpa += r.jumlah;
        });

        const filterGrafik = isAdmin
            ? "WHERE p.tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)"
            : "WHERE p.tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND k.mandor_id = ?";
        const paramsGrafik = isAdmin ? [] : [req.user.id];

        const [grafikRows] = await pool.query(
            `SELECT p.tanggal,
              SUM(CASE WHEN p.status IN ('Hadir','Telat') THEN 1 ELSE 0 END) AS hadir,
              SUM(CASE WHEN p.status IN ('Izin','Sakit','Alpa') THEN 1 ELSE 0 END) AS tidak_hadir
       FROM presensi p
       JOIN karyawan k ON k.id = p.karyawan_id
       ${filterGrafik}
       GROUP BY p.tanggal ORDER BY p.tanggal ASC`,
            paramsGrafik,
        );

        res.json({
            totalAnggota: total,
            hadirHariIni: hadir,
            izinSakit,
            alpa,
            grafik7Hari: grafikRows,
        });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- CATAT PRESENSI ----------
router.post("/catat", verifyToken, upload.single("foto"), async (req, res) => {
    try {
        const { karyawan_id, status, keterangan, latitude, longitude } =
            req.body;

        if (!karyawan_id || !status) {
            return res
                .status(400)
                .json({ error: "karyawan_id dan status wajib diisi" });
        }
        const statusValid = ["Hadir", "Izin", "Sakit", "Telat", "Alpa"];
        if (!statusValid.includes(status)) {
            return res.status(400).json({ error: "Status tidak valid" });
        }

        const fotoPath = req.file ? `/uploads/${req.file.filename}` : null;
        const jamMasuk =
            status === "Hadir" || status === "Telat"
                ? new Date().toTimeString().slice(0, 8)
                : null;

        await pool.query(
            `INSERT INTO presensi (karyawan_id, tanggal, jam_masuk, status, keterangan, foto_path, latitude, longitude, mandor_id)
       VALUES (?, CURDATE(), ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE
         jam_masuk = VALUES(jam_masuk), status = VALUES(status), keterangan = VALUES(keterangan),
         foto_path = VALUES(foto_path), latitude = VALUES(latitude), longitude = VALUES(longitude)`,
            [
                karyawan_id,
                jamMasuk,
                status,
                keterangan || null,
                fotoPath,
                latitude || null,
                longitude || null,
                req.user.id,
            ],
        );

        res.json({ message: "Presensi berhasil dicatat" });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- DAFTAR LOKASI (buat dropdown filter) ----------
router.get("/lokasi", verifyToken, async (req, res) => {
    try {
        const isAdmin = req.user.role === "admin_hr";
        const where = isAdmin ? "" : "WHERE mandor_id = ?";
        const params = isAdmin ? [] : [req.user.id];

        const [rows] = await pool.query(
            `SELECT DISTINCT lokasi FROM karyawan ${where} ORDER BY lokasi ASC`,
            params,
        );
        res.json(rows.map((r) => r.lokasi));
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- LOG & LAPORAN PRESENSI ----------
router.get("/log", verifyToken, async (req, res) => {
    try {
        const { cari, lokasi, status, tanggal } = req.query;
        const isAdmin = req.user.role === "admin_hr";

        let where = "WHERE 1=1";
        const params = [];

        if (!isAdmin) {
            where += " AND k.mandor_id = ?";
            params.push(req.user.id);
        }
        if (cari) {
            where += " AND (k.nama LIKE ? OR k.nik LIKE ?)";
            params.push(`%${cari}%`, `%${cari}%`);
        }
        if (lokasi) {
            where += " AND k.lokasi = ?";
            params.push(lokasi);
        }
        if (status) {
            where += " AND p.status = ?";
            params.push(status);
        }
        if (tanggal) {
            where += " AND p.tanggal = ?";
            params.push(tanggal);
        }

        const [rows] = await pool.query(
            `SELECT p.id,
              CONCAT('ABS-', DATE_FORMAT(p.tanggal, '%Y%m%d'), '-', LPAD(p.id, 4, '0')) AS absen_id,
              p.tanggal, p.jam_masuk, p.status, p.keterangan, p.foto_path,
              p.latitude, p.longitude,
              k.nik, k.nama AS nama_karyawan, k.lokasi,
              u.nama AS nama_mandor
       FROM presensi p
       JOIN karyawan k ON k.id = p.karyawan_id
       JOIN users u ON u.id = p.mandor_id
       ${where}
       ORDER BY p.tanggal DESC, p.created_at DESC`,
            params,
        );

        res.json(rows);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- EXPORT CSV ----------
router.get("/export", async (req, res) => {
    try {
        const jwt = require("jsonwebtoken");
        const JWT_SECRET =
            process.env.JWT_SECRET || "ganti_dengan_secret_yang_aman";
        const token =
            req.query.token ||
            (req.headers.authorization || "").replace("Bearer ", "");

        let user;
        try {
            user = jwt.verify(token, JWT_SECRET);
        } catch (e) {
            return res.status(401).json({ error: "Token tidak valid" });
        }

        const isAdmin = user.role === "admin_hr";
        let where = "WHERE 1=1";
        const params = [];
        if (!isAdmin) {
            where += " AND k.mandor_id = ?";
            params.push(user.id);
        }
        if (req.query.lokasi) {
            where += " AND k.lokasi = ?";
            params.push(req.query.lokasi);
        }
        if (req.query.status) {
            where += " AND p.status = ?";
            params.push(req.query.status);
        }

        const [rows] = await pool.query(
            `SELECT p.tanggal, k.nik, k.nama AS nama_karyawan, k.lokasi, p.jam_masuk, p.status, p.keterangan, u.nama AS mandor
       FROM presensi p
       JOIN karyawan k ON k.id = p.karyawan_id
       JOIN users u ON u.id = p.mandor_id
       ${where}
       ORDER BY p.tanggal DESC`,
            params,
        );

        let csv =
            "Tanggal,NIK,Nama,Lokasi,Jam Masuk,Status,Keterangan,Mandor\n";
        rows.forEach((r) => {
            csv += `${r.tanggal.toISOString().slice(0, 10)},${r.nik},"${r.nama_karyawan}","${r.lokasi}",${r.jam_masuk || "-"},${r.status},"${r.keterangan || ""}","${r.mandor}"\n`;
        });

        res.setHeader("Content-Type", "text/csv");
        res.setHeader(
            "Content-Disposition",
            "attachment; filename=laporan_presensi.csv",
        );
        res.send(csv);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- TAMBAH KARYAWAN BARU ----------
router.post("/karyawan", verifyToken, async (req, res) => {
    try {
        if (req.user.role === "admin_hr") {
            return res
                .status(400)
                .json({
                    error: "Admin HR tidak menambah karyawan langsung, gunakan akun mandor",
                });
        }

        const { nik, nama, jabatan } = req.body;
        if (!nik || !nama) {
            return res.status(400).json({ error: "NIK dan nama wajib diisi" });
        }

        const [existing] = await pool.query(
            "SELECT id FROM karyawan WHERE nik = ?",
            [nik],
        );
        if (existing.length > 0) {
            return res.status(400).json({ error: "NIK sudah terdaftar" });
        }

        const tipe = req.user.role === "mandor_kebun" ? "kebun" : "pabrik";

        const [result] = await pool.query(
            `INSERT INTO karyawan (nik, nama, jabatan, tipe, lokasi, mandor_id) VALUES (?, ?, ?, ?, ?, ?)`,
            [nik, nama, jabatan || null, tipe, req.user.area, req.user.id],
        );

        res.json({
            message: "Karyawan berhasil ditambahkan",
            id: result.insertId,
        });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- UBAH KARYAWAN ----------
router.put(
    "/karyawan/:id",
    verifyToken,
    requirePermission("karyawan.update"),
    async (req, res) => {
        try {
            const { nik, nama, jabatan } = req.body;
            if (!nik || !nama)
                return res
                    .status(400)
                    .json({ error: "NIK dan nama wajib diisi" });

            const isAdmin = ["admin_hr", "super_admin"].includes(req.user.role);
            const [rows] = await pool.query(
                `SELECT id FROM karyawan WHERE id = ? ${isAdmin ? "" : "AND mandor_id = ?"}`,
                isAdmin ? [req.params.id] : [req.params.id, req.user.id],
            );
            if (rows.length === 0)
                return res
                    .status(404)
                    .json({ error: "Karyawan tidak ditemukan" });

            const [duplicate] = await pool.query(
                "SELECT id FROM karyawan WHERE nik = ? AND id <> ?",
                [nik, req.params.id],
            );
            if (duplicate.length > 0)
                return res.status(400).json({ error: "NIK sudah terdaftar" });

            await pool.query(
                "UPDATE karyawan SET nik = ?, nama = ?, jabatan = ? WHERE id = ?",
                [nik, nama, jabatan || null, req.params.id],
            );
            res.json({ message: "Karyawan berhasil diubah" });
        } catch (err) {
            console.error(err);
            res.status(500).json({ error: "Terjadi kesalahan server" });
        }
    },
);

// ---------- HAPUS KARYAWAN ----------
router.delete(
    "/karyawan/:id",
    verifyToken,
    requirePermission("karyawan.delete"),
    async (req, res) => {
        try {
            const isAdmin = ["admin_hr", "super_admin"].includes(req.user.role);
            const [rows] = await pool.query(
                `SELECT id FROM karyawan WHERE id = ? ${isAdmin ? "" : "AND mandor_id = ?"}`,
                isAdmin ? [req.params.id] : [req.params.id, req.user.id],
            );
            if (rows.length === 0)
                return res
                    .status(404)
                    .json({ error: "Karyawan tidak ditemukan" });

            await pool.query("DELETE FROM karyawan WHERE id = ?", [
                req.params.id,
            ]);
            res.json({ message: "Karyawan berhasil dihapus" });
        } catch (err) {
            console.error(err);
            res.status(500).json({ error: "Terjadi kesalahan server" });
        }
    },
);

module.exports = router;
