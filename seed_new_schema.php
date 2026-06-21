<?php
$conn = new mysqli('localhost', 'root', '123', 'sim_sekolah', 33060);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Dropping all current tables and views...\n";
$conn->query("SET FOREIGN_KEY_CHECKS = 0;");
$tables = ['tagihan_siswa', 'tagihan', 'pemasukan', 'lainnya', 'jenis_tagihan', 'pembayaran', 'gaji', 'siswa', 'kelas', 'guru', 'users', 'pengeluaran', 'laporan'];
foreach ($tables as $t) {
    if (!$conn->query("DROP TABLE IF EXISTS `$t` CASCADE;")) {
        echo "Error dropping table $t: " . $conn->error . "\n";
    }
}
$conn->query("DROP VIEW IF EXISTS `v_pengeluaran_gabungan` CASCADE;");

echo "Reading and executing schema.sql...\n";
$sql = file_get_contents('schema.sql');
if ($conn->multi_query($sql)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
} else {
    die("Error importing schema.sql: " . $conn->error . "\n");
}

echo "Database schema recreated successfully.\n";

echo "Seeding default data...\n";
$conn->query("SET FOREIGN_KEY_CHECKS = 1;");

$p1 = password_hash('123456', PASSWORD_DEFAULT);

// Seed Users: Admin, student
if (!$conn->query("INSERT INTO users (id_users, email, password, nama_users, role, gambar) VALUES 
  (1, 'admin@gmail.com', '$p1', 'Administrator', 1, 'user.png'),
  (2, '11111111', '$p1', 'Angelina Wulandari', 3, 'user.png')")) {
    die("Error seeding users: " . $conn->error . "\n");
}

// Seed Guru
if (!$conn->query("INSERT INTO guru (NUPTK, nama_guru, jk_guru, agama_guru, bidang_studi, alamat_guru, status_guru, telp_guru, foto_guru) VALUES 
  ('1001', 'Nurul Nuraeni', 'Perempuan', 'Islam', 'Pendidikan Agama', 'Jl. Cikande', 'Aktif', '08123456789', 'foto.jpg')")) {
    die("Error seeding guru: " . $conn->error . "\n");
}

// Seed Kelas
if (!$conn->query("INSERT INTO kelas (id_kelas, nama_kelas, NUPTK, ket_kelas) VALUES 
  (1, 'Kelas 1', '1001', 'Wali Kelas Agama')")) {
    die("Error seeding kelas: " . $conn->error . "\n");
}

// Seed Siswa
if (!$conn->query("INSERT INTO siswa (nis_siswa, nama_siswa, jk_siswa, agama_siswa, status_siswa, ortu_wali, tempat_lahirsiswa, tgl_lahirsiswa, alamat_ssiwa, telp_siswa, id_kelas, foto_siswa, tgl_masuk, thn_ajaran, id_users) VALUES 
  ('11111111', 'Angelina Wulandari', 'Perempuan', 'Islam', 'Aktif', 'Kastini', 'Serang', '2019-11-24', 'Cikande Permai', '085216165214', 1, 'avatar.jpg', '2026-04-20', '2025/2026', 2)")) {
    die("Error seeding siswa: " . $conn->error . "\n");
}

// Seed Jenis Tagihan
if (!$conn->query("INSERT INTO jenis_tagihan (kode_tagihan, nama_tagihan, nominal_tagihan, tenggat_waktu, tahun_ajaran, id_kelas) VALUES 
  ('KM-0001', 'SPP - Juli', '15000', 'Setiap Bulan', '2025/2026', 1)")) {
    die("Error seeding jenis_tagihan: " . $conn->error . "\n");
}

// Seed Tagihan Siswa
if (!$conn->query("INSERT INTO tagihan_siswa (id_tagihan, id_pemasukan, nis_siswa, kode_tagihan, status, tgl_pembayaran) VALUES 
  (1, NULL, '11111111', 'KM-0001', 'Belum Lunas', NULL)")) {
    die("Error seeding tagihan_siswa: " . $conn->error . "\n");
}

echo "Seeding completed successfully.\n";
$conn->close();
?>
