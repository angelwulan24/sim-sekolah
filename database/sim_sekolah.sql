-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2022 at 08:59 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sim_sekolah`
--

-- --------------------------------------------------------

--
-- Table structure for table `catering`
--

CREATE TABLE `catering` (
  `id` int(11) NOT NULL,
  `id_siswa` smallint(6) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `waktu` date NOT NULL,
  `time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `catering`
--

INSERT INTO `catering` (`id`, `id_siswa`, `nominal`, `tanggal`, `waktu`, `time`) VALUES
(1, 3, '15000', '2019-12-18 10:11:30', '2019-11-08', '2019-11-17'),
(2, 2, '15000', '2019-11-16 20:48:15', '2019-11-09', '2019-11-17'),
(3, 2, '15000', '2019-11-16 20:48:15', '2019-11-11', '2019-11-17'),
(4, 9, '15000', '2019-12-04 07:55:13', '2019-12-04', '2019-12-04'),
(5, 9, '15000', '2019-12-04 07:55:13', '2019-12-05', '2019-12-04'),
(6, 9, '0', '2019-12-18 10:40:36', '2019-12-06', '2019-12-04'),
(7, 9, '15000', '2019-12-04 07:55:13', '2019-12-07', '2019-12-04'),
(9, 9, '15000', '2019-12-04 08:04:39', '2019-12-27', '2019-12-04'),
(10, 9, '15000', '2019-12-04 08:04:39', '2019-12-28', '2019-12-04'),
(11, 9, '15000', '2019-12-04 08:04:39', '2019-12-30', '2019-12-04'),
(12, 8, '15000', '2019-12-04 08:07:48', '2019-12-12', '2019-12-04'),
(13, 8, '15000', '2019-12-04 08:07:48', '2019-12-13', '2019-12-04'),
(14, 8, '15000', '2019-12-04 08:07:48', '2019-12-14', '2019-12-04'),
(15, 8, '15000', '2019-12-04 08:08:51', '2019-12-26', '2019-12-04'),
(16, 8, '15000', '2019-12-04 08:08:51', '2019-12-30', '2019-12-04'),
(17, 8, '0', '2019-12-18 10:25:52', '2019-12-31', '2019-12-04'),
(18, 8, '15000', '2019-12-18 09:15:55', '2019-12-19', '2019-12-18'),
(19, 8, '15000', '2019-12-18 09:15:55', '2019-12-23', '2019-12-18'),
(20, 8, '15000', '2019-12-18 09:15:55', '2019-12-24', '2019-12-18'),
(21, 8, '15000', '2019-12-18 09:15:55', '2019-12-25', '2019-12-18'),
(22, 8, '15000', '2019-12-18 09:15:55', '2020-01-01', '2019-12-18'),
(23, 8, '15000', '2019-12-18 09:15:55', '2020-01-02', '2019-12-18'),
(24, 8, '15000', '2019-12-18 09:15:55', '2020-01-06', '2019-12-18'),
(25, 8, '15000', '2019-12-18 09:15:55', '2020-01-07', '2019-12-18'),
(26, 8, '15000', '2019-12-18 09:15:55', '2020-01-08', '2019-12-18'),
(27, 8, '15000', '2019-12-18 09:15:55', '2020-01-09', '2019-12-18');

-- --------------------------------------------------------

--
-- Table structure for table `gaji`
--

CREATE TABLE `gaji` (
  `id` int(11) NOT NULL,
  `id_guru` tinyint(4) NOT NULL,
  `periode` varchar(20) NOT NULL,
  `jam` varchar(4) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `time` date NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gaji`
--

INSERT INTO `gaji` (`id`, `id_guru`, `periode`, `jam`, `nominal`, `time`, `tanggal`) VALUES
(2, 3, 'Februari-2020', '16', '40000', '2020-02-25', '2020-02-25 05:16:50');

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sex` enum('Pria','Wanita') NOT NULL,
  `nip` varchar(15) NOT NULL,
  `bidang` varchar(40) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `status` enum('Berhenti','Cuti','Aktif') NOT NULL,
  `number` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `name`, `sex`, `nip`, `bidang`, `alamat`, `status`, `number`) VALUES
(3, 'Ahmad Soleh', 'Pria', '00000001', 'FIQIH', 'Jl. Kenangan', 'Aktif', '081234567890'),
(4, 'Sri Wulandari', 'Wanita', '00000002', 'Bahasa Indonesia', 'Jl. Perjuangan', 'Aktif', '0878889990009'),
(5, 'Zahra Amelia', 'Wanita', '00000003', 'Biologi', 'Jl. Mataaram', 'Aktif', '080909090909');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(15) NOT NULL,
  `wali` varchar(50) NOT NULL,
  `keterangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama`, `wali`, `keterangan`) VALUES
(5, 'X IPA', 'Ahmad Soleh', ''),
(6, 'X IPS', 'Sri Wulandari', '');

-- --------------------------------------------------------

--
-- Table structure for table `lainnya`
--

CREATE TABLE `lainnya` (
  `id` int(11) NOT NULL,
  `sekarang` varchar(15) NOT NULL,
  `time` date NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lainnya`
--

INSERT INTO `lainnya` (`id`, `sekarang`, `time`, `keterangan`, `nominal`, `tanggal`) VALUES
(6, '200225', '2020-02-25', 'Saldo Awal Sekolah', '25000000', '2020-02-25 05:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `saldo_awal` varchar(15) NOT NULL DEFAULT '0',
  `kas_masuk` varchar(15) DEFAULT '0',
  `kas_keluar` varchar(15) NOT NULL DEFAULT '0',
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `saldo_awal`, `kas_masuk`, `kas_keluar`, `tanggal`) VALUES
(13, '0', '25000000', '0', '2020-02-25'),
(14, '25000000', '400000', '0', '2022-11-06');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `tipe` enum('KM','KK') NOT NULL,
  `kode` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `nama`, `nominal`, `tipe`, `kode`) VALUES
(1, 'Uang SPP', '70000', 'KM', 'KM-0001'),
(2, 'Uang Ujian', '50000', 'KM', 'KM-0002'),
(3, 'Uang Snack', '5000', 'KM', 'KM-0003'),
(4, 'Uang Catering', '15000', 'KM', 'KM-0004'),
(5, 'Uang Pendaftaran', '200000', 'KM', 'KM-0005'),
(6, 'Pembayaran Gaji', '40000', 'KK', 'KK-0001');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id` int(11) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `time` date NOT NULL,
  `siswa` varchar(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id`, `nominal`, `time`, `siswa`, `tanggal`) VALUES
(3, '200000', '2022-11-06', 'Ahmad Safawi', '2022-11-05 19:51:09'),
(4, '200000', '2022-11-06', 'Ali Makruf', '2022-11-05 19:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id` int(11) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `sekarang` varchar(10) NOT NULL,
  `time` date NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `nis` varchar(15) NOT NULL,
  `sex` enum('Pria','Wanita') NOT NULL,
  `status` enum('Berhenti','Cuti','Aktif') NOT NULL,
  `wali` varchar(50) NOT NULL,
  `tempat` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `kelas` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `name`, `nis`, `sex`, `status`, `wali`, `tempat`, `tanggal`, `alamat`, `kelas`) VALUES
(10, 'Suci Permata Sari', '123313134223', 'Wanita', 'Aktif', 'Musa Harun', 'Kampar', '2020-02-17', 'Jl. Simpang Lima', 6),
(11, 'Ahmad Dhairobbi', '1231314331', 'Pria', 'Aktif', 'Darmijan', 'Batu Guntung', '2002-07-25', 'Jln Kebakyoran Baru', 5),
(12, 'Ahmad Safawi', '1333', 'Pria', 'Aktif', 'Bahar', 'Rambah Samo', '2000-06-13', 'Jl. Simpang Mangga', 5),
(13, 'Ali Makruf', '010000', 'Pria', 'Aktif', 'Hamdan', 'Pati', '2000-07-13', 'Jl. Bandeng', 5),
(14, 'Firda', '01005', 'Wanita', 'Aktif', 'Huda', 'Rembang', '2003-07-16', 'Jl. Kaliori', 6),
(15, 'Yuliana', '01008', 'Wanita', 'Aktif', 'Solikin', 'Pati', '2003-05-13', 'Jl. Pemuda', 5);

-- --------------------------------------------------------

--
-- Table structure for table `snack`
--

CREATE TABLE `snack` (
  `id` int(11) NOT NULL,
  `id_siswa` smallint(6) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `time` date NOT NULL,
  `waktu` date NOT NULL,
  `nominal` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `snack`
--

INSERT INTO `snack` (`id`, `id_siswa`, `tanggal`, `time`, `waktu`, `nominal`) VALUES
(15, 8, '2019-11-17 07:08:52', '2019-11-17', '2019-11-18', '5000'),
(16, 8, '2019-11-17 07:08:52', '2019-11-17', '2019-11-19', '5000'),
(17, 8, '2019-11-17 07:08:52', '2019-11-17', '2019-11-20', '5000'),
(18, 8, '2019-12-18 11:57:24', '2019-11-17', '2019-11-21', '0'),
(19, 8, '2019-11-17 07:08:52', '2019-11-17', '2019-11-22', '5000'),
(20, 8, '2019-11-17 07:08:52', '2019-11-17', '2019-11-23', '5000'),
(21, 8, '2019-12-18 11:01:43', '2019-11-17', '2019-11-25', '0'),
(22, 10, '2020-02-25 05:11:36', '2020-02-25', '2020-02-12', '5000'),
(23, 10, '2020-02-25 05:11:36', '2020-02-25', '2020-02-13', '5000');

-- --------------------------------------------------------

--
-- Table structure for table `spp`
--

CREATE TABLE `spp` (
  `id` int(11) NOT NULL,
  `id_siswa` smallint(6) NOT NULL,
  `time` date NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `nominal` varchar(12) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `spp`
--

INSERT INTO `spp` (`id`, `id_siswa`, `time`, `bulan`, `nominal`, `tanggal`) VALUES
(3, 9, '2019-11-17', 'Januari-2019', '70000', '2019-11-17 07:20:18'),
(4, 10, '2020-02-25', 'Januari-2020', '70000', '2020-02-25 05:09:48');

-- --------------------------------------------------------

--
-- Table structure for table `tanggal`
--

CREATE TABLE `tanggal` (
  `id` int(11) NOT NULL,
  `tgl` date NOT NULL,
  `Keterangan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tanggal`
--

INSERT INTO `tanggal` (`id`, `tgl`, `Keterangan`) VALUES
(5, '2022-11-10', 'Hari Pahlawan'),
(7, '2023-02-18', 'Isra\' dan Mi\'raj Nabi Muhammad'),
(8, '2023-03-22', 'Hari Nyepi');

-- --------------------------------------------------------

--
-- Table structure for table `temp`
--

CREATE TABLE `temp` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nis` varchar(15) NOT NULL,
  `tempat` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `wali` varchar(100) NOT NULL,
  `sex` enum('Pria','Wanita') NOT NULL,
  `status` enum('Non-Aktif','Aktif') NOT NULL,
  `kelas` tinyint(4) NOT NULL,
  `bayar` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `temp`
--

INSERT INTO `temp` (`id`, `name`, `nis`, `tempat`, `tanggal`, `alamat`, `wali`, `sex`, `status`, `kelas`, `bayar`) VALUES
(5, 'Soleh', '01001', 'Jepara', '2003-07-24', 'Jl. Mawar', 'Solikin', 'Pria', 'Non-Aktif', 0, 0),
(6, 'Yusuf', '01002', 'Pati', '2003-08-29', 'Jl. Bandeng', 'Bambang', 'Pria', 'Non-Aktif', 0, 0),
(7, 'Malika', '01003', 'Pati', '2003-12-24', 'Jl. Kenagan', 'Ahmad', 'Pria', 'Non-Aktif', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ujian`
--

CREATE TABLE `ujian` (
  `id` int(11) NOT NULL,
  `id_siswa` smallint(6) NOT NULL,
  `nominal` varchar(15) NOT NULL,
  `periode` varchar(20) NOT NULL,
  `time` date NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` int(11) NOT NULL,
  `active` enum('1','0') NOT NULL,
  `gambar` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `role`, `active`, `gambar`) VALUES
(1, 'madrasah@gmail.com', '$2y$10$dFdQaba34BplJRnmCv54/uhoFLU0wlXCY4lRG/EG9FpX9fN1kzjq.', 'Administrator', 1, '1', 'ikhlas.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `catering`
--
ALTER TABLE `catering`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gaji`
--
ALTER TABLE `gaji`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lainnya`
--
ALTER TABLE `lainnya`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `snack`
--
ALTER TABLE `snack`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spp`
--
ALTER TABLE `spp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tanggal`
--
ALTER TABLE `tanggal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp`
--
ALTER TABLE `temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ujian`
--
ALTER TABLE `ujian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catering`
--
ALTER TABLE `catering`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `gaji`
--
ALTER TABLE `gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lainnya`
--
ALTER TABLE `lainnya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `snack`
--
ALTER TABLE `snack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `spp`
--
ALTER TABLE `spp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tanggal`
--
ALTER TABLE `tanggal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `temp`
--
ALTER TABLE `temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ujian`
--
ALTER TABLE `ujian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
