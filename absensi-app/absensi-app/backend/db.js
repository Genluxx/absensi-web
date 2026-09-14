const mysql = require("mysql2/promise");
const bcrypt = require("bcryptjs");

const pool = mysql.createPool({
    host: process.env.DB_HOST || "localhost",
    port: process.env.DB_PORT || 3306,
    user: process.env.DB_USER || "root",
    password: process.env.DB_PASSWORD || "",
    database: process.env.DB_NAME || "absensi_db",
    waitForConnections: true,
    connectionLimit: 10,
});

async function initDb() {
    // Daftar permission yang tersedia di sistem
    await pool.query(`
    CREATE TABLE IF NOT EXISTS permissions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      kode VARCHAR(100) UNIQUE NOT NULL,
      label VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
  `);

    // Role (bisa dibuat baru oleh Super Admin, bukan enum tetap lagi)
    await pool.query(`
    CREATE TABLE IF NOT EXISTS roles (
      id INT AUTO_INCREMENT PRIMARY KEY,
      kode VARCHAR(100) UNIQUE NOT NULL,
      nama VARCHAR(255) NOT NULL,
      is_system BOOLEAN DEFAULT FALSE,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
  `);

    // Relasi role <-> permission (many-to-many)
    await pool.query(`
    CREATE TABLE IF NOT EXISTS role_permissions (
      role_id INT NOT NULL,
      permission_id INT NOT NULL,
      PRIMARY KEY (role_id, permission_id),
      FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
      FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
    )
  `);

    // Users sekarang pakai role_id (relasi), bukan enum
    await pool.query(`
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(100) UNIQUE NOT NULL,
      password VARCHAR(255) NOT NULL,
      nama VARCHAR(255) NOT NULL,
      email VARCHAR(255) UNIQUE DEFAULT NULL,
      reset_token_hash VARCHAR(255) DEFAULT NULL,
      reset_token_expires_at DATETIME DEFAULT NULL,
      role_id INT NOT NULL,
      area VARCHAR(255) DEFAULT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (role_id) REFERENCES roles(id)
    )
  `);

    for (const statement of [
        "ALTER TABLE users ADD COLUMN email VARCHAR(255) UNIQUE DEFAULT NULL",
        "ALTER TABLE users ADD COLUMN reset_token_hash VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE users ADD COLUMN reset_token_expires_at DATETIME DEFAULT NULL",
    ]) {
        try {
            await pool.query(statement);
        } catch (err) {
            if (
                err.code !== "ER_DUP_FIELDNAME" &&
                err.code !== "ER_DUP_KEYNAME"
            )
                throw err;
        }
    }

    await pool.query(`
    CREATE TABLE IF NOT EXISTS karyawan (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nik VARCHAR(50) UNIQUE NOT NULL,
      nama VARCHAR(255) NOT NULL,
      jabatan VARCHAR(255),
      tipe ENUM('kebun', 'pabrik') NOT NULL,
      lokasi VARCHAR(255) NOT NULL,
      mandor_id INT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (mandor_id) REFERENCES users(id)
    )
  `);

    await pool.query(`
    CREATE TABLE IF NOT EXISTS presensi (
      id INT AUTO_INCREMENT PRIMARY KEY,
      karyawan_id INT NOT NULL,
      tanggal DATE NOT NULL,
      jam_masuk TIME DEFAULT NULL,
      status ENUM('Hadir', 'Izin', 'Sakit', 'Telat', 'Alpa') NOT NULL,
      keterangan VARCHAR(500),
      foto_path VARCHAR(500),
      latitude DOUBLE,
      longitude DOUBLE,
      mandor_id INT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (karyawan_id) REFERENCES karyawan(id),
      FOREIGN KEY (mandor_id) REFERENCES users(id),
      UNIQUE KEY unique_presensi_harian (karyawan_id, tanggal)
    )
  `);

    // ---------- SEED: daftar permission dasar ----------
    const daftarPermission = [
        ["karyawan.view", "Lihat data karyawan"],
        ["karyawan.create", "Tambah karyawan"],
        ["karyawan.update", "Edit karyawan"],
        ["karyawan.delete", "Hapus karyawan"],
        ["mandor.view", "Lihat akun mandor"],
        ["mandor.create", "Tambah akun mandor"],
        ["mandor.delete", "Hapus akun mandor"],
        ["presensi.input", "Input presensi"],
        ["presensi.view.own", "Lihat presensi tim sendiri"],
        ["presensi.view.all", "Lihat semua presensi"],
        ["presensi.export", "Export laporan presensi"],
        ["koreksi.request", "Ajukan koreksi presensi"],
        ["koreksi.approve", "Approve/reject koreksi presensi"],
        ["role.manage", "Kelola role & permission"],
    ];

    for (const [kode, label] of daftarPermission) {
        const [exist] = await pool.query(
            "SELECT id FROM permissions WHERE kode = ?",
            [kode],
        );
        if (exist.length === 0) {
            await pool.query(
                "INSERT INTO permissions (kode, label) VALUES (?, ?)",
                [kode, label],
            );
        }
    }

    // ---------- SEED: role dasar (system role, tidak bisa dihapus) ----------
    const [adaSuperAdmin] = await pool.query(
        "SELECT id FROM roles WHERE kode = ?",
        ["super_admin"],
    );
    if (adaSuperAdmin.length === 0) {
        // Super Admin: semua permission
        const [semuaPermission] = await pool.query(
            "SELECT id, kode FROM permissions",
        );
        const [rSuperAdmin] = await pool.query(
            "INSERT INTO roles (kode, nama, is_system) VALUES (?, ?, TRUE)",
            ["super_admin", "Super Admin"],
        );
        for (const p of semuaPermission) {
            await pool.query(
                "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                [rSuperAdmin.insertId, p.id],
            );
        }

        // Admin HR
        const [rAdminHr] = await pool.query(
            "INSERT INTO roles (kode, nama, is_system) VALUES (?, ?, TRUE)",
            ["admin_hr", "Admin HR"],
        );
        const permAdminHr = [
            "karyawan.view",
            "karyawan.create",
            "karyawan.update",
            "karyawan.delete",
            "mandor.view",
            "mandor.create",
            "mandor.delete",
            "presensi.view.all",
            "presensi.export",
            "koreksi.approve",
        ];
        for (const kode of permAdminHr) {
            const p = semuaPermission.find((x) => x.kode === kode);
            if (p)
                await pool.query(
                    "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                    [rAdminHr.insertId, p.id],
                );
        }

        // Mandor Kebun
        const [rMandorKebun] = await pool.query(
            "INSERT INTO roles (kode, nama, is_system) VALUES (?, ?, TRUE)",
            ["mandor_kebun", "Mandor Kebun"],
        );
        // Mandor Pabrik
        const [rMandorPabrik] = await pool.query(
            "INSERT INTO roles (kode, nama, is_system) VALUES (?, ?, TRUE)",
            ["mandor_pabrik", "Mandor Pabrik"],
        );
        const permMandor = [
            "karyawan.view",
            "presensi.input",
            "presensi.view.own",
            "presensi.export",
            "koreksi.request",
        ];
        for (const kode of permMandor) {
            const p = semuaPermission.find((x) => x.kode === kode);
            if (p) {
                await pool.query(
                    "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                    [rMandorKebun.insertId, p.id],
                );
                await pool.query(
                    "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                    [rMandorPabrik.insertId, p.id],
                );
            }
        }

        // ---------- SEED: akun & data awal ----------
        const hashSuperAdmin = await bcrypt.hash("super123", 10);
        const hashAdmin = await bcrypt.hash("admin123", 10);
        const hashKebun = await bcrypt.hash("kebun123", 10);
        const hashPabrik = await bcrypt.hash("pabrik123", 10);

        await pool.query(
            "INSERT INTO users (username, password, nama, role_id, area) VALUES (?, ?, ?, ?, ?)",
            [
                "superadmin",
                hashSuperAdmin,
                "Super Admin",
                rSuperAdmin.insertId,
                null,
            ],
        );
        await pool.query(
            "INSERT INTO users (username, password, nama, role_id, area) VALUES (?, ?, ?, ?, ?)",
            ["admin", hashAdmin, "Ir. Budi Santoso", rAdminHr.insertId, null],
        );
        const [mk1] = await pool.query(
            "INSERT INTO users (username, password, nama, role_id, area) VALUES (?, ?, ?, ?, ?)",
            [
                "mandorkebun1",
                hashKebun,
                "Ahmad Dahlan",
                rMandorKebun.insertId,
                "Divisi I - Blok A",
            ],
        );
        const [mp1] = await pool.query(
            "INSERT INTO users (username, password, nama, role_id, area) VALUES (?, ?, ?, ?, ?)",
            [
                "mandorpabrik1",
                hashPabrik,
                "Joko Susilo",
                rMandorPabrik.insertId,
                "Stasiun Rebusan & Press",
            ],
        );

        await pool.query(
            `INSERT INTO karyawan (nik, nama, jabatan, tipe, lokasi, mandor_id) VALUES
       ('NIK-1001', 'Ujang Supriatna', 'Pemanen Utama', 'kebun', 'Divisi I - Blok A', ?),
       ('NIK-1002', 'Sutarman', 'Pemanen', 'kebun', 'Divisi I - Blok A', ?),
       ('NIK-2001', 'Eko Prasetyo', 'Operator Sterilizer', 'pabrik', 'Stasiun Rebusan & Press', ?),
       ('NIK-2002', 'Agus Kuncoro', 'Operator Press Engine', 'pabrik', 'Stasiun Rebusan & Press', ?)`,
            [mk1.insertId, mk1.insertId, mp1.insertId, mp1.insertId],
        );

        console.log("Sistem permission + data awal berhasil dibuat.");
        console.log(
            "Akun: superadmin/super123, admin/admin123, mandorkebun1/kebun123, mandorpabrik1/pabrik123",
        );
    }
}

module.exports = { pool, initDb };
