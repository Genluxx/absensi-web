const jwt = require("jsonwebtoken");
const JWT_SECRET = process.env.JWT_SECRET || "ganti_dengan_secret_yang_aman";

function verifyToken(req, res, next) {
  const authHeader = req.headers.authorization;
  if (!authHeader || !authHeader.startsWith("Bearer ")) {
    return res.status(401).json({ error: "Token tidak ditemukan" });
  }

  const token = authHeader.split(" ")[1];
  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.user = decoded; // { id, username, nama, roleKode, roleNama, area, permissions: [...] }
    next();
  } catch (err) {
    return res.status(401).json({ error: "Token tidak valid atau kedaluwarsa" });
  }
}

// Middleware baru: cek permission, bukan role
function requirePermission(...permissionDibutuhkan) {
  return (req, res, next) => {
    const permissionUser = req.user.permissions || [];
    const punyaAkses = permissionDibutuhkan.some((p) => permissionUser.includes(p));

    if (!punyaAkses) {
      return res.status(403).json({ error: "Kamu tidak punya izin untuk aksi ini" });
    }
    next();
  };
}

module.exports = { verifyToken, requirePermission };