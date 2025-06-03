-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 02:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_uniform`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_stock` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_produk`, `id_stock`, `jumlah`, `subtotal`) VALUES
(16, 24, 15, 115, 10, 678900.00),
(17, 24, 15, 116, 20, 1357800.00),
(18, 24, 15, 117, 20, 1357800.00),
(19, 24, 15, 117, 10, 678900.00),
(20, 24, 15, 118, 20, 1357800.00),
(21, 24, 15, 119, 10, 678900.00),
(22, 24, 18, 130, 10, 652500.00),
(23, 24, 18, 131, 20, 1305000.00),
(24, 24, 18, 132, 29, 1892250.00),
(25, 24, 18, 133, 20, 1305000.00),
(26, 24, 18, 134, 10, 652500.00),
(29, 26, 19, 135, 30, 1937400.00),
(30, 26, 16, 120, 30, 2357070.00),
(31, 26, 22, 150, 30, 1703370.00),
(32, 26, 28, 243, 30, 410070.00),
(33, 26, 31, 246, 30, 440070.00),
(34, 27, 15, 117, 100, 6789000.00),
(35, 27, 18, 132, 100, 6525000.00),
(36, 27, 30, 245, 100, 1297800.00),
(37, 27, 33, 248, 98, 1261162.00);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(255) NOT NULL,
  `no_telepon` varchar(30) NOT NULL,
  `sekolah` varchar(30) NOT NULL,
  `alamat_sekolah` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_telepon`, `sekolah`, `alamat_sekolah`) VALUES
(1, 'Rosita', '0832468238', 'SMA Cahaya Intan', 'dbntymn'),
(2, 'Rosita', '0832468238', 'SMA Cahaya Intan', '-'),
(3, 'Puteri', '0832468238', 'SMA Cahaya Intan', '-'),
(4, 'Samantha', '0832468238', 'SMA Cahaya Intan', '-'),
(5, 'Rosita Kurningsih', '0832468238', 'SMA Cahaya Intan', 'Jl. Kebangsaan Raya No. 106-10'),
(6, 'Samantha Gading', '082354679', 'SMA Pelita Harapan', 'Jl. Agung Raya No. 89-90'),
(7, 'Bagas Cahyo', '08327438456', 'SDN Bogor I', 'Jl. Cibodas Raya No. 76-80');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `metode_pembayaran` enum('Tunai','Transfer','QRIS') NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `tanggal_bayar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `metode_pembayaran`, `jumlah_bayar`, `tanggal_bayar`) VALUES
(16, 24, 'Transfer', 6473925.00, '2025-06-02 22:21:42'),
(18, 26, 'QRIS', 3423990.00, '2025-06-03 13:47:41'),
(19, 27, 'Tunai', 15872962.00, '2025-06-03 13:50:09');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `tanggal_pesanan` datetime DEFAULT NULL,
  `total_harga` decimal(10,2) DEFAULT NULL,
  `status` enum('Dicicil','Sudah Lunas') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_pelanggan`, `tanggal_pesanan`, `total_harga`, `status`) VALUES
(24, 5, '2025-06-02 22:21:42', 12947850.00, 'Sudah Lunas'),
(26, 6, '2025-06-03 13:47:41', 6847980.00, 'Sudah Lunas'),
(27, 7, '2025-06-03 13:50:09', 15872962.00, 'Sudah Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `warna` varchar(50) DEFAULT NULL,
  `jenis_kelamin` enum('Pria','Wanita','Unisex') DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `gambar_produk` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `kategori`, `warna`, `jenis_kelamin`, `harga`, `gambar_produk`) VALUES
(15, 'Kemeja Putih', 'SD', NULL, 'Unisex', 67890.00, 'produk_683e043bac7c8.png'),
(16, 'Kemeja Putih', 'SMP', NULL, 'Unisex', 78569.00, 'produk_683e04694dac2.png'),
(17, 'Kemeja Putih', 'SMA', NULL, 'Unisex', 84359.00, 'produk_683e049f79944.png'),
(18, 'Rok Merah', 'SD', NULL, 'Wanita', 65250.00, 'produk_683e04cb4feb4.png'),
(19, 'Rok Biru Tua', 'SMP', NULL, 'Wanita', 64580.00, 'produk_683e04fedcaf0.png'),
(20, 'Rok Abu-abu', 'SMA', NULL, 'Wanita', 74129.00, 'produk_683e052972cfa.png'),
(21, 'Celana Merah', 'SD', NULL, 'Pria', 45890.00, 'produk_683e05609aaff.png'),
(22, 'Celana Biru Tua', 'SMP', NULL, 'Pria', 56779.00, 'produk_683e0594ca9cc.png'),
(23, 'Sabuk Hitam', 'SD', NULL, 'Unisex', 12339.00, 'produk_683e05c8da8fc.png'),
(28, 'Sabuk Hitam', 'SMP', NULL, 'Unisex', 13669.00, 'produk_683ede2ca4e40.png'),
(29, 'Sabuk Hitam', 'SMA', NULL, 'Unisex', 14560.00, 'produk_683ede49a5ffd.png'),
(30, 'Topi Merah', 'SD', NULL, 'Unisex', 12978.00, 'produk_683edef4636b8.png'),
(31, 'Topi Biru Tua', 'SMP', NULL, 'Unisex', 14669.00, 'produk_683edf0f89516.png'),
(32, 'Topi Abu-abu', 'SMA', NULL, 'Unisex', 15729.00, 'produk_683edf31acc0f.png'),
(33, 'Dasi Merah', 'SD', NULL, 'Unisex', 12869.00, 'produk_683edf55598f5.png'),
(34, 'Dasi Biru Tua', 'SMP', NULL, 'Unisex', 14579.00, 'produk_683edf769dd95.png'),
(35, 'Dasi Abu-abu', 'SMA', NULL, 'Unisex', 15760.00, 'produk_683edf9bbfc29.png');

-- --------------------------------------------------------

--
-- Table structure for table `produk_stock`
--

CREATE TABLE `produk_stock` (
  `id` int(11) NOT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk_stock`
--

INSERT INTO `produk_stock` (`id`, `id_produk`, `size`, `stok`) VALUES
(115, 15, 'XS', 990),
(116, 15, 'S', 980),
(117, 15, 'M', 870),
(118, 15, 'L', 980),
(119, 15, 'XL', 990),
(120, 16, 'XS', 970),
(121, 16, 'S', 1000),
(122, 16, 'M', 1000),
(123, 16, 'L', 1000),
(124, 16, 'XL', 1000),
(125, 17, 'XS', 1000),
(126, 17, 'S', 1000),
(127, 17, 'M', 1000),
(128, 17, 'L', 1000),
(129, 17, 'XL', 1000),
(130, 18, 'XS', 990),
(131, 18, 'S', 980),
(132, 18, 'M', 871),
(133, 18, 'L', 980),
(134, 18, 'XL', 990),
(135, 19, 'XS', 970),
(136, 19, 'S', 1000),
(137, 19, 'M', 1000),
(138, 19, 'L', 1000),
(139, 19, 'XL', 1000),
(140, 20, 'XS', 1000),
(141, 20, 'S', 1000),
(142, 20, 'M', 1000),
(143, 20, 'L', 1000),
(144, 20, 'XL', 1000),
(145, 21, 'XS', 1000),
(146, 21, 'S', 1000),
(147, 21, 'M', 1000),
(148, 21, 'L', 1000),
(149, 21, 'XL', 1000),
(150, 22, 'XS', 970),
(151, 22, 'S', 1000),
(152, 22, 'M', 1000),
(153, 22, 'L', 1000),
(154, 22, 'XL', 1000),
(238, 23, NULL, 80),
(243, 28, 'NO_SIZE', 970),
(244, 29, 'NO_SIZE', 1000),
(245, 30, 'NO_SIZE', 900),
(246, 31, 'NO_SIZE', 970),
(247, 32, 'NO_SIZE', 1000),
(248, 33, 'NO_SIZE', 902),
(249, 34, 'NO_SIZE', 1000),
(250, 35, 'NO_SIZE', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'user_1', 'user1@gmail.com', '$2y$10$zHtY.l0MG66JzKQJHHx.Gu5ffl1J2XtRbdxQvbHDrRrFXiU8vGXTy', '2025-05-26 03:44:36'),
(3, 'ameliarosa', 'ameliarosa@gmail.com', '$2y$10$3RMOnf143jUi5.S7qPr0bujOFaH2sqFvsXEHBefqelA/gDTX/9vGK', '2025-06-03 02:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id_profile` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `gender` enum('Laki-laki','Perempuan') NOT NULL,
  `birth_date` date NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id_profile`, `user_id`, `full_name`, `phone_number`, `address`, `gender`, `birth_date`, `updated_at`) VALUES
(2, 1, 'Amelia', '0812437348', 'Jl. Mawar No. 28', 'Perempuan', '2001-01-01', '2025-06-03 09:22:55'),
(3, 3, 'Amelia Rosa', '0812437348', 'Jl. Mawar No. 28', 'Perempuan', '2002-02-02', '2025-06-03 09:25:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_stock` (`id_stock`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `produk_stock`
--
ALTER TABLE `produk_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id_profile`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `produk_stock`
--
ALTER TABLE `produk_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id_profile` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_3` FOREIGN KEY (`id_stock`) REFERENCES `produk_stock` (`id`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `produk_stock`
--
ALTER TABLE `produk_stock`
  ADD CONSTRAINT `produk_stock_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
