<?php
/**
 * Migration script: Database Refactoring
 * 1. Add gaji → pengeluaran FK
 * 2. Migrate existing gaji to pengeluaran
 * 3. Drop sekarang from pengeluaran & pemasukan
 * 4. Create kas_awal table from laporan saldo_awal
 * 5. Create v_laporan view (aggregate)
 * 6. Drop tabel laporan
 * 7. Simplify v_pengeluaran_gabungan
 */

$conn = new mysqli('localhost', 'root', '123', 'sim_sekolah', 33060);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
$conn->set_charset('utf8mb4');

echo "=== Database Refactoring Migration ===\n\n";

// Helper function
function run($conn, $sql, $desc) {
    if ($conn->query($sql)) {
        echo "  [OK] $desc\n";
        return true;
    } else {
        echo "  [ERR] $desc: " . $conn->error . "\n";
        return false;
    }
}

$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// -------------------------------------------------------
// STEP 1a: Add id_pengeluaran (nullable) to gaji
// -------------------------------------------------------
echo "\n[Step 1a] Add id_pengeluaran column to gaji...\n";
// Check if column already exists
$check = $conn->query("SHOW COLUMNS FROM gaji LIKE 'id_pengeluaran'");
if ($check->num_rows == 0) {
    run($conn, "ALTER TABLE gaji ADD COLUMN id_pengeluaran INT(11) DEFAULT NULL", "Add id_pengeluaran to gaji");
} else {
    echo "  [SKIP] id_pengeluaran already exists\n";
}

// -------------------------------------------------------
// STEP 1b: Migrate existing gaji → create pengeluaran records
// -------------------------------------------------------
echo "\n[Step 1b] Migrate existing gaji records to pengeluaran...\n";
$gaji_rows = $conn->query("
    SELECT g.id_gaji, g.NUPTK, g.periode, g.jam, g.nominal_gaji, g.tgl_gaji, u.nama_guru
    FROM gaji g
    JOIN guru u ON g.NUPTK = u.NUPTK
    WHERE g.id_pengeluaran IS NULL
");
$migrated = 0;
while ($row = $gaji_rows->fetch_assoc()) {
    $total = intval($row['jam']) * intval($row['nominal_gaji']);
    $ket = $conn->real_escape_string("Pembayaran Gaji: " . $row['nama_guru'] . " (" . $row['periode'] . ")");
    $tgl = $conn->real_escape_string($row['tgl_gaji'] ?: date('Y-m-d'));
    $skrg = date('ymd', strtotime($tgl)); // sekarang pseudo-ID, will be dropped later
    
    // Check if sekarang column still exists
    $has_sekarang = $conn->query("SHOW COLUMNS FROM pengeluaran LIKE 'sekarang'")->num_rows > 0;
    if ($has_sekarang) {
        $conn->query("INSERT INTO pengeluaran (nominal_pengeluaran, sekarang, tgl_pengeluaran, ket_pengeluaran) VALUES ('$total', '$skrg', '$tgl', '$ket')");
    } else {
        $conn->query("INSERT INTO pengeluaran (nominal_pengeluaran, tgl_pengeluaran, ket_pengeluaran) VALUES ('$total', '$tgl', '$ket')");
    }
    $pen_id = $conn->insert_id;

    
    // Link back to gaji
    $conn->query("UPDATE gaji SET id_pengeluaran = $pen_id WHERE id_gaji = " . $row['id_gaji']);
    $migrated++;
}
echo "  [OK] Migrated $migrated gaji records to pengeluaran\n";

// -------------------------------------------------------
// STEP 1b: Add FK constraint (after data migration)
// -------------------------------------------------------
echo "\n[Step 1b-FK] Add FK constraint gaji.id_pengeluaran → pengeluaran...\n";
// Check if FK already exists
$fk_check = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_NAME='gaji' AND CONSTRAINT_NAME='fk_gaji_pengeluaran' AND TABLE_SCHEMA='sim_sekolah'");
if ($fk_check->num_rows == 0) {
    run($conn, "ALTER TABLE gaji ADD CONSTRAINT fk_gaji_pengeluaran FOREIGN KEY (id_pengeluaran) REFERENCES pengeluaran(id_pengeluaran) ON DELETE SET NULL ON UPDATE CASCADE",
        "Add FK gaji.id_pengeluaran → pengeluaran");
} else {
    echo "  [SKIP] FK already exists\n";
}

// -------------------------------------------------------
// STEP 1c: Drop sekarang from pengeluaran
// -------------------------------------------------------
echo "\n[Step 1c] Drop sekarang from pengeluaran...\n";
$chk = $conn->query("SHOW COLUMNS FROM pengeluaran LIKE 'sekarang'");
if ($chk->num_rows > 0) {
    run($conn, "ALTER TABLE pengeluaran DROP COLUMN sekarang", "Drop sekarang from pengeluaran");
} else {
    echo "  [SKIP] sekarang already not in pengeluaran\n";
}

// -------------------------------------------------------
// STEP 1d: Drop sekarang from pemasukan
// -------------------------------------------------------
echo "\n[Step 1d] Drop sekarang from pemasukan...\n";
$chk = $conn->query("SHOW COLUMNS FROM pemasukan LIKE 'sekarang'");
if ($chk->num_rows > 0) {
    run($conn, "ALTER TABLE pemasukan DROP COLUMN sekarang", "Drop sekarang from pemasukan");
} else {
    echo "  [SKIP] sekarang already not in pemasukan\n";
}

// -------------------------------------------------------
// STEP 1e: Create kas_awal table + migrate saldo
// -------------------------------------------------------
echo "\n[Step 1e] Create kas_awal table and migrate saldo_awal...\n";
run($conn, "CREATE TABLE IF NOT EXISTS `kas_awal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saldo_awal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` varchar(100) DEFAULT NULL,
  `tanggal` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1", "Create kas_awal table");

// Migrate saldo_awal from laporan (take the oldest entry's saldo_awal)
$laporan_check = $conn->query("SHOW TABLES LIKE 'laporan'");
if ($laporan_check->num_rows > 0) {
    $kas_awal_count = $conn->query("SELECT COUNT(*) AS cnt FROM kas_awal")->fetch_assoc()['cnt'];
    if ($kas_awal_count == 0) {
        $oldest = $conn->query("SELECT saldo_awal, tanggal FROM laporan ORDER BY tanggal ASC LIMIT 1")->fetch_assoc();
        if ($oldest) {
            $saldo = intval($oldest['saldo_awal']);
            $tgl = $oldest['tanggal'];
            run($conn, "INSERT INTO kas_awal (saldo_awal, keterangan, tanggal) VALUES ('$saldo', 'Saldo awal migrasi dari tabel laporan', '$tgl')",
                "Migrate saldo_awal $saldo from laporan");
        } else {
            run($conn, "INSERT INTO kas_awal (saldo_awal, keterangan, tanggal) VALUES (0, 'Saldo awal default', NOW())",
                "Insert default saldo_awal = 0");
        }
    } else {
        echo "  [SKIP] kas_awal already has data\n";
    }
}

// -------------------------------------------------------
// STEP 1f: Create v_laporan view (dynamic aggregate)
// -------------------------------------------------------
echo "\n[Step 1f] Create v_laporan aggregate view...\n";
run($conn, "CREATE OR REPLACE VIEW `v_laporan` AS
SELECT 
    tanggal,
    SUM(kas_masuk) AS kas_masuk,
    SUM(kas_keluar) AS kas_keluar
FROM (
    -- Pemasukan lainnya
    SELECT DATE(tgl_pemasukan) AS tanggal, CAST(nominal_pemasukan AS DECIMAL(15,2)) AS kas_masuk, 0 AS kas_keluar
    FROM pemasukan
    UNION ALL
    -- Tagihan siswa yang lunas
    SELECT DATE(ts.tgl_pembayaran) AS tanggal, CAST(j.nominal_tagihan AS DECIMAL(15,2)) AS kas_masuk, 0 AS kas_keluar
    FROM tagihan_siswa ts
    JOIN jenis_tagihan j ON ts.kode_tagihan = j.kode_tagihan
    WHERE ts.status = 'Lunas' AND ts.tgl_pembayaran IS NOT NULL
    UNION ALL
    -- Pengeluaran (termasuk gaji yang sudah ada di sini via FK)
    SELECT DATE(tgl_pengeluaran) AS tanggal, 0 AS kas_masuk, CAST(nominal_pengeluaran AS DECIMAL(15,2)) AS kas_keluar
    FROM pengeluaran
) AS agg
GROUP BY tanggal", "Create v_laporan view");

// -------------------------------------------------------
// STEP 1g: Drop tabel laporan
// -------------------------------------------------------
echo "\n[Step 1g] Drop tabel laporan...\n";
run($conn, "DROP TABLE IF EXISTS laporan", "Drop tabel laporan");

// -------------------------------------------------------
// STEP 1h: Update v_pengeluaran_gabungan (simplify - no more UNION)
// -------------------------------------------------------
echo "\n[Step 1h] Update v_pengeluaran_gabungan (simplified - no UNION)...\n";
run($conn, "CREATE OR REPLACE VIEW `v_pengeluaran_gabungan` AS
SELECT 
    p.id_pengeluaran AS id,
    DATE_FORMAT(p.tgl_pengeluaran,'%Y-%m-%d') AS tanggal,
    p.ket_pengeluaran AS keterangan,
    CAST(p.nominal_pengeluaran AS DECIMAL(15,2)) AS nominal,
    p.bukti
FROM pengeluaran p", "Update v_pengeluaran_gabungan");

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "\n=== Migration Complete! ===\n";
$conn->close();
?>
