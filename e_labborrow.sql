/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.16-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: e_labborrow
-- ------------------------------------------------------
-- Server version	10.11.16-MariaDB

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
-- Table structure for table `alat`
--

DROP TABLE IF EXISTS `alat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `kategori` varchar(100) DEFAULT 'Umum',
  `stok_total` int(11) NOT NULL DEFAULT 0,
  `stok_tersedia` int(11) NOT NULL DEFAULT 0,
  `stok_rusak` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alat`
--

LOCK TABLES `alat` WRITE;
/*!40000 ALTER TABLE `alat` DISABLE KEYS */;
INSERT INTO `alat` VALUES
(1,'Multimeter Digital','ELK-001','Elektronika',10,10,0,'Alat ukur tegangan, arus, dan resistansi','2026-04-26 05:53:56'),
(2,'Osiloskop 2 Channel','ELK-002','Elektronika',5,5,0,'Osiloskop digital 100MHz','2026-04-26 05:53:56'),
(3,'Power Supply DC','ELK-003','Elektronika',8,6,0,'Power supply variabel 0-30V 5A','2026-04-26 05:53:56'),
(4,'Jangka Sorong','MEK-001','Fisika',15,15,0,'Jangka sorong digital 0-150mm','2026-04-26 05:53:56'),
(5,'Mikrometer Sekrup','MEK-002','Fisika',10,10,0,'Mikrometer 0-25mm','2026-04-26 05:53:56'),
(6,'Mikroskop Binokuler','OPT-001','Biologi',4,4,0,'Perbesaran 40x-1000x','2026-04-26 05:53:56'),
(7,'Breadboard','LST-001','Elektronika',20,20,0,'Breadboard 830','2026-04-26 05:53:56'),
(8,'Arduino ','KMP-001','Elektronika',12,12,0,'Mikrokontroler Arduino R3','2026-04-26 05:53:56'),
(9,'Raspberry Pi 4','KMP-002','Elektronika',5,5,0,'Single Board Computer 4GB','2026-04-26 05:53:56');
/*!40000 ALTER TABLE `alat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman`
--

DROP TABLE IF EXISTS `peminjaman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `alat_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `tgl_pinjam` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `tgl_dikembalikan` datetime DEFAULT NULL,
  `keperluan` text NOT NULL,
  `status` enum('pending','disetujui','perpanjangan','selesai','ditolak') DEFAULT 'pending',
  `jml_baik` int(11) NOT NULL DEFAULT 0,
  `jml_rusak` int(11) NOT NULL DEFAULT 0,
  `jml_hilang` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `alat_id` (`alat_id`),
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman`
--

LOCK TABLES `peminjaman` WRITE;
/*!40000 ALTER TABLE `peminjaman` DISABLE KEYS */;
INSERT INTO `peminjaman` VALUES
(2,2,1,3,'2026-04-26','2026-04-29',NULL,'minjem','ditolak',0,0,0,'2026-04-26 06:23:49'),
(3,2,4,1,'2026-04-26','2026-05-06','2026-05-02 12:42:20','pliss','selesai',0,0,0,'2026-04-26 06:25:32'),
(4,2,6,2,'2026-05-09','2026-05-16','2026-05-09 21:43:22','Buat praktikum','selesai',0,0,0,'2026-05-09 14:34:10'),
(5,2,3,3,'2026-05-09','2026-05-16','2026-05-09 21:47:58','biasalah','selesai',0,0,0,'2026-05-09 14:47:30'),
(6,2,3,2,'2026-05-11','2026-05-18',NULL,'asekk','disetujui',0,0,0,'2026-05-11 16:24:31');
/*!40000 ALTER TABLE `peminjaman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perpanjangan`
--

DROP TABLE IF EXISTS `perpanjangan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `perpanjangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `peminjaman_id` int(11) NOT NULL,
  `tgl_kembali_baru` date NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `peminjaman_id` (`peminjaman_id`),
  CONSTRAINT `perpanjangan_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perpanjangan`
--

LOCK TABLES `perpanjangan` WRITE;
/*!40000 ALTER TABLE `perpanjangan` DISABLE KEYS */;
INSERT INTO `perpanjangan` VALUES
(1,3,'2026-05-06','kurang mass','disetujui','2026-04-26 06:26:28');
/*!40000 ALTER TABLE `perpanjangan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `npm` varchar(20) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','admin') DEFAULT 'mahasiswa',
  `jurusan` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `npm` (`npm`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'ADMIN001', 'Administrator Lab','admin@lab.ac.id','$2y$10$oRUcHd/kANs77GzbljGo3uJlWzz/AJ4p1zKLLNtPgB3R9bQjzqUu6','admin','Pengelola Lab',NULL,'2026-04-26 05:53:56'),
(2,'2417052016', 'Rifan Habibi','2417052016@students.unila.ac.id','$2y$10$oRUcHd/kANs77GzbljGo3uJlWzz/AJ4p1zKLLNtPgB3R9bQjzqUu6','mahasiswa','Ilmu Komputer',NULL,'2026-04-26 05:53:56'),
(3,'2417052025', 'Salsabila Yuriska','2417052025@students.unila.ac.id','$2y$10$oRUcHd/kANs77GzbljGo3uJlWzz/AJ4p1zKLLNtPgB3R9bQjzqUu6','mahasiswa','Ilmu Komputer',NULL,'2026-04-26 05:53:56');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-12 13:54:57
