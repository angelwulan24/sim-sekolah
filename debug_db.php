<?php
$conn = new mysqli('localhost', 'root', '', 'sim_sekolah');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$res = $conn->query("
    SELECT t.id_tagihan, t.status, t.tgl_pembayaran, j.nama_tagihan, j.nominal_tagihan 
    FROM tagihan_siswa t 
    JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan 
    WHERE t.nis = '11111111'
")->fetch_all(MYSQLI_ASSOC);
file_put_contents('debug_data.txt', print_r($res, true));
echo "Done";
$conn->close();
?>



