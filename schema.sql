-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sim_sekolah
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `guru`
--

DROP TABLE IF EXISTS `guru`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `sex` varchar(15) DEFAULT NULL,
  `nip` varchar(15) NOT NULL,
  `bidang` varchar(40) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `status` enum('Berhenti','Cuti','Aktif') NOT NULL,
  `number` varchar(15) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(15) NOT NULL,
  `wali` varchar(50) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gaji`
--

DROP TABLE IF EXISTS `gaji`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gaji` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_guru` int(11) NOT NULL,
  `periode` varchar(20) NOT NULL,
  `jam` varchar(4) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `time` date NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_gaji_guru` (`id_guru`),
  CONSTRAINT `fk_gaji_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `nis` varchar(15) NOT NULL,
  `sex` varchar(15) DEFAULT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Aktif',
  `orangtua_wali` varchar(50) NOT NULL,
  `tempat` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `telpon` varchar(20) DEFAULT '',
  `kelas` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT 'Loket',
  `foto` varchar(255) DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_siswa_kelas` (`kelas`),
  CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`kelas`) REFERENCES `kelas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tagihan`
--

DROP TABLE IF EXISTS `tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tagihan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(50) DEFAULT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `jenis_tagihan` varchar(100) DEFAULT NULL,
  `nominal` varchar(20) DEFAULT NULL,
  `tahun_ajaran` varchar(50) DEFAULT NULL,
  `tenggat_waktu` date DEFAULT NULL,
  `status` enum('Belum Lunas','Lunas') DEFAULT 'Belum Lunas',
  `waktu_bayar` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_siswa_tagihan` (`id_siswa`,`status`),
  KEY `idx_jenis_tagihan` (`jenis_tagihan`),
  CONSTRAINT `fk_tagihan_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=446 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lainnya`
--

DROP TABLE IF EXISTS `lainnya`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lainnya` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sekarang` varchar(15) NOT NULL,
  `time` date NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `laporan`
--

DROP TABLE IF EXISTS `laporan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saldo_awal` varchar(15) NOT NULL DEFAULT '0',
  `kas_masuk` varchar(15) DEFAULT '0',
  `kas_keluar` varchar(15) NOT NULL DEFAULT '0',
  `tanggal` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `tenggat_waktu` varchar(50) DEFAULT NULL,
  `tipe` enum('KM','KK') NOT NULL,
  `kode` varchar(10) NOT NULL,
  `tahun_ajaran` varchar(50) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pembayaran_nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pengeluaran`
--

DROP TABLE IF EXISTS `pengeluaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pengeluaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nominal` varchar(12) NOT NULL,
  `sekarang` varchar(10) NOT NULL,
  `time` date NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bukti` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `telpon` varchar(20) DEFAULT '',
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` int(11) NOT NULL,
  `active` enum('1','0') NOT NULL,
  `gambar` varchar(255) NOT NULL DEFAULT 'user.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v_pengeluaran_gabungan`
--

DROP TABLE IF EXISTS `v_pengeluaran_gabungan`;
/*!50001 DROP VIEW IF EXISTS `v_pengeluaran_gabungan`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_pengeluaran_gabungan` (
  `id` tinyint NOT NULL,
  `sekarang` tinyint NOT NULL,
  `tanggal` tinyint NOT NULL,
  `keterangan` tinyint NOT NULL,
  `nominal` tinyint NOT NULL,
  `bukti` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_pengeluaran_gabungan`
--

/*!50001 DROP TABLE IF EXISTS `v_pengeluaran_gabungan`*/;
/*!50001 DROP VIEW IF EXISTS `v_pengeluaran_gabungan`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_pengeluaran_gabungan` AS select `pengeluaran`.`id` AS `id`,`pengeluaran`.`sekarang` AS `sekarang`,`pengeluaran`.`tanggal` AS `tanggal`,`pengeluaran`.`keterangan` AS `keterangan`,`pengeluaran`.`nominal` AS `nominal`,`pengeluaran`.`bukti` AS `bukti` from `pengeluaran` union all select `g`.`id` AS `id`,`g`.`periode` AS `sekarang`,`g`.`tanggal` AS `tanggal`,concat('Pembayaran Gaji Guru: ',`u`.`name`) AS `keterangan`,`g`.`jam` * `g`.`nominal` AS `nominal`,'' AS `bukti` from (`gaji` `g` join `guru` `u` on(`g`.`id_guru` = `u`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
