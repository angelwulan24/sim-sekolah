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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_users` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_users` varchar(100) NOT NULL,
  `role` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL DEFAULT 'user.png',
  PRIMARY KEY (`id_users`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `guru`
--

DROP TABLE IF EXISTS `guru`;
CREATE TABLE `guru` (
  `NUPTK` varchar(20) NOT NULL,
  `nama_guru` varchar(50) NOT NULL,
  `jk_guru` varchar(15) DEFAULT NULL,
  `agama_guru` varchar(20) DEFAULT NULL,
  `bidang_studi` varchar(40) NOT NULL,
  `alamat_guru` varchar(100) NOT NULL,
  `status_guru` enum('Berhenti','Cuti','Aktif') NOT NULL,
  `telp_guru` varchar(15) NOT NULL,
  `foto_guru` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`NUPTK`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas` (
  `id_kelas` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(15) NOT NULL,
  `NUPTK` varchar(20) DEFAULT NULL,
  `ket_kelas` varchar(100) NOT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `fk_kelas_guru` (`NUPTK`),
  CONSTRAINT `fk_kelas_guru` FOREIGN KEY (`NUPTK`) REFERENCES `guru` (`NUPTK`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa` (
  `nis_siswa` varchar(15) NOT NULL,
  `nama_siswa` varchar(50) NOT NULL,
  `jk_siswa` varchar(15) DEFAULT NULL,
  `agama_siswa` varchar(20) DEFAULT NULL,
  `status_siswa` varchar(50) NOT NULL DEFAULT 'Aktif',
  `ortu_wali` varchar(50) NOT NULL,
  `tempat_lahirsiswa` varchar(20) NOT NULL,
  `tgl_lahirsiswa` date NOT NULL,
  `alamat_ssiwa` varchar(100) NOT NULL,
  `telp_siswa` varchar(20) DEFAULT '',
  `id_kelas` int(11) DEFAULT NULL,
  `foto_siswa` varchar(255) DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `thn_ajaran` varchar(20) DEFAULT NULL,
  `id_users` int(11) DEFAULT NULL,
  PRIMARY KEY (`nis_siswa`),
  KEY `fk_siswa_kelas` (`id_kelas`),
  KEY `fk_siswa_users` (`id_users`),
  CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_siswa_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gaji`
--

DROP TABLE IF EXISTS `gaji`;
CREATE TABLE `gaji` (
  `id_gaji` int(11) NOT NULL AUTO_INCREMENT,
  `NUPTK` varchar(20) NOT NULL,
  `periode` varchar(20) NOT NULL,
  `jam` varchar(4) NOT NULL,
  `nominal_gaji` varchar(12) NOT NULL,
  `tgl_gaji` date NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_gaji`),
  KEY `fk_gaji_guru` (`NUPTK`),
  CONSTRAINT `fk_gaji_guru` FOREIGN KEY (`NUPTK`) REFERENCES `guru` (`NUPTK`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `jenis_tagihan`
--

DROP TABLE IF EXISTS `jenis_tagihan`;
CREATE TABLE `jenis_tagihan` (
  `kode_tagihan` varchar(10) NOT NULL,
  `nama_tagihan` varchar(50) NOT NULL,
  `nominal_tagihan` varchar(12) NOT NULL,
  `tenggat_waktu` varchar(50) DEFAULT NULL,
  `tahun_ajaran` varchar(50) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  PRIMARY KEY (`kode_tagihan`),
  KEY `fk_jenis_tagihan_kelas` (`id_kelas`),
  CONSTRAINT `fk_jenis_tagihan_kelas` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `pemasukan`
--

DROP TABLE IF EXISTS `pemasukan`;
CREATE TABLE `pemasukan` (
  `id_pemasukan` int(11) NOT NULL AUTO_INCREMENT,
  `sekarang` varchar(15) NOT NULL,
  `tgl_pemasukan` date NOT NULL,
  `ket_pemasukan` varchar(100) NOT NULL,
  `nominal_pemasukan` varchar(12) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pemasukan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tagihan_siswa`
--

DROP TABLE IF EXISTS `tagihan_siswa`;
CREATE TABLE `tagihan_siswa` (
  `id_tagihan` int(11) NOT NULL AUTO_INCREMENT,
  `id_pemasukan` int(11) DEFAULT NULL,
  `nis_siswa` varchar(15) NOT NULL,
  `kode_tagihan` varchar(10) NOT NULL,
  `status` enum('Belum Lunas','Lunas') DEFAULT 'Belum Lunas',
  `tgl_pembayaran` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tagihan`),
  KEY `fk_tagihan_pemasukan` (`id_pemasukan`),
  KEY `fk_tagihan_siswa_nis` (`nis_siswa`),
  KEY `fk_tagihan_jenis` (`kode_tagihan`),
  CONSTRAINT `fk_tagihan_pemasukan` FOREIGN KEY (`id_pemasukan`) REFERENCES `pemasukan` (`id_pemasukan`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_tagihan_siswa_nis` FOREIGN KEY (`nis_siswa`) REFERENCES `siswa` (`nis_siswa`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tagihan_jenis` FOREIGN KEY (`kode_tagihan`) REFERENCES `jenis_tagihan` (`kode_tagihan`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `pengeluaran`
--

DROP TABLE IF EXISTS `pengeluaran`;
CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT,
  `nominal_pengeluaran` varchar(12) NOT NULL,
  `sekarang` varchar(10) NOT NULL,
  `tgl_pengeluaran` date NOT NULL,
  `ket_pengeluaran` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `bukti` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pengeluaran`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `laporan`
--

DROP TABLE IF EXISTS `laporan`;
CREATE TABLE `laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saldo_awal` varchar(15) NOT NULL DEFAULT '0',
  `kas_masuk` varchar(15) DEFAULT '0',
  `kas_keluar` varchar(15) NOT NULL DEFAULT '0',
  `tanggal` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
/*!50001 VIEW `v_pengeluaran_gabungan` AS select `pengeluaran`.`id_pengeluaran` AS `id`,`pengeluaran`.`sekarang` AS `sekarang`,`pengeluaran`.`tanggal` AS `tanggal`,`pengeluaran`.`ket_pengeluaran` AS `keterangan`,`pengeluaran`.`nominal_pengeluaran` AS `nominal`,`pengeluaran`.`bukti` AS `bukti` from `pengeluaran` union all select `g`.`id_gaji` AS `id`,`g`.`periode` AS `sekarang`,`g`.`tanggal` AS `tanggal`,concat('Pembayaran Gaji Guru: ',`u`.`nama_guru`) AS `keterangan`,(`g`.`jam` * `g`.`nominal_gaji`) AS `nominal`,'' AS `bukti` from (`gaji` `g` join `guru` `u` on(`g`.`NUPTK` = `u`.`NUPTK`)) */;
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
