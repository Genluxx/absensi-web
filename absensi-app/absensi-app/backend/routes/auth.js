const express = require("express");
const bcrypt = require("bcryptjs");
const jwt = require("jsonwebtoken");
const crypto = require("crypto");
const nodemailer = require("nodemailer");
const { pool } = require("../db");

const router = express.Router();
const JWT_SECRET = process.env.JWT_SECRET || "ganti_dengan_secret_yang_aman";

const mailer = nodemailer.createTransport({
    host: process.env.MAIL_HOST || "localhost",
    port: Number(process.env.MAIL_PORT || 1025),
    secure: process.env.MAIL_SECURE === "true",
    auth: process.env.MAIL_USER
        ? { user: process.env.MAIL_USER, pass: process.env.MAIL_PASSWORD || "" }
        : undefined,
});

async function ambilPermissionUser(roleId) {
    const [rows] = await pool.query(
        `SELECT p.kode FROM role_permissions rp
     JOIN permissions p ON p.id = rp.permission_id
     WHERE rp.role_id = ?`,
        [roleId],
    );
    return rows.map((r) => r.kode);
}

router.post("/login", async (req, res) => {
    try {
        const { username, password } = req.body;
        if (!username || !password) {
            return res
                .status(400)
                .json({ error: "Username dan password wajib diisi" });
        }

        const [rows] = await pool.query(
            `SELECT u.*, r.kode AS role_kode, r.nama AS role_nama FROM users u
       JOIN roles r ON r.id = u.role_id
       WHERE u.username = ?`,
            [username],
        );
        const user = rows[0];

        if (!user || !(await bcrypt.compare(password, user.password))) {
            return res
                .status(401)
                .json({ error: "Username atau password salah" });
        }

        const permissions = await ambilPermissionUser(user.role_id);

        const token = jwt.sign(
            {
                id: user.id,
                username: user.username,
                nama: user.nama,
                role: user.role_kode,
                roleId: user.role_id,
                roleKode: user.role_kode,
                role: user.role_kode,
                roleNama: user.role_nama,
                area: user.area,
                permissions,
            },
            JWT_SECRET,
            { expiresIn: "7d" },
        );

        res.json({
            token,
            user: {
                id: user.id,
                username: user.username,
                nama: user.nama,
                roleKode: user.role_kode,
                roleNama: user.role_nama,
                area: user.area,
                permissions,
            },
        });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

router.post("/register", async (req, res) => {
    try {
        const { username, nama, email, password, area } = req.body;
        if (!username || !nama || !email || !password || !area) {
            return res.status(400).json({ error: "Semua field wajib diisi" });
        }
        if (password.length < 8)
            return res
                .status(400)
                .json({ error: "Password minimal 8 karakter" });
        const [existing] = await pool.query(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [username, email],
        );
        if (existing.length)
            return res
                .status(409)
                .json({ error: "Username atau email sudah digunakan" });
        const [role] = await pool.query(
            "SELECT id FROM roles WHERE kode = 'mandor_kebun' LIMIT 1",
        );
        if (!role.length)
            return res
                .status(500)
                .json({ error: "Role default belum tersedia" });
        const hash = await bcrypt.hash(password, 10);
        await pool.query(
            "INSERT INTO users (username, password, nama, email, role_id, area) VALUES (?, ?, ?, ?, ?, ?)",
            [username, hash, nama, email, role[0].id, area],
        );
        res.status(201).json({ message: "Akun berhasil dibuat" });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

router.post("/forgot-password", async (req, res) => {
    try {
        const { email } = req.body;
        if (!email) return res.status(400).json({ error: "Email wajib diisi" });
        const [rows] = await pool.query(
            "SELECT id FROM users WHERE email = ?",
            [email],
        );
        if (rows.length) {
            const rawToken = crypto.randomBytes(32).toString("hex");
            const tokenHash = crypto
                .createHash("sha256")
                .update(rawToken)
                .digest("hex");
            await pool.query(
                "UPDATE users SET reset_token_hash = ?, reset_token_expires_at = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE id = ?",
                [tokenHash, rows[0].id],
            );
            const resetUrl = `${process.env.MOBILE_RESET_URL || "http://localhost:3000/reset-password"}?token=${rawToken}&email=${encodeURIComponent(email)}`;
            await mailer.sendMail({
                from: process.env.MAIL_FROM || "noreply@absensi.local",
                to: email,
                subject: "Reset password SAP.HRIS",
                text: `Gunakan link berikut dalam 30 menit untuk membuat password baru: ${resetUrl}`,
            });
        }
        res.json({ message: "Jika email terdaftar, link reset sudah dikirim" });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Email reset tidak dapat dikirim" });
    }
});

router.post("/reset-password", async (req, res) => {
    try {
        const { email, token, password } = req.body;
        if (!email || !token || !password)
            return res
                .status(400)
                .json({ error: "Email, token, dan password wajib diisi" });
        if (password.length < 8)
            return res
                .status(400)
                .json({ error: "Password minimal 8 karakter" });
        const tokenHash = crypto
            .createHash("sha256")
            .update(token)
            .digest("hex");
        const [rows] = await pool.query(
            "SELECT id FROM users WHERE email = ? AND reset_token_hash = ? AND reset_token_expires_at > NOW()",
            [email, tokenHash],
        );
        if (!rows.length)
            return res
                .status(400)
                .json({ error: "Token reset tidak valid atau kedaluwarsa" });
        await pool.query(
            "UPDATE users SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?",
            [await bcrypt.hash(password, 10), rows[0].id],
        );
        res.json({ message: "Password berhasil diubah" });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: "Terjadi kesalahan server" });
    }
});

module.exports = router;
