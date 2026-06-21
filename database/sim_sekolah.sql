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
-- Dumping data for table `gaji`
--

LOCK TABLES `gaji` WRITE;
/*!40000 ALTER TABLE `gaji` DISABLE KEYS */;
INSERT INTO `gaji` VALUES (4,6,'April-2026','20','40000','2026-04-10','2026-04-10 14:37:44'),(5,4,'April-2026','1','200000','2026-04-10','2026-04-10 15:17:34'),(6,7,'April-2026','1','300000','2026-04-10','2026-04-10 15:17:34'),(7,8,'April-2026','1','150000','2026-04-10','2026-04-10 15:17:34'),(8,4,'Maret-2026','5','40000','2026-04-10','2026-04-10 15:22:10'),(9,6,'Maret-2026','10','40000','2026-04-10','2026-04-10 15:22:10'),(10,7,'Maret-2026','20','40000','2026-04-10','2026-04-10 15:22:10'),(11,8,'Maret-2026','15','40000','2026-04-10','2026-04-10 15:22:10'),(12,4,'Februari-2026','20','40000','2026-04-10','2026-04-10 16:19:16'),(13,6,'Februari-2026','11','40000','2026-04-10','2026-04-10 16:19:16'),(14,7,'Februari-2026','14','40000','2026-04-10','2026-04-10 16:19:16'),(15,8,'Februari-2026','17','40000','2026-04-10','2026-04-10 16:19:16'),(16,6,'Januari-2026','2','40000','2026-04-25','2026-04-25 11:07:15'),(17,8,'Januari-2026','3','40000','2026-04-25','2026-04-25 11:07:15'),(18,4,'Januari-2026','4','40000','2026-04-25','2026-04-25 11:07:15'),(19,7,'Januari-2026','5','40000','2026-04-25','2026-04-25 11:07:15'),(20,6,'Mei-2026','4','20000','2026-04-27','2026-04-27 14:21:11'),(21,8,'Mei-2026','4','20000','2026-04-27','2026-04-27 14:21:11'),(22,7,'Mei-2026','4','20000','2026-04-27','2026-04-27 14:21:11'),(23,6,'Juni-2026','2','25000','2026-04-27','2026-04-27 14:21:55');
/*!40000 ALTER TABLE `gaji` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `guru`
--

LOCK TABLES `guru` WRITE;
/*!40000 ALTER TABLE `guru` DISABLE KEYS */;
INSERT INTO `guru` VALUES (4,'Sri Wulandari','Perempuan','00000002','Bahasa Indonesia','Jl. Perjuangan','Berhenti','0878889990009','1773384656_guru.jpg'),(6,'Nurul Nuraeni','Perempuan','008923567','Pendidikan Agama','Jl. Jalan Ke Pasar','Aktif','08123458899','nurul_nuraeni_1777135426.jpg'),(7,'Suparjo','Laki-Laki','0087489272','Bahasa Inggris','Jl. Kenangan 1','Aktif','08984849909','suparjo_1777135472.jpeg'),(8,'Saripudin','Laki-Laki','00937784738','Sejarah','Jl. Kesiangan','Aktif','098738489387','saripudin_1777135459.jpg');
/*!40000 ALTER TABLE `guru` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES (5,'Kelas 1','Sri Wulandari',''),(6,'Kelas 2','Sri Wulandari',''),(7,'Kelas 3','Zahra Amelia',''),(8,'Kelas 4','Suparjo',''),(9,'Kelas 5','Nurul Nuraeni',''),(10,'Kelas 6','Saripudin','');
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `lainnya`
--

LOCK TABLES `lainnya` WRITE;
/*!40000 ALTER TABLE `lainnya` DISABLE KEYS */;
INSERT INTO `lainnya` VALUES (6,'200225','2020-02-25','Saldo Awal Sekolah','25000000','2020-02-25 05:21:15');
/*!40000 ALTER TABLE `lainnya` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `laporan`
--

LOCK TABLES `laporan` WRITE;
/*!40000 ALTER TABLE `laporan` DISABLE KEYS */;
INSERT INTO `laporan` VALUES (13,'0','25000000','0','2020-02-25'),(14,'25000000','400000','0','2022-11-06'),(15,'25400000','0','0','2026-01-15'),(16,'25400000','0','0','2026-01-20'),(17,'25400000','0','0','2026-01-21'),(18,'25400000','120000','0','2026-01-29'),(19,'25520000','270000','0','2026-02-02'),(20,'25790000','670000','0','2026-02-05'),(21,'26460000','400000','270000','2026-02-06'),(22,'26590000','880000','0','2026-02-12'),(23,'27470000','0','0','2026-03-04'),(24,'27470000','15000','0','2026-03-05'),(25,'27485000','0','20000','2026-03-10'),(26,'27465000','0','0','2026-03-13'),(27,'27465000','0','0','2026-04-09'),(28,'27465000','0','6000000','2026-04-10'),(29,'21465000','0','0','2026-04-16'),(30,'21465000','0','0','2026-04-17'),(31,'21465000','50000','0','2026-04-20'),(32,'21515000','0','0','2026-04-21'),(33,'21515000','0','0','2026-04-23'),(34,'21515000','60000','560000','2026-04-25'),(35,'21015000','0','0','2026-04-26'),(36,'21015000','145000','390000','2026-04-27'),(37,'20770000','0','0','2026-04-28'),(38,'20770000','0','0','2026-05-03'),(39,'20770000','0','0','2026-05-18');
/*!40000 ALTER TABLE `laporan` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
INSERT INTO `pembayaran` VALUES (20,'spp','15000','Setiap Bulan','KM','KM-0001','2026/2027','Kelas 1'),(21,'praktek','10000','2026-04-26','KM','KM-0002','2026/2027','Kelas 1'),(22,'Biaya Penndaftaran','100000','2026-04-28','KM','KM-0003','2026/2027','Kelas 1'),(23,'Uang Baju Seragam','150000','2026-04-29','KM','KM-0004','2026/20027','Kelas 1');
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `pengeluaran`
--

LOCK TABLES `pengeluaran` WRITE;
/*!40000 ALTER TABLE `pengeluaran` DISABLE KEYS */;
INSERT INTO `pengeluaran` VALUES (5,'20000','260206','2026-02-06','beli lampu','2026-02-06 09:58:42',NULL),(6,'50000','260206','2026-02-06','print dokumen','2026-02-06 09:59:25',NULL),(7,'20000','260310','2026-03-10','pembelian sabun wc','2026-03-09 19:16:50',NULL),(8,'50000','260410','2026-04-10','beli hiasan kelas','2026-04-10 14:36:02',NULL),(9,'20000','260410','2026-04-10','beli kertas','2026-04-10 16:31:04','88828feb3f328b7e5c949252cd11e8eb.png'),(10,'100000','260427','2026-04-27','jenguk siswa','2026-04-27 14:33:26','e21ce9fcfcfba40199c50a449a03997a.png');
/*!40000 ALTER TABLE `pengeluaran` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
INSERT INTO `siswa` VALUES (25,'Angelina Wulandari','11111111','Perempuan','Islam','Aktif','Kastini','Serang','2019-11-24','Cikande Permai Blok J1 No 27','085216165214',5,'Loket','angelina_wulandari_1776678253.jpg','2026-04-20','2026/2027'),(26,'Febriana Valentine','222222','Perempuan','Islam','Aktif','Agus Mintoro','Serang','2018-02-14','Cikande Permai Blok J1 No 28','085216165214',6,'Loket','febriana_valentina_1776678431.jpg','2025-05-10','2025/2026'),(27,'Yasmine Yusriyyah Wibowo','3333333','Perempuan','Islam','Aktif','Supri','Jogja','2017-12-23','Cikande Permai Blok A1 No 15','085713071649',7,'Loket','yasmine_yusriyyah_wibowo_1776678751.jpg','2024-05-16','2024/2025'),(28,'Popo Kosasih','44444444','Laki-Laki','Islam','Aktif','Purwanto','Serang','2016-12-23','Kampung Kedung Sampan','081247995437',8,'Loket','popo_kosasih_1776678891.jpg','2023-06-01','2023/2024'),(29,'Angkasa','5555555','Laki-Laki','Islam','Aktif','Barni','Pati','2015-09-11','Cikande Asem Blok J2 No 5','081296341007',9,'Loket','angkasa_1776679473.jpg','2022-05-18','2022/2023'),(31,'Siska Hardianti','77777777','Perempuan','Islam','Aktif','Sutijah','Baluk','2018-08-22','Cikande Baluk Blok H1 No 6','085210074601',9,'Loket','siska_hardianti_1777280795.jpg','2021-04-27','2021/2022');
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--

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
-- Dumping data for table `tagihan`
--

LOCK TABLES `tagihan` WRITE;
/*!40000 ALTER TABLE `tagihan` DISABLE KEYS */;
INSERT INTO `tagihan` VALUES (431,NULL,25,'SPP - Mei','15000','2026/2027','2026-04-10','Lunas','2026-04-24 17:00:00'),(432,NULL,25,'SPP - Juni','15000','2026/2027','2026-04-10','Lunas','2026-04-24 17:00:00'),(433,NULL,25,'SPP - Juli','15000','2026/2027','2026-04-10','Lunas','2026-04-27 06:03:36'),(434,NULL,25,'SPP - Agustus','15000','2026/2027','2026-04-10','Lunas','2026-04-27 06:02:16'),(435,NULL,25,'SPP - September','15000','2026/2027','2026-04-10','Lunas','2026-04-27 06:03:36'),(436,NULL,25,'SPP - Oktober','15000','2026/2027','2026-04-10','Lunas','2026-04-26 17:00:00'),(437,NULL,25,'SPP - November','15000','2026/2027','2026-04-10','Lunas','2026-04-26 17:00:00'),(438,NULL,25,'SPP - Desember','15000','2026/2027','2026-04-10','Lunas','2026-04-26 17:00:00'),(439,NULL,25,'SPP - Januari','15000','2026/2027','2026-04-10','Lunas','2026-04-26 17:00:00'),(440,NULL,25,'SPP - Februari','15000','2026/2027','2026-04-10','Lunas','2026-04-26 17:00:00'),(441,NULL,25,'SPP - Maret','15000','2026/2027','2026-04-10','Lunas','2026-04-26 17:00:00'),(442,NULL,25,'SPP - April','15000','2026/2027','2026-04-10','Belum Lunas',NULL),(443,NULL,25,'Praktek','10000','2026/2027','2026-04-26','Lunas','2026-04-27 06:07:47'),(444,NULL,25,'Biaya Penndaftaran','100000','2026/2027','2026-04-28','Belum Lunas',NULL),(445,NULL,25,'Uang Baju Seragam','150000','2026/20027','2026-04-29','Belum Lunas',NULL);
/*!40000 ALTER TABLE `tagihan` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'madrasah@gmail.com','','$2y$10$dFdQaba34BplJRnmCv54/uhoFLU0wlXCY4lRG/EG9FpX9fN1kzjq.','Administrator',1,'1','MI.png'),(2,'admin@gmail.com','','$2a$12$6paoqbhYso.y11XF0NbxcuJJBP2LUKUTZNXF6bqZizXjaFJ8ztmRi','Administrator',1,'1','MI.png'),(3,'mahasiswa@gmail.com','','$2a$12$oRAYkMeE4ughDbcMHDdl7O3izaRYm2he39upfSgkgBSF3OM67gMV2','Mahasiswa',3,'1',''),(11,'angelina@gmail.com','','$2a$10$JYXHntsdWHZtoRQki20jo.JtSopMysqbLHYCSNSnQvZHRoKn5stFi','Angelina Wulandari',3,'1',''),(12,'kepsek@gmail.com','','$2y$10$AG7ASunz1IiHzTdzmm5O0u307E3neWUzB.Mjy1VDlbY7eYoFz4BNW','Kepala Yayasan',2,'1','kepsek.jpg'),(14,'11111111','','$2y$10$luUanoTPQBAob.jXoQnCtusleMVhp36XwDOaUMkQHDSrwyfng/.Vy','Angelina Wulandari',3,'1','user.png'),(15,'222222','','$2y$10$AedqDQdkO5waAQ/F4SmktuY//hYDmNSGZiSsVFon.GReetT59u9fS','Febriana Valentine',3,'1','user.png'),(16,'3333333','','$2y$10$MZv4TfQXHhXW94Tsrf1QTeZf8b4iuuaUJg1GTNDPyNMOoKCHbLSeG','Yasmine Yusriyyah Wibowo',3,'1','user.png'),(17,'44444444','','$2y$10$Zh7omsJBY9EukCfJnqIaz.ZQmRj0phP.9tI84PMBCABQMaKQu6C3e','Popo Kosasih',3,'1','user.png'),(18,'5555555','','$2y$10$LZ/2M2znNjUBoWAV3o9QquFKldHY4NQTWY1bG4fIpc0XtkyYAjNLu','Angkasa',3,'1','user.png'),(19,'66666666','','$2y$10$LYSddZit7L5JSC1qNJmbf.KYSIcUz8HRtAFJAGakp4IRVeAJA.Pi.','Anggara Budi',3,'1','user.png'),(20,'77777777','','$2y$10$0Iq7xLgXhsFjOhwnx4udL.NZqcdpv1n3WgsToj3dUc8d2CXbYyeTi','Siska Hardianti',3,'1','user.png');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

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

-- Dump completed on 2026-05-19  0:33:20
