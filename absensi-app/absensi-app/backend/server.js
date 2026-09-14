require("dotenv").config();
const express = require("express");
const cors = require("cors");
const path = require("path");
const { initDb } = require("./db");

const authRoutes = require("./routes/auth");
const presensiRoutes = require("./routes/presensi");
const adminRoutes = require("./routes/admin");

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());
app.use("/uploads", express.static(path.join(__dirname, "uploads")));

app.use("/api/auth", authRoutes);
app.use("/api/presensi", presensiRoutes);
app.use("/api/admin", adminRoutes);

app.get("/", (req, res) => {
  res.json({ status: "OK", message: "Absensi API berjalan" });
});

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function start() {
  const maxRetries = 15;
  let attempt = 0;

  while (attempt < maxRetries) {
    try {
      await initDb();
      app.listen(PORT, () => {
        console.log(`Server absensi jalan di port ${PORT}`);
      });
      return;
    } catch (err) {
      attempt++;
      console.log(`Menunggu database siap... (percobaan ${attempt}/${maxRetries})`);
      await sleep(2000);
    }
  }

  console.error("Gagal konek ke database setelah beberapa percobaan. Server berhenti.");
  process.exit(1);
}

start();