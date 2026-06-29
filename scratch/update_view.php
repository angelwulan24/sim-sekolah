<?php
$conn = new mysqli('localhost', 'root', '123', 'sim_sekolah', 33060);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

$sql = "CREATE OR REPLACE VIEW `v_pengeluaran_gabungan` AS
select 
    `pengeluaran`.`id_pengeluaran` AS `id`,
    `pengeluaran`.`sekarang` AS `sekarang`,
    `pengeluaran`.`tanggal` AS `tanggal`,
    `pengeluaran`.`ket_pengeluaran` AS `keterangan`,
    `pengeluaran`.`nominal_pengeluaran` AS `nominal`,
    `pengeluaran`.`bukti` AS `bukti`
from `pengeluaran`
union all
select 
    `g`.`id_gaji` AS `id`,
    `g`.`periode` AS `sekarang`,
    `g`.`tanggal` AS `tanggal`,
    concat('Pembayaran Gaji Guru: ', `u`.`nama_guru`, ' (', `g`.`periode`, ')') AS `keterangan`,
    (`g`.`jam` * `g`.`nominal_gaji`) AS `nominal`,
    '' AS `bukti`
from (`gaji` `g` join `guru` `u` on(`g`.`NUPTK` = `u`.`NUPTK`))";

if ($conn->query($sql)) {
    echo "View berhasil diperbarui!\n";
} else {
    echo "Error: " . $conn->error . "\n";
}

$conn->close();
?>
