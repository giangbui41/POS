-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2025 at 06:11 PM
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
-- Database: `quanlybanhang`
--
CREATE DATABASE IF NOT EXISTS `quanlybanhang` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `quanlybanhang`;

-- --------------------------------------------------------

--
-- Table structure for table `cthd`
--

CREATE TABLE `cthd` (
  `ID` varchar(255) NOT NULL,
  `MAHD` varchar(255) NOT NULL,
  `MASP` varchar(255) NOT NULL,
  `SOLUONG` int(11) DEFAULT NULL,
  `DONGIA` int(11) DEFAULT NULL,
  `TONGTIEN` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cthd`
--

INSERT INTO `cthd` (`ID`, `MAHD`, `MASP`, `SOLUONG`, `DONGIA`, `TONGTIEN`) VALUES
('38035', 'HD-328427', 'SP680b09fe08976', 3, 25000000, 75000000),
('55762', 'HD-665440', 'SP680b09da9bec1', 1, 21000000, 21000000),
('62539', 'HD-535916', 'SP680b0a355e646', 1, 60000000, 60000000),
('64382', 'HD-732184', 'SP680b0ab55312a', 3, 8000000, 24000000),
('85333', 'HD-535916', 'SP680b0a7893f29', 1, 25000000, 25000000);

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc`
--

CREATE TABLE `danhmuc` (
  `MADM` varchar(255) NOT NULL,
  `TENDANHMUC` varchar(255) DEFAULT NULL,
  `MOTA` varchar(255) DEFAULT NULL,
  `NGAYTAO` date DEFAULT current_timestamp(),
  `NGUOITAO` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `danhmuc`
--

INSERT INTO `danhmuc` (`MADM`, `TENDANHMUC`, `MOTA`, `NGAYTAO`, `NGUOITAO`) VALUES
('DM68086c670b552', 'laptop', 'Các loại máy tính', '2025-04-23', 'giang'),
('DM680b070e9fe4f', 'Iphone', 'Điện thoại iphone', '2025-04-25', 'Giang'),
('DM680b07254e6e3', 'Samsung', 'Các dòng điện thoại samsung', '2025-04-25', 'Giang'),
('DM680b091e029ed', 'macbook', 'macbook', '2025-04-25', 'Giang'),
('DM680b094b4213d', 'Watch', 'Các dòng đồng hồ thông minh', '2025-04-25', 'Giang');

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `MAHD` varchar(255) NOT NULL,
  `MAKH` int(11) NOT NULL,
  `MANV` varchar(255) NOT NULL,
  `NGAYTAO` date NOT NULL DEFAULT current_timestamp(),
  `TONGTIEN` int(100) NOT NULL,
  `PHUONGTHUC` varchar(50) NOT NULL DEFAULT 'completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `hoadon`
--

INSERT INTO `hoadon` (`MAHD`, `MAKH`, `MANV`, `NGAYTAO`, `TONGTIEN`, `PHUONGTHUC`) VALUES
('HD-328427', 8, 'NV002', '2025-05-01', 75000000, 'Chuyển khoản'),
('HD-535916', 1, 'NV002', '2025-05-01', 85000000, 'Tiền mặt'),
('HD-665440', 2, 'NV002', '2025-05-01', 21000000, 'Tiền mặt'),
('HD-732184', 5, 'NV002', '2025-05-01', 24000000, 'Tiền mặt');

-- --------------------------------------------------------

--
-- Table structure for table `khachhang`
--

CREATE TABLE `khachhang` (
  `MAKH` int(11) NOT NULL,
  `HOTEN` varchar(255) DEFAULT NULL,
  `SDT` varchar(10) DEFAULT NULL,
  `DIACHI` varchar(255) DEFAULT NULL,
  `TRANGTHAI` varchar(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `khachhang`
--

INSERT INTO `khachhang` (`MAKH`, `HOTEN`, `SDT`, `DIACHI`, `TRANGTHAI`) VALUES
(1, 'Giang', '0123456789', 'TPHCM\r\n', '0'),
(2, 'Lê Nguyễn Cát Tường', '0778077883', 'Quận 1', '0'),
(3, 'Nguyễn Khang', '0277775555', 'Quận 8', '0'),
(4, 'Khuyết Danh', '0568686868', 'Đà Lạt', '0'),
(5, 'Trọng Hiếu', '0120120121', 'Quận 5', '0'),
(6, 'Ngọc Ân', '0903751126', 'Tiền Giang', '0'),
(7, 'Trịnh Thành', '0770770779', 'Đồng Tháp', '0'),
(8, 'Chí Tường', '0120775657', 'Hà Nội', '0');

-- --------------------------------------------------------

--
-- Table structure for table `nhanvien`
--

CREATE TABLE `nhanvien` (
  `ANHDAIDIEN` varchar(255) DEFAULT NULL,
  `MANV` varchar(255) NOT NULL,
  `HOTEN` varchar(255) DEFAULT NULL,
  `EMAIL` varchar(255) DEFAULT NULL,
  `TENDANGNHAP` varchar(255) DEFAULT NULL,
  `MATKHAU` varchar(255) NOT NULL DEFAULT '52300266',
  `SDT` varchar(10) DEFAULT NULL,
  `LOAI` enum('Admin','Staff') DEFAULT 'Staff',
  `TRANGTHAI` enum('Locked','Unlocked') DEFAULT 'Unlocked',
  `NHANVIENMOI` tinyint(1) DEFAULT NULL,
  `TOKEN_DANGNHAP` varchar(255) DEFAULT NULL,
  `TOKEN_HETHAN` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `nhanvien`
--

INSERT INTO `nhanvien` (`ANHDAIDIEN`, `MANV`, `HOTEN`, `EMAIL`, `TENDANGNHAP`, `MATKHAU`, `SDT`, `LOAI`, `TRANGTHAI`, `NHANVIENMOI`, `TOKEN_DANGNHAP`, `TOKEN_HETHAN`) VALUES
('frontend/images/anhdaidien/avatar_admin_1745330322.jpg', 'NV001', 'IntroNix-System', 'admin@gmail.com', 'admin', 'admin', '0123456789', 'Admin', 'Unlocked', 0, NULL, NULL),
(NULL, 'NV002', 'Lê Nguyễn Cát Tường', 'brandya337@gmail.com', 'brandya337', '123', NULL, 'Staff', 'Unlocked', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sanpham`
--

CREATE TABLE `sanpham` (
  `MASP` varchar(255) NOT NULL,
  `BARCODE` varchar(20) NOT NULL,
  `TENSP` varchar(255) DEFAULT NULL,
  `ANHSP` varchar(255) DEFAULT NULL,
  `GIAGOC` int(11) DEFAULT NULL,
  `GIABANLE` int(11) DEFAULT NULL,
  `SOLUONG` int(11) NOT NULL,
  `DANHMUC` varchar(255) DEFAULT NULL,
  `NGAYTAO` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sanpham`
--

INSERT INTO `sanpham` (`MASP`, `BARCODE`, `TENSP`, `ANHSP`, `GIAGOC`, `GIABANLE`, `SOLUONG`, `DANHMUC`, `NGAYTAO`) VALUES
('SP680b09da9bec1', '835609343248', 'Iphone12', 'frontend/images/anhsp/SP680b09da9bec1.jpg', 13000000, 21000000, 4, 'DM680b070e9fe4f', '2025-04-25'),
('SP680b09fe08976', '810211307887', 'iphone13-blue', 'frontend/images/anhsp/SP680b09fe08976.jpg', 15000000, 25000000, 3, 'DM680b070e9fe4f', '2025-04-25'),
('SP680b0a355e646', '887345515742', 'macbook pro 16', 'frontend/images/anhsp/SP680b0a355e646.jpg', 30000000, 60000000, 6, 'DM680b091e029ed', '2025-04-25'),
('SP680b0a7893f29', '859332146454', 'samsung-galaxyS25', 'frontend/images/anhsp/SP680b0a7893f29.jpg', 17000000, 25000000, 14, 'DM680b07254e6e3', '2025-04-25'),
('SP680b0ab55312a', '850245313884', 'apple watch serie 6', 'frontend/images/anhsp/SP680b0ab55312a.jpg', 4500000, 8000000, 15, 'DM680b094b4213d', '2025-04-25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cthd`
--
ALTER TABLE `cthd`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `MAHD` (`MAHD`),
  ADD KEY `MASP` (`MASP`);

--
-- Indexes for table `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`MADM`),
  ADD UNIQUE KEY `TENDANHMUC` (`TENDANHMUC`);

--
-- Indexes for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`MAHD`),
  ADD KEY `MAKH` (`MAKH`),
  ADD KEY `MANV` (`MANV`);

--
-- Indexes for table `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MAKH`);

--
-- Indexes for table `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`MANV`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`),
  ADD UNIQUE KEY `TEN_DANG_NHAP` (`TENDANGNHAP`);

--
-- Indexes for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`MASP`),
  ADD UNIQUE KEY `barcode_unipue` (`BARCODE`),
  ADD UNIQUE KEY `TENSP` (`TENSP`),
  ADD KEY `DANH_MUC` (`DANHMUC`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cthd`
--
ALTER TABLE `cthd`
  ADD CONSTRAINT `cthd_ibfk_1` FOREIGN KEY (`MAHD`) REFERENCES `hoadon` (`MAHD`) ON DELETE CASCADE,
  ADD CONSTRAINT `cthd_ibfk_2` FOREIGN KEY (`MASP`) REFERENCES `sanpham` (`MASP`) ON DELETE CASCADE;

--
-- Constraints for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`MAKH`) REFERENCES `khachhang` (`MAKH`) ON DELETE CASCADE,
  ADD CONSTRAINT `hoadon_ibfk_2` FOREIGN KEY (`MANV`) REFERENCES `nhanvien` (`MANV`) ON DELETE CASCADE;

--
-- Constraints for table `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`DANHMUC`) REFERENCES `danhmuc` (`MADM`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
