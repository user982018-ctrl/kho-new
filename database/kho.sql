-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 30, 2024 lúc 10:56 AM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `kho`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(4, 'thịt heo', 0, '2024-02-29 23:52:10', '2024-02-29 23:52:10'),
(5, 'thịt bò', 0, '2024-02-29 23:53:35', '2024-02-29 23:53:35'),
(8, 'Áo thun', 0, '2024-03-13 23:51:00', '2024-03-13 23:51:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_02_03_145250_create_product_table', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `id_product` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `sex` int(11) NOT NULL DEFAULT 0 COMMENT '0:nam\r\n1:nữ',
  `total` float DEFAULT NULL,
  `note` text DEFAULT NULL,
  `assign_user` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `district` int(11) DEFAULT NULL,
  `ward` varchar(11) DEFAULT NULL,
  `brand_ship` int(11) NOT NULL DEFAULT 0 COMMENT '0:giao hàng nhanh\r\n1:GHTK\r\n2:Khác',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_price_sale` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `id_product`, `phone`, `address`, `name`, `sex`, `total`, `note`, `assign_user`, `qty`, `province`, `district`, `ward`, `brand_ship`, `status`, `is_price_sale`, `created_at`, `updated_at`) VALUES
(20, '[{\"id\":34,\"val\":1},{\"id\":26,\"val\":1},{\"id\":37,\"val\":1}]', '0369691451', 'Về chợ chiều giăng cao bát tràng gia Lâm hà Nội', 'Chị Lưu Hường', 1, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:20:25'),
(21, '[{\"id\":27,\"val\":1},{\"id\":39,\"val\":1},{\"id\":31,\"val\":1}]', '0767925188', 'Tiệm chả lụa Anh Đào đường Nguyễn Trãi phường 1 TP Vĩnh Long', 'Anh Hoc Nguyễn', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 0, 0, '2024-03-07 08:47:57', '2024-03-23 10:20:52'),
(22, '[{\"id\":22,\"val\":1},{\"id\":34,\"val\":1},{\"id\":30,\"val\":1},{\"id\":37,\"val\":1}]', '0971568325', 'Thôn Đai Tin, Xã Phước Lộc, Huyện Tuy Phước, Bình Định', 'Anh Linh', 0, 396000, NULL, 49, 4, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:21:04'),
(23, '[{\"id\":24,\"val\":1},{\"id\":28,\"val\":1},{\"id\":40,\"val\":1}]', '0981437393', 'Chung Cư RuBy Land, Số 4 Lê Quát, Tân Thới Hòa, Tân Phú, HCM', 'Anh Việt', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:21:20'),
(24, '[{\"id\":28,\"val\":2},{\"id\":40,\"val\":2}]', '0903616925', '134A Đỗ Xuân Hợp, Phước Long A, Q9', 'Anh Trần Đức', 0, 396000, NULL, 49, 4, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:22:13'),
(25, '[{\"id\":27,\"val\":1},{\"id\":31,\"val\":1},{\"id\":39,\"val\":1}]', '0905787667', 'Số 10 dường Huỳnh Thúc Kháng, Phường Nghĩa Thành, TP. Gia Nghĩa, DakNong', 'Anh Phát', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:22:21'),
(26, '[{\"id\":27,\"val\":1},{\"id\":39,\"val\":1},{\"id\":31,\"val\":1}]', '0392378379', 'Khu Phố An Hòa, Phường Hòa Lợi, Tx Bến Cát, Bình Dương', 'a Tâm', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:22:28'),
(27, '[{\"id\":39,\"val\":1},{\"id\":23,\"val\":2}]', '0839936175', 'Khu Công Nghiệp SamSung, Yên Phong, Bắc Ninh', 'Anh Dũng', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 0, 0, '2024-03-07 08:47:57', '2024-03-23 10:22:37'),
(28, '[{\"id\":22,\"val\":1},{\"id\":34,\"val\":1},{\"id\":26,\"val\":1},{\"id\":37,\"val\":2}]', '0907240263', 'Xưởng gỗ Vĩnh hẻm 24B Phường Kim Dinh, TP BRVT', 'Anh Hòa', 0, 495000, NULL, 49, 5, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:22:44'),
(29, '[{\"id\":36,\"val\":1}]', '0798413262', 'Tổ 4 , khu phố 1, Phường Hắc Dịch, Tx Phú Mỹ, Bà Rịa - Vũng Tàu', 'Anh Hùng', 0, 99000, NULL, 49, 1, NULL, NULL, NULL, 0, 3, 0, '2024-03-07 08:47:57', '2024-03-23 10:22:47'),
(30, '[{\"id\":36,\"val\":1},{\"id\":32,\"val\":1},{\"id\":40,\"val\":1}]', '0942324727', '81 Lê Hồng Phong, Khóm Bắc Sơn, Thị Trấn Núi Sập, Huyện Thoại Sơn, AN Giang', 'Thao Phan', 0, 297000, 'trả hàng => không vừa size => ok', 49, 3, NULL, NULL, NULL, 0, 0, 0, '2024-03-08 09:30:51', '2024-03-23 10:22:59'),
(31, '[{\"id\":23,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":2}]', '0905481353', 'K884/09/56 Nguyễn Lương Bằng, Quận Liên Chiểu, Đà Nẳng', 'Anh Cường', 0, 396000, NULL, 49, 4, NULL, NULL, NULL, 0, 3, 0, '2024-03-08 09:30:51', '2024-03-23 10:23:31'),
(32, '[{\"id\":23,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":2},{\"id\":31,\"val\":1}]', '0965559782', 'Thôn từ Nham, Xã Xuân Thịnh, Thị Xã Sông Cầu, Phú Yên', 'Anh Phong', 0, 495000, NULL, 49, 5, NULL, NULL, NULL, 0, 0, 0, '2024-03-08 09:30:51', '2024-03-23 11:05:33'),
(33, '[{\"id\":23,\"val\":1},{\"id\":35,\"val\":1}]', '0329637105', 'Cảng cá Lạch, Bảng Phương Hai Binh, Nghi Sơn, Thanh Hóa', 'Anh Tuấn', 0, 198000, NULL, 49, 2, NULL, NULL, NULL, 0, 0, 0, '2024-03-08 09:30:51', '2024-03-23 10:16:36'),
(34, '[{\"id\":23,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":1}]', '0911070203', '38/1 Thống Nhất, Thị Trấn Phan Rí Cửa, Tuy Phong, Bình Thuận', 'Anh Thin', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-08 09:30:51', '2024-03-23 10:18:11'),
(35, '[{\"id\":26,\"val\":1},{\"id\":37,\"val\":2}]', '0918463579', '765 Trần Phú , Phườn BLao, TP. Bảo Lộc, Lâm Đồng', 'Anh Minh Tuấn', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-08 09:30:51', '2024-03-23 11:05:57'),
(36, '[{\"id\":35,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":1}]', '0919795051', '369/25/7 Lý Thái Tổ, P8 Q10', 'Anh Thảo Trần', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-09 11:07:38', '2024-03-23 11:07:38'),
(37, '[{\"id\":39,\"val\":1},{\"id\":26,\"val\":1}]', '0373262963', 'Thôn Hoá Trung xã Quế Thọ, Hiệp Đức', 'Anh Tuân', 0, 198000, NULL, 49, 2, NULL, NULL, NULL, 0, 3, 0, '2024-03-09 11:07:38', '2024-03-23 11:08:45'),
(38, '[{\"id\":26,\"val\":1},{\"id\":37,\"val\":1},{\"id\":30,\"val\":1}]', '0857422667', 'nhà 18 đường hồ tùng mậu phường An Bình thị xã buôn Hồ', 'Tuyết Sương', 1, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-11 11:10:28', '2024-03-23 11:10:28'),
(39, '[{\"id\":23,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":2}]', '0336153973', 'Thôn 6 thị trấn chưprong huyện chưprong', 'FB Tiên Nhỏ', 1, 396000, NULL, 49, 4, NULL, NULL, NULL, 0, 3, 0, '2024-03-11 11:10:28', '2024-03-23 11:11:23'),
(40, '[{\"id\":23,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":2},{\"id\":35,\"val\":1}]', '0345424859', 'Thôn 6 số 86 đường lê lợi khóm 3 thị trấn mỹ an huyện tháp 10', 'Nguyễn Vĩnh Hưng', 0, 495000, NULL, 49, 5, NULL, NULL, NULL, 0, 3, 0, '2024-03-11 11:10:28', '2024-03-23 11:12:14'),
(41, '[{\"id\":34,\"val\":1},{\"id\":37,\"val\":1}]', '0353469018', 'ấp thị tứ thị trấn 1 ngàn châu thành a', 'anh Dư', 0, 198000, NULL, 49, 2, NULL, NULL, NULL, 0, 3, 0, '2024-03-12 11:14:19', '2024-03-23 11:18:17'),
(42, '[{\"id\":27,\"val\":1},{\"id\":35,\"val\":1},{\"id\":39,\"val\":1}]', '0359958969', 'tổ dân phố ba thác bà yên bình', 'A Khánh', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-13 11:15:32', '2024-03-23 11:15:32'),
(43, '[{\"id\":37,\"val\":2}]', '0374715840', 'p Ngã Tư, Hưng Điền B,Tân Hưng', 'Phạm Văn Bằng', 0, 198000, NULL, 49, 2, NULL, NULL, NULL, 0, 3, 0, '2024-03-13 11:15:32', '2024-03-23 11:20:04'),
(44, '[{\"id\":35,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":1}]', '0907748112', '46/5 Lê Thái Tổ phường 2 tpvl tỉnh', 'A Bỉnh', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 0, 0, '2024-03-13 11:15:32', '2024-03-23 11:25:39'),
(45, '[{\"id\":24,\"val\":1},{\"id\":28,\"val\":1},{\"id\":36,\"val\":1}]', '0976224958', 'quầy thuốc hồng nghĩa - xã la bằng - đại từ -thái Nguyên', 'A Hồng', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-14 11:26:15', '2024-03-23 11:26:15'),
(46, '[{\"id\":34,\"val\":1},{\"id\":26,\"val\":1},{\"id\":37,\"val\":1}]', '0918503676', '14 yết kiêu phường 6 Dalat', 'Anh Quang', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-14 11:26:15', '2024-03-23 11:26:54'),
(47, '[{\"id\":40,\"val\":1},{\"id\":36,\"val\":1},{\"id\":24,\"val\":1},{\"id\":28,\"val\":1}]', '0907234167', 'A47 Thái phiên f16 q11 tphcm', 'a Khang', 0, 396000, NULL, 49, 4, NULL, NULL, NULL, 0, 3, 0, '2024-03-14 11:26:15', '2024-03-23 11:27:40'),
(48, '[{\"id\":26,\"val\":1},{\"id\":22,\"val\":1},{\"id\":34,\"val\":1}]', '0914777711', '4 Lê Công Trình.. F4.. TP Tân An. Long An', 'Anh Phương', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 0, 0, '2024-03-14 11:26:15', '2024-03-23 11:28:25'),
(49, '[{\"id\":27,\"val\":2},{\"id\":39,\"val\":1}]', '0374323577', 'ấp long hòa 1 xã long Phú thị xã long Mỹ tỉnh Hậu Giang', 'Anh Phong', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-14 11:26:15', '2024-03-23 11:29:37'),
(50, '[{\"id\":34,\"val\":1}]', '0947172917', 'Ap phu thuan b,xa phu lam ,huyen phu tan ,an giang', 'Anh Lơi', 0, 99000, NULL, 49, 1, NULL, NULL, NULL, 0, 0, 0, '2024-03-15 11:30:07', '2024-03-23 11:30:07'),
(51, '[{\"id\":39,\"val\":2},{\"id\":27,\"val\":1}]', '0972457497', '179 ba đình , p , thắng lợi , tp , kon tum', 'anh Vĩ', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-15 11:30:07', '2024-03-23 11:32:04'),
(52, '[{\"id\":21,\"val\":1}]', '0966554566', 'tổ 1 Phường hương Sơn tp thái nguyên', 'Ket Koi', 0, 99000, 'giao sai màu', 49, 1, NULL, NULL, NULL, 0, 0, 0, '2024-03-15 11:30:07', '2024-03-23 11:32:39'),
(53, '[{\"id\":28,\"val\":1},{\"id\":36,\"val\":1},{\"id\":40,\"val\":1}]', '0989742779', '.tổ 7.đồng xuân.phúc yên.vĩnh phúc', 'Anh Diễn', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-15 11:30:07', '2024-03-23 11:33:13'),
(54, '[{\"id\":31,\"val\":1},{\"id\":27,\"val\":1},{\"id\":39,\"val\":1}]', '0348776806', '460 tôn đức thắng-bãi bông-phổ yên-thái nguyên', 'Anh Dương', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-15 11:30:07', '2024-03-23 11:33:57'),
(55, '[{\"id\":27,\"val\":1}]', '0932232422', '59 phan huy thực .p tân kiển q7', 'Kiến Trúc Vô Ngã', 0, 99000, NULL, 49, 1, NULL, NULL, NULL, 0, 3, 0, '2024-03-15 11:30:07', '2024-03-23 11:34:24'),
(56, '[{\"id\":21,\"val\":1},{\"id\":38,\"val\":1},{\"id\":25,\"val\":1}]', '0969366882', '16 phạm văn đồng-phú thượng- tp huế', 'Anh Đức-quán mộc-kiệt', 0, 297000, NULL, 49, 3, NULL, NULL, NULL, 0, 3, 0, '2024-03-15 11:30:07', '2024-03-23 11:35:11'),
(57, '[{\"id\":31,\"val\":1},{\"id\":27,\"val\":1}]', '0937688416', '12 KDC TIẾN THẠNH TIẾN LỢI TP PHAN THIẾT', 'Anh Minh', 0, 198000, NULL, 49, 2, NULL, NULL, NULL, 0, 3, 0, '2024-02-20 11:30:07', '2024-03-23 11:35:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product`
--

CREATE TABLE `product` (
  `id` int(11) UNSIGNED NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` double NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0: disable\r\n1:enable',
  `roles` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product`
--

INSERT INTO `product` (`id`, `category_id`, `name`, `qty`, `price`, `status`, `roles`, `created_at`, `updated_at`) VALUES
(21, 4, 'Paulo Trắng M', -2, 99000, 1, '2', '2024-03-13 23:51:39', '2024-03-23 11:35:11'),
(22, 4, 'Paulo Trắng L', 12, 99000, 1, '2', '2024-03-13 23:51:47', '2024-03-23 11:28:25'),
(23, 4, 'Paulo Trắng XL', 48, 99000, 1, '2', '2024-03-13 23:51:53', '2024-03-23 11:14:19'),
(24, 4, 'Paulo Trắng XXL', -1, 99000, 1, '2', '2024-03-13 23:51:58', '2024-03-23 11:27:41'),
(25, 4, 'Paulo Xám M', 25, 99000, 1, '2', '2024-03-13 23:52:07', '2024-03-23 11:35:11'),
(26, 4, 'Paulo Xám L', -3, 99000, 1, '2', '2024-03-13 23:52:11', '2024-03-23 11:28:25'),
(27, 4, 'Paulo Xám XL', 30, 99000, 1, '2', '2024-03-13 23:52:16', '2024-03-23 11:35:47'),
(28, 4, 'Paulo Xám XXL', 3, 99000, 1, '2', '2024-03-13 23:52:21', '2024-03-23 11:33:13'),
(29, 4, 'Paulo Xanh Green M', 26, 99000, 1, '2', '2024-03-13 23:53:47', '2024-03-14 00:04:39'),
(30, 4, 'Paulo Xanh Green L', 10, 99000, 1, '2', '2024-03-13 23:53:51', '2024-03-23 11:10:28'),
(31, 4, 'Paulo Xanh Green XL', 59, 99000, 1, '2', '2024-03-13 23:53:56', '2024-03-23 11:35:47'),
(32, 4, 'Paulo Xanh Green XXL', 12, 99000, 1, '2', '2024-03-13 23:54:01', '2024-03-23 10:22:59'),
(33, 4, 'Paulo Đen M', 27, 99000, 1, '2', '2024-03-13 23:54:15', '2024-03-14 00:01:53'),
(34, 4, 'Paulo Đen L', -6, 99000, 1, '2', '2024-03-13 23:54:18', '2024-03-23 11:30:07'),
(35, 4, 'Paulo Đen XL', 70, 99000, 1, '2', '2024-03-13 23:54:35', '2024-03-23 11:25:39'),
(36, 4, 'Paulo Đen XXL', -27, 99000, 1, '2', '2024-03-13 23:54:39', '2024-03-23 11:33:13'),
(37, 4, 'Paulo Xanh Hải Quân L', -29, 99000, 1, '2', '2024-03-13 23:55:10', '2024-03-23 11:26:54'),
(38, 4, 'Paulo Xanh Hải Quân M', 13, 99000, 1, '2', '2024-03-13 23:55:18', '2024-03-23 11:35:11'),
(39, 4, 'Paulo Xanh Hải Quân XL', -3, 99000, 1, '2', '2024-03-13 23:55:22', '2024-03-23 11:33:57'),
(40, 4, 'Paulo Xanh Hải Quân XXL', -1, 99000, 1, '2', '2024-03-13 23:55:27', '2024-03-23 11:33:13'),
(41, 4, 'Đạm SC 20l', 99, 1500000, 1, '3', '2024-03-15 08:50:23', '2024-03-23 07:28:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_order`
--

CREATE TABLE `shipping_order` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_code` varchar(255) NOT NULL,
  `vendor_ship` varchar(255) DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `shipping_order`
--

INSERT INTO `shipping_order` (`id`, `order_id`, `order_code`, `vendor_ship`, `created_at`, `updated_at`) VALUES
(2, 57, 'G8N83KXV', 'GHN', '2024-03-26', '2024-03-26'),
(3, 56, 'G8NEUTTL', 'GHN', '2024-03-28', '2024-03-28'),
(4, 55, 'G8NXFDUV', 'GHN', '2024-03-28', '2024-03-28'),
(5, 54, 'G8DQ4BCU', 'GHN', '2024-03-29', '2024-03-29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_sale` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `status`, `is_sale`, `created_at`, `updated_at`) VALUES
(1, 'admin test', 'admin-test@gmail.com', NULL, '$2y$12$qR8QAv7mKmCX4FWEnowO2ecgfGIgEybveXScHyTyMcxRBPhDnSIlG', NULL, '[\"1\",\"3\",\"4\"]', 1, 0, '2024-03-14 09:25:28', '2024-03-15 08:45:05'),
(4, 'dat', 'admin@gmail.com', NULL, '$2y$12$M6oN8x4P9EBJ/yc1kJy2A.5B9EM9buEuF2AfJ7DIM9YD.DbDWhA8.', NULL, '[\"1\",\"2\",\"3\",\"4\"]', 1, 1, '2024-03-15 01:40:19', '2024-03-16 01:30:01'),
(37, 'dat', 'admin321@gmail.com', NULL, '$2y$12$xcxkulHUcTKxueRCjtILguyCRHTN0YmT.rtjmMGqRNkWA.sXF.j6G', NULL, '[\"2\"]', 1, 0, '2024-03-15 02:07:25', '2024-03-15 21:39:57'),
(49, 'hiep', 'hiep@gmail.com', NULL, '$2y$12$0BA3B5KMjWap33Yv.L.9sOOYsJn1zVeaNnzqbkGvBCjHEREBzBjyG', NULL, '[\"2\"]', 1, 1, '2024-03-16 01:05:07', '2024-03-16 01:19:59'),
(50, 'sale', 'sale@gmail.com', NULL, '$2y$12$F/dBBlH1FGj.r9ztS4Jsres2cXC0sLeQfNtaZ3ohMqC10uEwh.ECC', NULL, '[\"3\"]', 1, 1, '2024-03-30 02:20:10', '2024-03-30 02:20:10');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `shipping_order`
--
ALTER TABLE `shipping_order`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `shipping_order`
--
ALTER TABLE `shipping_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
