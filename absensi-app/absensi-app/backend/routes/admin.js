const express = require("express");
const bcrypt = require("bcryptjs");
const { pool } = require("../db");
const { verifyToken, requirePermission } = require("../middleware");

const router = express.Router();
router.use(verifyToken);

// ---------- DAFTAR SEMUA MANDOR ----------
router.get("/mandor", requirePermission("mandor.view"), async (req, res) => {
    try {
        const [rows] = await pool.query(
            `SELECT u.id, u.username, u.nama, r.kode AS role_kode, r.nama AS role_nama, u.area, u.created_at
       FROM users u
       JOIN roles r ON r.id = u.role_id
       WHERE r.kode IN ('mandor_kebun', 'mandor_pabrik')
       ORDER BY u.nama ASC`,
        );
        res.json(rows);
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- TAMBAH AKUN MANDOR ----------
router.post("/mandor", requirePermission("mandor.create"), async (req, res) => {
    try {
        const { nama, username, password, roleKode, area } = req.body;

        if (!nama || !username || !password || !roleKode || !area) {
            return res.status(400).json({ error: "Semua field wajib diisi" });
        }
        if (!["mandor_kebun", "mandor_pabrik"].includes(roleKode)) {
            return res.status(400).json({ error: "Role tidak valid" });
        }

        const [existing] = await pool.query(
            "SELECT id FROM users WHERE username = ?",
            [username],
        );
        if (existing.length > 0) {
            return res.status(400).json({ error: "Username sudah dipakai" });
        }

        const [roleRow] = await pool.query(
            "SELECT id FROM roles WHERE kode = ?",
            [roleKode],
        );
        if (roleRow.length === 0) {
            return res.status(400).json({ error: "Role tidak ditemukan" });
        }

        const hash = await bcrypt.hash(password, 10);
        const [result] = await pool.query(
            "INSERT INTO users (username, password, nama, role_id, area) VALUES (?, ?, ?, ?, ?)",
            [username, hash, nama, roleRow[0].id, area],
        );

        res.json({
            message: "Akun mandor berhasil ditambahkan",
            id: result.insertId,
        });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

// ---------- HAPUS AKUN MANDOR ----------
router.delete(
    "/mandor/:id",
    requirePermission("mandor.delete"),
    async (req, res) => {
        try {
            const { id } = req.params;

            const [karyawanAktif] = await pool.query(
                "SELECT id FROM karyawan WHERE mandor_id = ? LIMIT 1",
                [id],
            );
            if (karyawanAktif.length > 0) {
                return res
                    .status(400)
                    .json({
                        error: "Mandor ini masih punya karyawan aktif, tidak bisa dihapus",
                    });
            }

            await pool.query("DELETE FROM users WHERE id = ?", [id]);
            res.json({ message: "Akun mandor berhasil dihapus" });
        } catch (err) {
            console.error(err);
            res.status(500).json({ error: "Terjadi kesalahan server" });
        }
    },
);

// ---------- DAFTAR SEMUA KARYAWAN (semua tim) ----------
router.get(
    "/karyawan",
    requirePermission("karyawan.view"),
    async (req, res) => {
        try {
            const isAdmin = ["admin_hr", "super_admin"].includes(req.user.role);
            const [rows] = await pool.query(
                `SELECT k.*, u.nama AS mandor_nama FROM karyawan k
       JOIN users u ON u.id = k.mandor_id
       ${isAdmin ? "" : "WHERE k.mandor_id = ?"}
       ORDER BY k.lokasi, k.nama ASC`,
                isAdmin ? [] : [req.user.id],
            );
            res.json(rows);
        } catch (err) {
            console.error(err);
            res.status(500).json({ error: "Terjadi kesalahan server" });
        }
    },
);

module.exports = router;
