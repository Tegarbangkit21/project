-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 10:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chibor_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$6jbXPJd2zELUEOLNWxDvieJFfQ5.GRTZZZS9M4DrcYdWWcAbH4xYi', '2025-08-07 22:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban` text NOT NULL,
  `kategori_faq` varchar(50) NOT NULL DEFAULT 'produk',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `pertanyaan`, `jawaban`, `kategori_faq`, `created_at`) VALUES
(1, 'Bagaimana cara memesan produk CHIBOR?', 'Anda dapat memesan melalui WhatsApp di nomor 0823-2020-8899 atau melalui website ini dengan klik tombol \"Pesan Sekarang\".', 'produk', '2025-08-07 22:46:31'),
(2, 'Apakah ada minimum order?', 'Untuk pembelian retail tidak ada minimum order. Untuk pembelian dalam jumlah besar, silakan hubungi kami untuk mendapatkan harga khusus.', 'produk', '2025-08-07 22:46:31'),
(3, 'Berapa lama masa expired produk CHIBOR?', 'Produk CHIBOR memiliki masa simpan 6 bulan dari tanggal produksi dalam kemasan yang belum dibuka.', 'produk', '2025-08-07 22:46:31'),
(4, 'Apakah CHIBOR halal?', 'Ya, semua produk CHIBOR telah bersertifikat halal dan diproduksi dengan standar keamanan pangan yang tinggi.', 'produk', '2025-08-07 22:46:31'),
(5, 'Apakah bisa custom rasa atau packaging?', 'Tentu! Kami melayani custom order untuk rasa maupun packaging. Silakan hubungi kami untuk diskusi lebih lanjut.', 'produk', '2025-08-07 22:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `created_at`) VALUES
(1, 'Retail', '2025-08-07 22:46:31'),
(2, 'Specialties', '2025-08-07 22:46:31'),
(3, 'Custom', '2025-08-07 22:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `id_subkategori` int(11) DEFAULT NULL,
  `link_wa` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `deskripsi`, `gambar`, `id_kategori`, `id_subkategori`, `link_wa`, `created_at`, `updated_at`) VALUES
(1, 'CHEESE', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754708262_1716.jpg', 1, NULL, 'https://wa.me/6282320208899?text=Halo, saya ingin memesan CHIBOR Original', '2025-08-07 22:46:31', '2025-08-10 02:59:24'),
(3, 'Garuda Wisnu Kencana', 'Edisi khusus dengan citarasa khas Bali', 'product_1754799104_7304.jpg', 2, 1, 'https://wa.me/6282320208899?text=Halo, saya ingin memesan CHIBOR Bali Special', '2025-08-07 22:46:31', '2025-08-10 04:11:44'),
(4, 'Legong Dance', 'Produk eksklusif untuk Jakarta', 'product_1754799132_7349.jpg', 2, 1, 'https://wa.me/6282320208899?text=Halo, saya ingin memesan CHIBOR Jakarta Exclusive', '2025-08-07 22:46:31', '2025-08-10 04:12:12'),
(5, 'ORIGINAL SALT', 'a', 'product_1754791947_3565.jpg', 1, NULL, NULL, '2025-08-10 02:12:27', NULL),
(6, 'CHICKEN ROSEMARY', 'a', 'product_1754791983_7218.jpg', 1, NULL, NULL, '2025-08-10 02:13:03', NULL),
(7, 'TRUFFLE', 'a', 'product_1754792003_5110.jpg', 1, NULL, NULL, '2025-08-10 02:13:23', NULL),
(8, 'SOUR CREAM & ONION', 'a', 'product_1754792035_2710.jpg', 1, NULL, NULL, '2025-08-10 02:13:55', NULL),
(9, 'Jalak Bali', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754799155_9619.jpg', 2, 1, NULL, '2025-08-10 04:12:35', '2025-08-10 06:11:17'),
(10, 'Batong', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754799180_9106.jpg', 2, 1, NULL, '2025-08-10 04:13:00', '2025-08-10 06:11:25'),
(11, 'Pusa Ulung Danu', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754799203_8786.jpg', 2, 1, NULL, '2025-08-10 04:13:23', '2025-08-10 06:11:07'),
(12, 'Bali lot', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754799223_6928.jpg', 2, 1, NULL, '2025-08-10 04:13:43', '2025-08-10 06:10:58'),
(13, 'Ondel Ondel', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754806486_6600.jpg', 2, 2, NULL, '2025-08-10 06:14:46', NULL),
(14, 'Bajay Jakarta', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754806510_2256.jpg', 2, 2, NULL, '2025-08-10 06:15:10', NULL),
(15, 'Kota Tua', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754806537_6311.jpg', 2, 2, NULL, '2025-08-10 06:15:37', NULL),
(16, 'Tanjidor', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754806556_5686.jpg', 2, 2, NULL, '2025-08-10 06:15:56', NULL),
(17, 'Monumen Nasional', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754806583_2858.jpg', 2, 2, NULL, '2025-08-10 06:16:23', NULL),
(18, 'Silat Betawi', 'Snack renyah dengan rasa original yang tak terlupakan', 'product_1754806602_2986.jpg', 2, 2, NULL, '2025-08-10 06:16:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subkategori`
--

CREATE TABLE `subkategori` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_subkategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subkategori`
--

INSERT INTO `subkategori` (`id`, `id_kategori`, `nama_subkategori`, `created_at`) VALUES
(1, 2, 'Bali', '2025-08-07 22:46:31'),
(2, 2, 'Jakarta', '2025-08-07 22:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `testimoni`
--

CREATE TABLE `testimoni` (
  `id` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `komentar` text NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimoni`
--

INSERT INTO `testimoni` (`id`, `nama_pelanggan`, `foto`, `komentar`, `rating`, `created_at`, `updated_at`) VALUES
(1, 'Sarah Putri', 'testi_1754881956_testi.png', 'CHIBOR benar-benar bikin nagih! Sekali coba langsung ketagihan. Rasanya unik dan teksturnya pas banget.', 5, '2025-08-07 22:46:31', '2025-08-11 03:12:36'),
(2, 'Ahmad Rizki', 'testi_1754881948_testi.png', 'Produk lokal yang berkualitas internasional. Packaging juga menarik, cocok buat oleh-oleh.', 5, '2025-08-07 22:46:31', '2025-08-11 03:12:28'),
(3, 'Maya Sari', 'testi_1755133011_testi.png', 'Produk lokal yang berkualitas internasional. Packaging juga menarik, cocok buat oleh-oleh.', 5, '2025-08-07 22:46:31', '2025-08-14 00:56:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_subkategori` (`id_subkategori`);

--
-- Indexes for table `subkategori`
--
ALTER TABLE `subkategori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `testimoni`
--
ALTER TABLE `testimoni`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `subkategori`
--
ALTER TABLE `subkategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `testimoni`
--
ALTER TABLE `testimoni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`id_subkategori`) REFERENCES `subkategori` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subkategori`
--
ALTER TABLE `subkategori`
  ADD CONSTRAINT `subkategori_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
