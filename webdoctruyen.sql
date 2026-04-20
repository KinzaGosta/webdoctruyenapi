-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 19, 2026 lúc 12:03 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webdoctruyen`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Tiên Hiệp', 'tien-hiep'),
(2, 'Kiếm Hiệp', 'kiem-hiep'),
(3, 'Ngôn Tình', 'ngon-tinh'),
(4, 'Đô Thị', 'do-thi'),
(5, 'Huyền Huyễn', 'huyen-huyen'),
(6, 'Nấu ăn', 'nau-an');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `is_deleted`, `created_at`) VALUES
(1, 1, 2, 'hello', 0, 0, '2026-04-18 11:39:24'),
(2, 1, 2, 'Test from api context', 0, 0, '2026-04-18 11:43:39'),
(3, 3, 1, 'a', 1, 0, '2026-04-18 11:45:42'),
(4, 1, 3, 'oke', 1, 0, '2026-04-18 11:46:43'),
(5, 3, 1, 'tot r', 1, 1, '2026-04-18 11:46:53'),
(6, 1, 3, 'hehe', 1, 0, '2026-04-18 11:46:56'),
(7, 1, 3, 'â', 1, 0, '2026-04-18 11:56:46'),
(8, 3, 2, 'g', 0, 0, '2026-04-18 13:37:32'),
(9, 5, 4, 'Hẹ hẹ', 0, 0, '2026-04-18 14:20:17'),
(10, 5, 4, 'Gà', 0, 0, '2026-04-18 14:20:27'),
(11, 1, 3, 'Âjaj', 1, 0, '2026-04-18 14:20:39'),
(12, 3, 1, 'tmt', 1, 0, '2026-04-18 14:21:01'),
(13, 1, 3, 'Jdje', 1, 0, '2026-04-18 14:21:04'),
(14, 5, 1, 'Hello', 1, 0, '2026-04-18 14:21:04'),
(15, 5, 1, 'Hẹ hẹ', 1, 0, '2026-04-18 14:21:13'),
(16, 3, 1, 'ngu', 1, 0, '2026-04-18 14:21:15'),
(17, 1, 5, 'Oke e nhé', 0, 0, '2026-04-18 14:21:16'),
(18, 1, 3, 'Jsjsj', 1, 0, '2026-04-18 14:21:40'),
(19, 1, 3, 'Hdjddj', 1, 0, '2026-04-18 14:21:42'),
(20, 1, 3, 'Jdjddj', 1, 0, '2026-04-18 14:21:44'),
(21, 1, 3, 'Ngu', 1, 0, '2026-04-18 14:21:46'),
(22, 1, 3, 'Ngu', 1, 0, '2026-04-18 14:21:48'),
(23, 1, 3, 'Ngu', 1, 0, '2026-04-18 14:21:50'),
(24, 3, 1, 'a', 0, 0, '2026-04-18 14:46:02'),
(25, 3, 1, 'sao thếnh ỉ', 0, 1, '2026-04-18 14:46:17'),
(26, 3, 1, 'yeah', 0, 0, '2026-04-18 16:12:45'),
(27, 6, 1, 'a', 0, 0, '2026-04-18 16:26:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comics_cache`
--

CREATE TABLE `comics_cache` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comic_favorites`
--

CREATE TABLE `comic_favorites` (
  `user_id` int(11) NOT NULL,
  `comic_slug` varchar(255) NOT NULL,
  `comic_name` varchar(255) NOT NULL,
  `comic_thumb` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comic_favorites`
--

INSERT INTO `comic_favorites` (`user_id`, `comic_slug`, `comic_name`, `comic_thumb`, `created_at`) VALUES
(1, 'vua-vo-dich-tai-mat-the-da-bi-chan-cua-cau-hon', 'Vừa Vô Địch Tại Mạt Thế Đã Bị Chặn Cửa Cầu Hôn', 'https://img.otruyenapi.com/uploads/comics/vua-vo-dich-tai-mat-the-da-bi-chan-cua-cau-hon-thumb.jpg', '2026-01-10 04:54:56'),
(3, 'trong-khai-di-the-ta-dung-guong-than-tro-thanh-vo-dich', 'Trọng Khải Dị Thế: Ta Dùng Gương Thần Trở Thành Vô Địch', 'https://img.otruyenapi.com/uploads/comics/trong-khai-di-the-ta-dung-guong-than-tro-thanh-vo-dich-thumb.jpg', '2026-04-18 12:22:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comic_views`
--

CREATE TABLE `comic_views` (
  `comic_slug` varchar(255) NOT NULL,
  `comic_name` varchar(255) DEFAULT NULL,
  `comic_thumb` varchar(255) DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `last_viewed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comic_views`
--

INSERT INTO `comic_views` (`comic_slug`, `comic_name`, `comic_thumb`, `view_count`, `last_viewed`) VALUES
('abase-the-ace', 'Abase the ace', 'https://img.otruyenapi.com/uploads/comics/abase-the-ace-thumb.jpg', 3074, '2026-01-09 13:49:34'),
('by-your-side-where-happiness-lives', 'By Your Side: Where Happiness Lives!', 'https://img.otruyenapi.com/uploads/comics/by-your-side-where-happiness-lives-thumb.jpg', 3792, '2026-04-18 11:16:05'),
('cau-vuot-gioi-han-roi', NULL, NULL, 2508, '2026-01-02 13:35:17'),
('centuria', 'Centuria', 'https://img.otruyenapi.com/uploads/comics/centuria-thumb.jpg', 3923, '2026-01-02 14:40:19'),
('chieu-hon-gia-sieu-pham', 'Chiêu Hồn Giả Siêu Phàm', 'https://img.otruyenapi.com/uploads/comics/chieu-hon-gia-sieu-pham-thumb.jpg', 2280, '2026-01-10 04:09:48'),
('darenimo-aisarenakatta-shuue-reijou-ga-shiawase-ni-naru-made', 'Darenimo Aisarenakatta Shuue Reijou Ga Shiawase Ni Naru Made', 'https://img.otruyenapi.com/uploads/comics/darenimo-aisarenakatta-shuue-reijou-ga-shiawase-ni-naru-made-thumb.jpg', 1894, '2026-01-02 14:38:40'),
('devil-andamp-ice', NULL, NULL, 2315, '2026-01-01 04:41:40'),
('doi-ta-la-hai-thai-cuc-doi-lap', 'Đôi Ta Là Hai Thái Cực Đối Lập', 'https://img.otruyenapi.com/uploads/comics/doi-ta-la-hai-thai-cuc-doi-lap-thumb.jpg', 2438, '2026-04-18 11:28:14'),
('druid-tai-ga-seoul', NULL, NULL, 1202, '2026-01-01 09:35:53'),
('em-cho-co-muon-chut-lua-nhe', 'Em Cho Cô Mượn Chút Lửa Nhé?', 'https://img.otruyenapi.com/uploads/comics/em-cho-co-muon-chut-lua-nhe-thumb.jpg', 1346, '2026-04-18 11:33:14'),
('gia-vo-lam-phe-vat-hoc-duong', 'Giả Vờ Làm Kẻ Vô Dụng Ở Học Đường', 'https://img.otruyenapi.com/uploads/comics/gia-vo-lam-phe-vat-hoc-duong-thumb.jpg', 2054, '2026-01-09 13:20:43'),
('hoa-giai-di-chung-toc', 'Hòa Giải Dị Chủng Tộc', 'https://img.otruyenapi.com/uploads/comics/hoa-giai-di-chung-toc-thumb.jpg', 2662, '2026-04-18 11:19:30'),
('hundred-burger-chan-isekai-tensei-enikki', NULL, NULL, 1527, '2026-01-01 09:44:50'),
('huyen-thoai-game-thu-tai-xuat', 'Huyền Thoại Game Thủ - Tái Xuất', 'https://img.otruyenapi.com/uploads/comics/huyen-thoai-game-thu-tai-xuat-thumb.jpg', 1110, '2026-01-08 10:21:49'),
('jirai-nandesuka-chihara-san', NULL, NULL, 1250, '2026-01-01 03:57:18'),
('khoc-di-hay-cau-xin-toi-cung-duoc', 'Khóc Đi, Hay Cầu Xin Tôi Cũng Được', 'https://img.otruyenapi.com/uploads/comics/khoc-di-hay-cau-xin-toi-cung-duoc-thumb.jpg', 3573, '2026-04-18 16:21:52'),
('khoi-dau-voi-13-an-ky-toi-thuong', 'Khởi Đầu Với 13 Ẩn Kỹ Tối Thượng', 'https://img.otruyenapi.com/uploads/comics/khoi-dau-voi-13-an-ky-toi-thuong-thumb.jpg', 3776, '2026-01-08 00:14:29'),
('khong-bao-gio-chet-lai-lan-nua', 'Không Bao Giờ Chết Lại Lần Nữa', 'https://img.otruyenapi.com/uploads/comics/khong-bao-gio-chet-lai-lan-nua-thumb.jpg', 4840, '2026-04-18 16:17:18'),
('khong-on-roi-ta-dot-nhien-vo-dich', 'Không Ổn Rồi, Ta Đột Nhiên Vô Địch', 'https://img.otruyenapi.com/uploads/comics/khong-on-roi-ta-dot-nhien-vo-dich-thumb.jpg', 1789, '2026-01-08 10:33:08'),
('level-ga-shihai-suru-sekai-de', NULL, NULL, 1810, '2026-01-02 13:35:20'),
('ly-do-ket-hon', 'Lý Do Kết Hôn', 'https://img.otruyenapi.com/uploads/comics/ly-do-ket-hon-thumb.jpg', 1198, '2026-04-18 11:16:33'),
('midareta-futari-no-mitasare-gohan', NULL, NULL, 3298, '2026-01-01 03:54:20'),
('nao-phang-sieu-nang-luc', 'Não Phẳng Siêu Năng Lực', 'https://img.otruyenapi.com/uploads/comics/nao-phang-sieu-nang-luc-thumb.jpg', 2369, '2026-04-18 11:27:34'),
('ngoai-dong-thoi-gian', NULL, NULL, 2595, '2026-01-01 03:50:23'),
('nha-tro-umine-tren-hon-dao', 'Nhà Trọ Umine Trên Hòn Đảo', 'https://img.otruyenapi.com/uploads/comics/nha-tro-umine-tren-hon-dao-thumb.jpg', 4993, '2026-04-18 16:17:22'),
('nhat-ky-giac-mo', 'Nhật Ký Giấc Mơ', 'https://img.otruyenapi.com/uploads/comics/nhat-ky-giac-mo-thumb.jpg', 2006, '2026-04-18 11:33:40'),
('nu-phu-tu-tien-tu-choi-kich-ban-phao-hoi', 'Nữ Phụ Tu Tiên Từ Chối Kịch Bản Pháo Hôi', 'https://img.otruyenapi.com/uploads/comics/nu-phu-tu-tien-tu-choi-kich-ban-phao-hoi-thumb.jpg', 3682, '2026-01-02 14:45:11'),
('nu-tuong-quan-trong-sinh', NULL, NULL, 2927, '2026-01-02 13:30:22'),
('phat-song-cua-sieu-viet-gia', 'Phát Sóng Của Siêu Việt Giả', 'https://img.otruyenapi.com/uploads/comics/phat-song-cua-sieu-viet-gia-thumb.jpg', 3798, '2026-01-10 04:09:40'),
('ta-no-dich-ca-thanh-chu-lan-ma-nu', NULL, NULL, 1414, '2026-01-01 04:39:46'),
('ta-that-khong-muon-hoc-cam-chu', 'Ta Thật Không Muốn Học Cấm Chú', 'https://img.otruyenapi.com/uploads/comics/ta-that-khong-muon-hoc-cam-chu-thumb.jpg', 1866, '2026-04-18 11:33:33'),
('ta-thu-thap-hau-cung-o-do-thi', NULL, NULL, 2468, '2026-01-01 04:25:15'),
('tales-of-eternia', 'Tales of Eternia', 'https://img.otruyenapi.com/uploads/comics/tales-of-eternia-thumb.jpg', 2612, '2026-04-18 14:34:36'),
('tau-tu-ta-that-khong-phai-ke-ngoc', 'Tẩu Tử: Ta Thật Không Phải Kẻ Ngốc', 'https://img.otruyenapi.com/uploads/comics/tau-tu-ta-that-khong-phai-ke-ngoc-thumb.jpg', 3947, '2026-01-02 14:40:15'),
('tho-san-nguyen-thuy', 'Thợ Săn Nguyên Thủy', 'https://img.otruyenapi.com/uploads/comics/tho-san-nguyen-thuy-thumb.jpg', 2691, '2026-04-18 14:33:49'),
('thong-bach', 'Thông Bách', 'https://img.otruyenapi.com/uploads/comics/thong-bach-thumb.jpg', 3765, '2026-01-10 03:56:09'),
('thong-linh-hoc-vien-chi-bang-dao-sashimi', 'Thống Lĩnh Học Viện Chỉ Bằng Dao Sashimi', 'https://img.otruyenapi.com/uploads/comics/thong-linh-hoc-vien-chi-bang-dao-sashimi-thumb.jpg', 2095, '2026-01-09 13:27:20'),
('thua-ngai-toi-cam-thay-kho-chiu', 'Thưa Ngài, Tôi Cảm Thấy Khó Chịu', 'https://img.otruyenapi.com/uploads/comics/thua-ngai-toi-cam-thay-kho-chiu-thumb.jpg', 2885, '2026-01-10 03:50:51'),
('thuc-quy-mao-hiem-gia', 'Thực Quỷ Mạo Hiểm Giả', 'https://img.otruyenapi.com/uploads/comics/thuc-quy-mao-hiem-gia-thumb.jpg', 4841, '2026-04-18 16:05:47'),
('tieu-thu-tich-tien-di-bui', 'Tiểu Thư Tích Tiền Đi Bụi', 'https://img.otruyenapi.com/uploads/comics/tieu-thu-tich-tien-di-bui-thumb.jpg', 3673, '2026-01-10 13:06:42'),
('tinh-yeu-nho-nhoi', 'Tình Yêu Nhỏ Nhoi', 'https://img.otruyenapi.com/uploads/comics/tinh-yeu-nho-nhoi-thumb.jpg', 4072, '2026-04-18 11:33:25'),
('to-bloom', 'To Bloom', 'https://img.otruyenapi.com/uploads/comics/to-bloom-thumb.jpg', 4809, '2026-04-18 11:16:41'),
('toan-dan-chuyen-chuc-bi-dong-cua-ta-vo-dich', 'Toàn Dân Chuyển Chức: Bị Động Của Ta Vô Địch', 'https://img.otruyenapi.com/uploads/comics/toan-dan-chuyen-chuc-bi-dong-cua-ta-vo-dich-thumb.jpg', 2951, '2026-04-18 16:21:45'),
('toi-bi-quyen-ru-boi-nam-chinh-om-yeu', 'Tôi Bị Quyến Rũ Bởi Nam Chính Ốm Yếu', 'https://img.otruyenapi.com/uploads/comics/toi-bi-quyen-ru-boi-nam-chinh-om-yeu-thumb.jpg', 4027, '2026-04-18 11:33:19'),
('touhou-ba-cong-mot-doujinshi', 'Touhou - Ba Cộng Một (Doujinshi)', 'https://img.otruyenapi.com/uploads/comics/touhou-ba-cong-mot-doujinshi-thumb.jpg', 1549, '2026-04-18 11:33:02'),
('tra-lai-gap-van-su-ty-xin-hay-tu-trong', NULL, NULL, 1020, '2026-01-01 04:25:12'),
('trong-khai-di-the-ta-dung-guong-than-tro-thanh-vo-dich', 'Trọng Khải Dị Thế: Ta Dùng Gương Thần Trở Thành Vô Địch', 'https://img.otruyenapi.com/uploads/comics/trong-khai-di-the-ta-dung-guong-than-tro-thanh-vo-dich-thumb.jpg', 2167, '2026-04-18 16:17:01'),
('trong-sinh-gia-streaming', 'Trọng Sinh Giả Streaming', 'https://img.otruyenapi.com/uploads/comics/trong-sinh-gia-streaming-thumb.jpg', 1162, '2026-01-09 12:27:10'),
('tu-than-phieu-nguyet', 'Tử Thần Phiêu Nguyệt', 'https://img.otruyenapi.com/uploads/comics/tu-than-phieu-nguyet-thumb.jpg', 4930, '2026-04-18 16:17:28'),
('tuy-xa-ma-gan-nhu-chi-em', 'Tuy Xa Mà Gần Như Chị Em.', 'https://img.otruyenapi.com/uploads/comics/tuy-xa-ma-gan-nhu-chi-em-thumb.jpg', 3735, '2026-01-09 12:44:04'),
('tuyet-the-sat-thu-hoi-quy-tai-hoc-vien', 'Tuyệt Thế Sát Thủ Hồi Quy Tại Học Viện', 'https://img.otruyenapi.com/uploads/comics/tuyet-the-sat-thu-hoi-quy-tai-hoc-vien-thumb.jpg', 4420, '2026-01-09 12:16:45'),
('u-minh-nguy-tuong', 'U Minh Ngụy tượng', 'https://img.otruyenapi.com/uploads/comics/u-minh-nguy-tuong-thumb.jpg', 3090, '2026-01-10 11:31:57'),
('vo-dich-chi-voi-1-mau', 'Vô Địch Chỉ Với 1 Máu', 'https://img.otruyenapi.com/uploads/comics/vo-dich-chi-voi-1-mau-thumb.jpg', 3711, '2026-01-09 13:34:03'),
('vo-dich-thien-ha-chuyen-the-dau-quan-phe-dich', 'Vô Địch Thiên Hạ, Chuyển Thế Đầu Quân Phe Địch', 'https://img.otruyenapi.com/uploads/comics/vo-dich-thien-ha-chuyen-the-dau-quan-phe-dich-thumb.jpg', 1142, '2026-01-10 13:08:53'),
('volundio-su-thi-ve-mong-kiem', 'Volundio: Sử Thi Về Mộng Kiếm', 'https://img.otruyenapi.com/uploads/comics/volundio-su-thi-ve-mong-kiem-thumb.jpg', 3384, '2026-04-18 11:19:19'),
('vua-choi-da-co-tai-khoan-vuong-gia', 'Vừa Chơi Đã Có Tài Khoản Vương Giả', 'https://img.otruyenapi.com/uploads/comics/vua-choi-da-co-tai-khoan-vuong-gia-thumb.jpg', 2244, '2026-04-18 16:05:05'),
('vua-hiep-si-da-tro-lai-voi-mot-vi-than', 'Vua Hiệp Sĩ Đã Trở Lại Với Một Vị Thần', 'https://img.otruyenapi.com/uploads/comics/vua-hiep-si-da-tro-lai-voi-mot-vi-than-thumb.jpg', 4456, '2026-01-10 13:29:27'),
('vua-vo-dich-tai-mat-the-da-bi-chan-cua-cau-hon', 'Vừa Vô Địch Tại Mạt Thế Đã Bị Chặn Cửa Cầu Hôn', 'https://img.otruyenapi.com/uploads/comics/vua-vo-dich-tai-mat-the-da-bi-chan-cua-cau-hon-thumb.jpg', 1440, '2026-01-10 04:54:55'),
('win-over-the-dragon-emperor-this-time-around-noble-girl', 'Win Over the Dragon Emperor This Time Around Noble Girl!', 'https://img.otruyenapi.com/uploads/comics/win-over-the-dragon-emperor-this-time-around-noble-girl-thumb.jpg', 4575, '2026-01-02 14:39:54'),
('wind-breaker', 'Wind Breaker', 'https://img.otruyenapi.com/uploads/comics/wind-breaker-thumb.jpg', 3568, '2026-01-09 12:21:25'),
('world-embryo-remastered', 'World Embryo Remastered', 'https://img.otruyenapi.com/uploads/comics/world-embryo-remastered-thumb.jpg', 2540, '2026-04-18 12:08:46'),
('xich-long-chi-tu', 'Xích Long Chi Tử', 'https://img.otruyenapi.com/uploads/comics/xich-long-chi-tu-thumb.jpg', 2439, '2026-01-09 12:20:52'),
('xuyen-khong-toi-tu-tien-gioi-lam-tru-than', NULL, NULL, 2658, '2026-01-02 13:37:01'),
('yeu-than-ky', 'Yêu Thần Ký', 'https://img.otruyenapi.com/uploads/comics/yeu-than-ky-thumb.jpg', 3173, '2026-01-10 03:50:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `novel_id` int(11) DEFAULT NULL,
  `comic_slug` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `like_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `novel_id`, `comic_slug`, `content`, `parent_id`, `like_count`, `created_at`) VALUES
(2, 1, 1, NULL, 'yeah', 1, 0, '2026-01-01 03:37:07'),
(3, 1, NULL, 'huyen-thoai-game-thu-tai-xuat', 'test nào', 0, 0, '2026-01-01 09:48:20'),
(4, 1, NULL, 'huyen-thoai-game-thu-tai-xuat', 'test cũng oke', 3, 0, '2026-01-01 09:48:26'),
(5, 1, NULL, 'khong-on-roi-ta-dot-nhien-vo-dich', 'test', 0, 0, '2026-01-01 09:57:43'),
(6, 1, 1, NULL, 'test', 0, 0, '2026-01-02 13:50:09'),
(7, 3, 1, NULL, 'Thằng tác giả ngu vcl', 0, 0, '2026-01-02 13:52:52'),
(9, 3, 1, NULL, 'có cái cc', 7, 0, '2026-04-18 13:26:50'),
(10, 1, 1, NULL, 'lmao', 9, 0, '2026-04-18 13:28:02'),
(11, 1, 1, NULL, 'lmao', 9, 0, '2026-04-18 13:28:10'),
(12, 3, NULL, 'thuc-quy-mao-hiem-gia', 'x', 0, 0, '2026-04-18 13:47:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comment_likes`
--

CREATE TABLE `comment_likes` (
  `user_id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_categories`
--

CREATE TABLE `forum_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `forum_categories`
--

INSERT INTO `forum_categories` (`id`, `name`, `slug`) VALUES
(1, 'Thảo luận', 'thao-luan'),
(2, 'Góp ý', 'gop-y'),
(3, 'Báo lỗi', 'bao-loi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `like_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `topic_id`, `user_id`, `content`, `like_count`, `created_at`) VALUES
(2, 1, 3, 'lmao', 0, '2026-04-18 12:07:44'),
(3, 1, 1, 'hahahah', 0, '2026-04-18 12:07:54'),
(4, 1, 1, 'aaa', 1, '2026-04-18 13:15:03'),
(5, 1, 3, 'fdfdf', 0, '2026-04-18 14:23:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_post_likes`
--

CREATE TABLE `forum_post_likes` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `forum_post_likes`
--

INSERT INTO `forum_post_likes` (`user_id`, `post_id`, `created_at`) VALUES
(3, 4, '2026-04-18 14:22:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `forum_topics`
--

INSERT INTO `forum_topics` (`id`, `category_id`, `user_id`, `title`, `content`, `views`, `created_at`) VALUES
(1, 1, 3, 'Truyện này rác vãi vl', 'Như cap', 63, '2026-04-18 11:35:39'),
(2, 3, 3, 'adad', 'adad', 2, '2026-04-19 10:00:08'),
(3, 2, 3, 'adsad', 'adad', 2, '2026-04-19 10:00:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL COMMENT 'NULL là Hệ thống, có ID là người gửi',
  `receiver_id` int(11) NOT NULL COMMENT 'Người nhận',
  `type` enum('system','report','reply') DEFAULT 'system' COMMENT 'Loại thông báo',
  `target_url` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `sender_id`, `receiver_id`, `type`, `target_url`, `title`, `message`, `is_read`, `created_at`) VALUES
(2, NULL, 2, 'system', NULL, 'Chúc mừng', 'test', 1, '2026-01-08 00:16:13'),
(6, NULL, 2, 'system', NULL, 'test', '2', 1, '2026-01-08 00:19:12'),
(14, NULL, 2, 'system', NULL, 'chúc mừng năm mới', 'oke nhé e', 0, '2026-01-09 13:51:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `novels`
--

CREATE TABLE `novels` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` enum('ongoing','completed','dropped') DEFAULT 'ongoing',
  `posted_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `view_count` int(11) DEFAULT 0,
  `favorite_count` int(11) DEFAULT 0,
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `novels`
--

INSERT INTO `novels` (`id`, `title`, `slug`, `description`, `author`, `cover_image`, `status`, `posted_by`, `created_at`, `updated_at`, `view_count`, `favorite_count`, `views`) VALUES
(1, 'Đấu phá thương khung', 'dau-pha-thuong-khung', 'Tiêu Viêm, một thiên chi kiêu tử với thiên phú tu luyện mà ai ai cũng hâm mộ, bỗng một ngày người mẹ mất đi đễ lại di vật là một chiếc giới chỉ màu đen nhưng từ khi đó Tiêu Viêm đã mất đi thiên phú tu luyện của mình.\r\n\r\n- Từ thiên tài rớt xuống làm phế vật trong 3 năm, rồi bị vị hôn thê thẳng thừng từ hôn, làm dấy lên ý chí nam nhi của mình, Tiêu Viêm nhờ di vật của mẫu thân để lại là 1 chiếc hắc giới chỉ (nhẫn màu đen)Tiêu Viêm gặp được hồn của Dược Lão (Dược Trần – Dược tôn giả) 1 đại luyện dược tông sư của đấu khí đại lục…\r\n\r\n- Từ đó cuộc đời của Tiêu Viêm có những biến hóa gì? Gặp được các đại ngộ gì? Thân phận thật sự của Huân Nhi (thanh mai trúc mã lúc nhỏ của Tiêu Viêm) ra sao? Bí mật của gia tộc hắn là gì? Cùng theo dõi bộ truyện Đấu Phá Thương Khung để có thể giải đáp các thắc mắc này các bạn nhé!', 'Thổ đậu', 'https://nhanvat.wiki/wp-content/uploads/2023/01/dau-pha-thuong-khung.jpg', 'ongoing', 1, '2025-12-29 13:57:52', '2026-04-18 16:19:08', 73, 1, 92);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `novel_categories`
--

CREATE TABLE `novel_categories` (
  `novel_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `novel_categories`
--

INSERT INTO `novel_categories` (`novel_id`, `category_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `novel_chapters`
--

CREATE TABLE `novel_chapters` (
  `id` int(11) NOT NULL,
  `novel_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `order_index` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `novel_chapters`
--

INSERT INTO `novel_chapters` (`id`, `novel_id`, `title`, `slug`, `content`, `order_index`, `created_at`) VALUES
(1, 1, 'Chương 1: Thiên tài rơi rụng', 'chuong-1-thien-tai-roi-rung', '\"Đấu lực, ba đoạn\"\r\n\r\nNhìn năm chữ to lớn có chút chói mắt trên trắc nghiệm ma thạch, thiếu niên mặt không chút thay đổi, thần sắc tự giễu, nắm chặt tay, bởi vì dùng lực quá mạnh làm móng tay đâm thật sâu vào trong lòng bàn tay, mang đến từng trận trận đau đớn trong tâm hồn...\r\n\r\n\"Tiêu Viêm, đấu lực, ba đoạn! Cấp bậc: Cấp thấp!\".\r\n\r\nBên cạnh trắc nghiệm ma thạch, một vị trung niên nam tử, thoáng nhìn tin tức trên bia, ngữ khí hờ hững công bố…\r\n\r\nTrung niên nam tử vừa nói xong, không có gì ngoài ý muốn, đám người trên quảng trường lại nổi lên trận trận châm chọc tao động\r\n\r\n\"Ba đoạn? Hắc hắc, quả nhiên không ngoài dự đoán của ta, \"\"Thiên tài\" này một năm rồi vẫn dậm chân tại chỗ a!\"\r\n\r\n\"Ai, phế vật này thật sự làm mất hết cả mặt mũi gia tộc.\"\r\n\r\n\"Nếu tộc trưởng không phải phụ thân của hắn. Loại phế vật này sớm đã bị đuổi khỏi gia tộc, tự sinh tự diệt rồi, làm gì còn có cơ hội ở gia tộc ăn không uống không.\"\r\n\r\n\"Ai..., thiên tài thiếu niên năm đó của Văn Ô Thản thành, tại sao hôm nay lại lạc phách thành bộ dáng này cơ chứ?\"\r\n\r\n\"Ai mà biết được? Có lẽ do làm việc gì đó trái với lương tâm, làm thần linh nổi giận đó mà…\"\r\n\r\nChung quanh truyền đến cười nhạo cùng thanh âm tiếc hận, dừng ở trong tai của thiếu niên, tựa như một chiếc dao nhọn hung hăng đâm vào tim hắn, khiến hô hấp của thiếu niên trở nên có chút dồn dập.\r\n\r\nThiếu niên chậm rãi ngẩng đầu, lộ ra khuôn mặt thanh tú non nớt, con ngươi đen nhánh nhẹ nhàng đảo qua đám bạn cùng lứa tuổi đang trào phúng chung quanh, khóe miệng thiếu niên tự giễu, tựa hồ trở nên càng thêm chua xót.\r\n\r\n\"Những người này, đều thừa hơi như vậy sao? Có lẽ vì ba năm trước bọn họ từng trước mặt mình lộ ra bộ mặt tươi cười nhún nhường, cho nên hiện tại muốn đòi trở về đây mà…\" Mỉm cười chua xót, Tiêu Viêm chán nản xoay người, im lặng đi tới cuối hàng, thân ảnh cô đơn cùng thế giới xung quanh trở nên có chút lạc lõng.\r\n\r\n\"Người tiếp theo, Tiêu Mị\"\r\n\r\nNghe người tiến hành trắc nghiệm gọi tên, một thiếu nữ rất nhanh từ trong đám người đi ra, tiếng nghị luận ở xung quanh trở nên nhỏ đi rất nhiều, từng đạo ánh mắt nóng bỏng tập trung lên trên khuôn mặt của thiếu nữ…\r\n\r\nThiếu nữ tuổi không quá mười bốn, dù chưa thể coi là tuyệt sắc, nhưng khuôn mặt non nớt kia cũng ẩn chứa trong đó một tia vũ mị nhàn nhạt, thanh thuần cùng vũ mị, một tập hợp mâu thuẫn, càng khiến nàng trở thành tiêu điểm của toàn trường…\r\n\r\nThiếu nữ nhanh chóng đi lên, tay vuốt ve ma thạch bi quen thuộc, sau đó chậm rãi nhắm mắt…\r\n\r\nTại lúc thiếu nữ nhắm mắt, ma thạch bi đen nhánh lại hiện lên quang mang…\r\n\r\n\"Đấu khí: Bảy đoạn!\"\r\n\r\n\"Tiêu Mị, Đấu khí: Bảy đoạn! Cấp bậc: Cao cấp\"\r\n\r\n\"Da!\" Nghe trắc ngiệm viên đọc lên thành tích, thiếu nữ ngẩng mặt lên đắc ý cười…\r\n\r\n\"Sách sách, bảy đoạn đấu khí, cứ theo tiến độ như vậy, chỉ sợ không quá ba năm thời gian, nàng có thể trở thành một đấu giả chính thức rồi…\"\r\n\r\n\"Không hổ là hạt giống của gia tộc a…\"\r\n\r\nNghe đám người truyền đến trận trận thanh âm hâm mộ, thiếu nữ tươi cười lại rạng rỡ thêm vài phần, tâm hư vinh, là thứ mà rất nhiều cô gái đều không thể kháng cự…\r\n\r\nNhớ đến ngày thường hay cùng mấy tỷ muội đàm tiếu, tầm mắt Tiêu Mị bỗng nhiên xuyên qua đám người, dừng trên một đạo thân ảnh cô đơn…\r\n\r\nNhíu mày suy nghĩ một chút, Tiêu Mị vứt bỏ ý niệm trong đầu, hiện tai hai người đã không còn cùng một giai tầng, lấy biểu hiện của Tiêu Viêm mấy năm này, sau khi trưởng thành, nhiều nhất cũng chỉ có thể làm nhân viên hạ tầng của gia tộc mà thôi, mà thiên phú vĩ đại như nàng, sẽ trở thành trọng điểm bồi dưỡng của gia tộc, có thể nói là tiền đồ không thể hạn lượng.\r\n\r\n\"Ai…\" Khẽ thở dài một tiếng, trong đầu Tiêu Mị bỗng hiện ra hình ảnh một thiếu niên ý khí phong phát ba năm trước đây, bốn tuổi luyện khí, mười tuổi có chín đoạn đấu khí, mười một tuổi đột phá mười đoạn đấu khí, ngưng tụ thành công đấu khí toàn, trở thành đấu giả trẻ nhất trong vòng trăm năm của gia tộc!\r\n\r\nThiếu niên trước kia, bộ dáng tự tin lại thêm tiềm lực không thể hạn lượng, không biết đã làm bao cô gái động xuân tâm, đương nhiên trong đó cũng có cả Tiêu Mị.\r\n\r\nNhưng con đường của thiên tài, từ trước đến giờ luôn luôn trắc trở, ba năm trước, khi danh vọng của thiếu niên thiên tài đạt tới đỉnh cao nhất, cũng là lúc đột ngột phải thừa nhận đả kích tàn khốc nhất, không chỉ có vừa vất vả khổ tu ngưng tụ đấu khí toàn trong một đêm biến mất, mà đấu khí theo thời gian trôi qua lại càng trở nên càng ngày càng ít đi một cách quỷ dị.\r\n\r\nKết quả của đấu khí biến mất, đó chính là thực lực không ngừng giảm đi.\r\n\r\nTừ thiên tài, một đêm trở thành một thứ mà ngay cả người bình thường cũng không bằng, loại đả kích này, khiến thiếu niên từ đó thất hồn lạc phách, cái tên thiên tài, cũng dần dần bị khinh thường cùng châm chọc thay thế.\r\n\r\nTrèo càng cao, ngã càng đau, lần ngã này có lẽ sẽ không còn cơ hội đứng dậy nữa.\r\n\r\n\"Người tiếp theo, Tiêu Huân Nhi!\"\r\n\r\nTrong âm thanh huyên náo của đám người, thanh âm của trắc nghiệm viên lại vang lên.\r\n\r\nTheo đó là một cái tên thanh nhã vang lên, đám người bỗng trở nên im lặng, ánh mắt đều dịch chuyển.\r\n\r\nTại nơi ánh mắt tụ hội, một thiếu nữ áo tím đang đạm nhã đứng đó, khuôn mặt non nớt bình tĩnh, không vì bị mọi người chú ý mà thay đổi chút nào.\r\n\r\nThiếu nữ khí chất lãnh đạm tựa như đóa sen mới nở, tuổi nhỏ đã có khí chất thoát tục, khó có thể tưởng tượng sau này lớn lên, thiếu nữ này sẽ khuynh quốc khuynh thành đến mức độ nào…\r\n\r\nTử y thiếu nữ này, nói về mỹ mạo cùng khí chất, so với Tiêu Mị trước đó lại càng hơn vài phần, khó trách mọi người đều có động tác như vậy.\r\n\r\nKhẽ bước tới, thiếu nữ tên Tiêu Huân Nhi đi tới phía trước ma thạch bi, bàn tay nhỏ bé đưa lên, ống tay áo theo đó mà chảy xuống, lộ ra da thịt trắng nõn nà, sau đó đặt nhẹ tay lên bia đá…\r\n\r\nSau một khoảng trầm tĩnh, trên thạch bia hiện lên ánh sáng chói mắt.\r\n\r\n\"Đấu khí: Chín đoạn! Cấp bậc: Cao cấp!\"\r\n\r\nNhìn mấy chữ trên thạch bia, giữa sân trở nên tĩnh lặng.\r\n\r\n\"…Đã tới chín đoạn rồi, thật là khủng bố mà! Người đứng đầu trong giới trẻ của gia tộc, chỉ sợ không ai ngoài Huân Nhi tiểu thư a.\" Yên tĩnh qua đi, các thiếu niên xung quanh đều không tự chủ được nuốt một ngụm nước miếng, ánh mắt tràn ngập kính sợ…\r\n\r\nĐấu khí, con đường bắt buộc phải đi qua của mỗi đấu giả, sơ giai đấu khí chia từ một đến mười đoạn, đấu khí trong cơ thể đạt tới mười đoạn, là có thể ngưng tụ đấu khí toàn, trở thành một đấu giả được người khác tôn trọng.\r\n\r\nTrong đám người, Tiêu Mị nhíu mày nhìn cô gái áo tím đứng trước bia đá, trên mặt hiện lên một tia ghen tị…\r\n\r\nNhìn tin tức trên thạch bia, khuôn mặt hờ hững của trung niên trắc nghiệm viên bên cạnh cũng lộ ra một tia mỉm cười hiếm hoi, đối với cô gái thoáng dùng âm thanh cung kính nói: \"Huân Nhi tiểu thư, nửa năm sau, tiểu thư hẳn sẽ có thể ngưng tụ đấu khí toàn, nếu thành công, mười bốn tuổi trở thành một đấu giả chân chính, tiểu thư sẽ là người thứ hai của Tiêu gia trong trăm năm nay!\"\r\n\r\nĐúng vậy, người thứ hai, người thứ nhất đó chính là thiên tài đã mất đi ánh hào quang - Tiêu Viêm.\r\n\r\n\"Cám ơn.\" Thiếu nữ khẽ gật đầu, khuôn mặt bình thản không vì được hắn khích lệ mà vui sướиɠ, im lặng xoay người, dưới ánh mắt nóng bỏng của mọi người, chậm rãi đi đến cuối đám người, tới trước mặt thiếu niên đang suy sụp…\r\n\r\n\"Tiêu Viêm ca ca.\" Tại lúc đến bên cạnh thiếu niên, thiếu nữ dừng chân, đối với Tiêu Viêm cung kính cúi người, trên khuôn mặt xinh đẹp, cư nhiên lộ ra nụ cười thanh nhã khiến các cô gái chung quanh cũng phải trở nên ghen tị.\r\n\r\n\"Huynh bây giờ còn có tư cách để muội gọi như vậy sao?\" Nhìn trước mặt đã trở thành khỏa minh châu sáng nhất trong gia tộc kia, Tiêu Viêm chua xót nói, sau khi bản thân hắn tụt dốc nàng chính là một trong số cực ít những người vẫn bảo trì tôn kính đối với hắn.\r\n\r\n\"Tiêu Viêm ca ca, trước kia huynh đã từng nói với Huân Nhi, có thể buông, mới có thể cầm lấy, thu phóng tự nhiên mới là người tự tại!\" Tiêu Huân Nhi mỉm cười ôn nhu nói, giọng nói non nớt, khiến người tâm đã chết cũng cảm thấy ấm lòng.\r\n\r\n\"Ha, ha, người tự tại sao? Huynh cũng chỉ biết nói mà thôi, muội xem bộ dáng hiện tại của huynh đi, giống một người tự tại sao? Hơn nữa… thế giới này, cơ bản cũng không phải là thế giới của huynh.\" Tiêu Viêm cười tự giễu nói.\r\n\r\nĐối với sự suy sụp của Tiêu Viêm, Tiêu Huân Nhi khẽ cau mày, thật lòng nói: \"Tiêu Viêm ca ca, tuy muội cũng không biết huynh vì sao lại bị như vậy, bất quá, Huân Nhi tin tưởng, huynh sẽ lại đứng dậy, lấy lại vinh quang và tôn nghiêm của huynh…\" Kết thúc câu nói, khuôn mặt trắng nõn của thiếu nữ lần đầu tiên hiện lên nét ửng đỏ nhàn nhạt: \"Tiêu viêm ca ca năm đó, thực ra rất hấp dẫn…\"\r\n\r\n\"A a…\" Đối với lời nói thẳng thắn của thiếu nữ, thiếu niên xấu hổ cười một tiếng, nhưng lại không nói gì, người không phong lưu phí hoài tuổi trẻ, nhưng hắn hiện tại thực sự đã không còn tư cách cùng tâm tình đó nữa, yên lặng xoay người, chậm rãi đi ra khỏi quảng trường…\r\n\r\nĐứng tại chỗ nhìn theo bóng lưng cô độc của thiếu niên, Tiêu Huân Nhi trù trừ một thoáng, sau đó bỏ lại tiêng sói tru tiếng ghen tị tại phía sau, bước nhanh theo, cùng thiếu niên sóng vai bước đi…', 1, '2025-12-29 14:15:59'),
(2, 1, 'Chương 2: Đấu khí đại lục', 'chuong-2-dau-khi-dai-luc', 'Trăng sáng vằng vặc, bầu trời đầy sao.\r\n\r\nTrên đỉnh núi, Tiêu Viêm nằm trên mặt cỏ, trong miệng ngậm một nhánh cỏ xanh, khẽ nhấm nháp, tùy ý để tư vị chua chát tràn ngập trong miệng…\r\n\r\nGiơ lên bàn tay trắng nõn lên trước mặt, ánh mắt xuyên thấu qua khe hở giữa các ngón tay, nhìn vầng trăng bạc trên bầu trời xa xăm.\r\n\r\n\"Ai…\" Nhớ đến trắc nghiệm lúc buổi chiều, Tiêu Viêm thở dài một tiếng, miễn cưỡng co tay lại, hai tay đỡ lấy đầu, ánh mắt có chút hoảng hốt.\r\n\r\n\"Mười lăm năm rồi…\" Âm thanh nho nhỏ bỗng nhiên không hề báo trước từ trong miệng thiếu niên xuất ra.\r\n\r\nTrong lòng Tiêu Viêm, có một cái bí mật chỉ mình hắn biết: Hắn không phải người của thế giới này, hoặc có thể nói là, linh hồn của Tiêu Viêm không thuộc về thế giới này, hắn đến từ một nơi tên là địa cầu, tuy nhiên vì sao hắn lại đến được nơi này hắn cũng không thể giải thích được, sống qua một khoảng thời gian, hắn hiểu ra được: Hắn đã xuyên việt!\r\n\r\n( Theo ý hiểu của mềnh \"xuyên việt\" tức là vượt qua chiều không gian khác))\r\n\r\nTheo tuổi dần dần tăng lên, đối với đại lục này, Tiêu Viêm cũng đã có chút lý giải mơ hồ về nó…\r\n\r\nĐại lục tên là Đấu Khí đại lục, trên đại lục không có các loại ma pháp như trong tiểu thuyết, mà đấu khí mới là chủ nhân duy nhất của đại lục!\r\n\r\nTại đại lục này, tu luyện đấu khí, cơ hồ dưới sự cố gắng của vô số thế hệ đã phát triển đến mức đỉnh cao, hơn nữa nhờ phạm vi đấu khí không ngừng mở rộng, cuối cùng đã phát triển vào dân gian, việc này khiến cho đấu khí cùng nhân loại càng trở nên quen thuộc, do đó, tầm quan trọng của đấu khí trong đại lục này là không gì có thể thể thay thế.\r\n\r\nTrải qua thống kê, đấu khí đại lục đem đấu khi chia ra các cấp bậc từ thấp đến cao, chia làm bốn giai mười hai cấp: Thiên, Địa, Huyền, Hoàng!\r\n\r\nMà mỗi đẳng cấp lại phân làm sơ, trung, cao - ba cấp.\r\n\r\nTu luyện đấu khí cấp bậc cao hay thấp cũng là mấu chốt quyết định thành tựu sau này, ví dụ như người tu luyện công pháp trung cấp Huyền giai tất nhiên sẽ mạnh hơn người cùng cấp bậc tu luyện công pháp trung cấp Hoàng giai.\r\n\r\nĐấu khí đại lục, phân biệt mạnh yếu, quyết định qua ba điều kiện. Bạn đang đọc truyện tại TruyenHD - www.Truyện FULL\r\n\r\nĐầu tiên, trọng yếu nhất đương nhiên là tự thân thực lực, nếu thực lực chỉ có nhất tinh đấu giả thì dù có luyện công pháp Thiên giai tuyệt thế cũng không thể chiến thắng một người tu luyện công pháp Hoàng giai đấu sư.\r\n\r\nTiếp theo, đó là công pháp! Đồng cấp bậc cường giả, nếu công pháp của ngươi cao hơn đối phương, như vậy tại lúc tỷ thí sẽ có ưu thế rất lớn.\r\n\r\nLoại cuối cùng, gọi là đấu kĩ!\r\n\r\nTên như ý nghĩa, đây là một lại kỹ năng phát huy đấu khí đặc thù, đấu khí trên đại lục cũng có cấp bậc phân biệt, cũng chia làm Thiên, Địa, Huyền, Hoàng bốn cấp.\r\n\r\nĐấu khí đại lục số lượng đấu kĩ không hề ít, bình thường đấu kĩ truyền lưu ra ngoài, phần lớn đều là hoàng cấp, muốn đạt được đấu kĩ cao thâm thì phải gia nhập tông phái hoặc đấu khí học viện trên đại lục.\r\n\r\nĐương nhiên, một số người do kỳ ngộ hoặc được tiền nhân để lại công pháp, hoặc có đấu kĩ tương xứng với chính bản thân mình, thì loại đấu kĩ đó phối hợp với công pháp khiến uy lực của nó càng mạnh hơn.\r\n\r\nDựa vào ba điều kiện này có thể phán đoán mạnh yếu, tổng thể mà nói nếu có thể có được cấp bậc công pháp càng cao, sau này phát triển càng không cần phải nói…\r\n\r\nBất quá công pháp tu luyện cao cấp đấu khí người thường đều rất khó tìm được, công pháp truyền lưu tại tầng lớp phổ thông, nhiều nhất cũng chỉ là Hoàng giai công pháp, một số gia tộc cường đại hoặc trung, tiểu tông phái mới có phương pháp tu luyện Huyền giai, ví dụ như gia tộc của Tiêu Viêm, công pháp cao nhất chỉ có tộc trưởng mới có tư cách tu luyện. Cuồng sư nộ cương, là một loại phong thuộc tính công pháp đấu khí trung cấp Huyền giai.\r\n\r\nTrên Huyền giai, đó là Địa giai, loại công pháp cao thâm này chỉ có thế lực siêu nhiên hoặc đại đế quốc mới có thể có…\r\n\r\nCòn Thiên giai… thì đã mấy trăm năm chưa từng thấy xuất hiện.\r\n\r\nTheo lý luận, người bình thường muốn có được công pháp cao cấp, cơ bản đều là khó như lên trời, nhưng mọi sự đều không có tuyệt đối, theo đấu khí đại lục mở rộng ra, vạn tộc tràn vào an cư lập nghiệp, phía bắc đại lục, có man tộc được xưng là lực lượng mạnh mẽ vô cùng, có thể cùng thú hồn hợp thể. Phía nam đại lục, cũng có các loại gia tộc ma thú cao cấp có trí tuệ, hay chủng tộc hắc ám âm ngoan quỷ dị…\r\n\r\nBởi vì địa vực mở ra, nên có rất nhiều ẩn sĩ vô danh, tại lúc sinh mệnh kết thúc, có lẽ sẽ đem công pháp do mình sáng tạo để ở một nơi, chờ đợi người có duyên đến lấy. Tại đấu khí đại lục truyền lưu một câu nói: Nếu có một ngày, ngươi rơi xuống một cái sơn động, không cần kinh hoảng, đi về phía trước hai bước, có lẽ nó sẽ giúp ngươi trở thành cường giả của thế giới này!\r\n\r\nNói như vậy, cũng không phải là giả, trong ngàn năm lịch sử của đại lục, cũng không thiếu loại cố sự nhờ những kì ngộ này mà trở thành cường giả.\r\n\r\nMà cố sự tạo thành hiệu quả là rất nhiều người tìm đến các vách núi hòng đi tìm tuyệt thế công pháp. Đương nhiên, những người này phần lớn đều là gẫy tay gẫy chân mà về…\r\n\r\nTóm lại, đây là một mảnh đất tràn ngập kỳ tích và người sáng tạo nên kỳ tích.\r\n\r\nĐương nhiên, muốn tu luyện bí tịch đấu khí, ít nhất phải trở thành đấu giả chân chính mới có tư cách này, mà Tiêu Viêm thì cách mục tiêu đó còn rất xa…\r\n\r\n\"Phì.\" Nhổ ra cây cỏ trong miệng, Tiêu Viêm đột nhiên nhảy dựng lên, khuôn mặt dữ tợn, đối với bầu trời thất rít gào lên: \"Ta đệt cơm mịa mài, đem lão tử xuyên qua thời không sang đây làm một cái phế vật sao? Kháo!\" ( để là \"thảo\" thì không có ý nghĩa gì trong câu này cả... vốn từ có hạn quá không biết bên tàu dùng cái chữ này với nghĩa là gì - đại khái là chửi hay thốt lên bất mãn í mà, \"ta thảo\" chắc cũng gần giống câu \"ta ngất\" ó \"ta kháo\" chứ ứng với nghĩa là cây cỏ thì... bó tay quá.)\r\n\r\nỞ kiếp trước, Tiêu Viêm là một người cực kỳ bình phàm, tiền tài gái đẹp so với hắn căn bản là hai cái đường thẳng song song, vĩnh viễn không có điểm gặp nhau, nhưng sau khi đến đấu khí đại lục, Tiêu Viêm kinh hỉ phát hiện, nhờ có kinh nghiệm hai đời, linh hồn của hắn so với người bình thường mạnh hơn rất nhiều!\r\n\r\nPhải biết rằng, tại đấu khí đại lục, linh hồn là do trời sinh, có thể theo tuổi lớn lên mà mạnh hơn một chút, nhưng chưa có công pháp nào có thể đơn độc tu luyện linh hồn, cho dù là thiên giai công pháp cũng không thể! Đây là kiến thức cơ bản nhất tại đấu khí đại lục.\r\n\r\nLinh hồn cường hóa, cũng tạo nên thiên phú tu luyện cho Tiêu Viêm, đồng thời cũng tạo nên danh tiếng thiên tài cho hắn\r\n\r\nKhi một người bình thường biết mình có tiền vốn để trờ thành tiêu điểm cho ánh mắt của vô số người, nếu không có đủ định lực, rất khó có thể giữ vững tâm tính của mình, rất hiển nhiên, kiếp trước Tiêu Viêm là một người bình thường, cũng không có định lực cao hơn người khác, cho nên lúc hắn bắt đầu tu luyện đấu khí, hắn lựa chọn làm thiên tài, tiêu điểm trước con mắt của mọi người mà không phải là phát triển trong an tĩnh.\r\n\r\nNếu không có việc gì ngoài ý muốn, Tiêu Viêm thực sự có thể đem hai chữ thiên tài càng lúc càng vang xa, đáng tiếc, tại năm hắn mười một tuổi, danh tiếng thiên tài đột nhiên bị biến cố cướp đoạt đi, mà thiên tài cũng trong một đêm trở thành phế vật cho mọi người chế nhạo!\r\n\r\n……\r\n\r\nSau khi hét lên vài tiếng, cảm xúc của Tiêu Viêm chậm rãi bình ổn dần, khuôn mặt lại hồi phục bộ dáng yên lặng như ngày thường, mọi việc đã đến nước này, hắn có nổi giận thế nào đi nữa cũng không thể vãn hồi đấu khí toàn mà trước đó hắn phải vất vả tu luyện.\r\n\r\nChua xót lắc đầu, trong lòng Tiêu Viêm kì thật có một tia ủy khuất, cơ bản là hắn đối với thân thể của chính mình xảy ra chuyện gì cũng không hề biết, ngày thường kiểm tra cũng không phát hiện ra không đúng ở chỗ nào, linh hồn theo tuổi gia tăng cũng càng ngày càng cường đại, hơn nữa tốc độ hấp thu đấu khí so với trạng thái đỉnh cao của vài năm trước còn mạnh mẽ hơn vài phần, những thứ đó này đều nói rõ lên rằng thiên phú của mình không hề suy giảm, nhưng đấu khí tiến vào cơ thể đều biến mất toàn bộ không chút ngoại lệ, tình hình quỷ dị khiến tinh thần Tiêu Viêm ảm đạm bi thương…\r\n\r\nẢm đạm thở dài, Tiêu Viêm nâng tay lên, trên ngón tay có một chiếc nhẫn màu đen, chiếc nhẫn rất cổ xưa, không biết do tài liệu nào làm ra, bên trên còn có đường hoa văn mờ nhạt, đây là lễ vật duy nhất mà mẫu thân trước khi chết đưa cho hắn, bắt đầu từ lúc bốn tuổi đến nay, hắn đã đeo nó mười năm, di vật của mẫu thân làm Tiêu Viêm đối với nó có một phần quyến luyến, ngón tay nhẹ nhàng vuốt ve giới chỉ, Tiêu Viêm cười khổ nói: \"Máy năm nay, thật sự đã phụ lòng kỳ vọng của mẫu thân rồi…\"\r\n\r\nThở dài một tiếng, Tiêu Viêm bỗng nhiên quay đầu, đối với rừng cây đen nhánh ấm áp cười nói: \"Phụ thân, ngài tới rồi ạ?\"\r\n\r\nTuy đấu khí chỉ có tam đoạn, bất quá linh hồn cảm giác của Tiêu Viêm so sánh với một ngũ tinh đấu giả còn mẫn tuệ hơn nhiều, ngay trong lúc nhắc đến mẫu thân, hắn đã phát hiện ra trong rừng cây có động tĩnh.\r\n\r\n\"Ha ha, Viêm nhi, muộn thế này rồi, tại sao còn ở trên này?\" Trong rừng cây, sau một thoáng im lặng truyền ra tiếng cười quan tâm của nam tử.\r\n\r\nCành cây lay động một chút, một vị trung niên nam tử bước ra, khuôn mặt mang theo nụ cười, dừng ở chỗ đứa con của mình đang đứng dưới ánh trăng.\r\n\r\nTrung niên nhân mặc một bộ y sam họa lệ màu xám, long hành hổ bộ rất có uy nghiêm, trên mặt một đôi lông mày thô dày lại tăng thêm vài phần hào khí, đó là Tiêu Gia đương nhiệm tộc trưởng đồng thời là phụ thân của Tiêu Viêm, ngũ tinh đại đấu sư Tiêu Chiến!\r\n\r\n\"Phụ thân, chẳng phải ngài cũng chưa nghỉ ngơi sao?\" Nhìn trung niên nam tử, nụ cười trên mặt Tiêu Viêm càng đậm, tuy mình có trí nhớ kiếp trước, bất quá từ lúc hắn sinh ra đến nay, vị phụ thân trước mặt này đối với hắn cực kỳ sủng ái, sau khi hắn suy sụp càng không giảm mà tăng, như vậy cũng đủ làm cho Tiêu Viêm cam tâm gọi hắn một tiếng phụ thân rồi.\r\n\r\n\"Viêm nhi, còn nghĩ tới việc trắc nghiệm buổi chiều sao?\" Bước nhanh tiến lên, Tiêu Chiến cười nói.\r\n\r\n\"Ha ha, có gì để nghĩ đâu, tất cả đều trong dự kiến rồi mà\". Thiếu niên lắc lắc đầu, nụ cười cũng có chút miễn cưỡng.\r\n\r\n\"Ai…\" Nhìn khuôn mặt có chút non nớt của Tiêu Viêm, Tiêu Chiến hít một hơi, trầm mặc một lúc, bỗng nhiên nói: \"Viêm nhi, năm nay ngươi mười lăm tuổi rồi đúng không?\"\r\n\r\n\"Vâng, phụ thân\"\r\n\r\n\"Qua một năm nữa, tựa hồ… phải tiến hành nghi thức trưởng thành rồi…\" Tiêu Chiến cười khổ nói.\r\n\r\n\"Đúng vậy, phụ thân, còn có một năm nữa thôi!\" Bàn tay có chút siết chặt, Tiêu Viêm bình tĩnh trả lời, nghi thức trưởng thành đại biểu cho cái gì hắn tự nhiên hiểu rõ, chỉ cần qua trưởng thành nghi thức, hắn không có tiềm lực tu luyện, tự nhiên sẽ bị hủy bỏ tư cách tiến vào Đấu Khí Các tiến hành tìm kiếm công pháp đấu khí, sau đó là bị phân đến các nơi có sản nghiệp của gia tộc, làm một số công việc bình thường, đây là tộc quy của gia tộc, cho dù phụ thân hắn cũng không thể thay đổi.\r\n\r\nMà, nếu trước hai mươi lăm tuổi vẫn chưa trở thành một đấu giả thì gia tộc cũng sẽ ghi nhận nữa!\r\n\r\n\"Thật xin lỗi con, Viêm nhi, nếu một năm sau đấu khí của con chưa đạt đến thất đoạn thì phụ thân cũng chỉ có thể nhịn đau đem con phân về trong sản nghiệp của gia tộc mà thôi, việc này cũng không phải một mình phụ thân có thể định đoạt, mấy cái lão gia hỏa kia mọi lúc đều chờ phụ thân mắc sai lầm ài…\" Nhìn Tiêu Viêm bình tĩnh, Tiêu Chiến có chút hổ thẹn thở dài.\r\n\r\n\"Phụ thân, con sẽ cố gắng, một năm sau con nhất định sẽ đạt tới thất đoạn đấu khí!\" Tiêu Viêm mỉm cười an ủi.\r\n\r\n\"Một năm, tăng bốn đoạn sao? Ha ha, nếu là trước kia, có lẽ còn có thể, bất quá hiện tại… Không có một điểm cơ hội nữa rồi…\" Tuy an ủi phụ thân như vật, nhưng trong lòng Tiêu Viêm vẫn cười khổ tự giễu.\r\n\r\nCũng là phi thường hiểu rõ Tiêu Viêm, Tiêu Chiến cũng chỉ đành thở dài một tiếng. Hắn biết một năm tăng bốn đoạn đấu khí có bao nhiêu khó khăn, vỗ nhẹ đầu hắn, bỗng nhiên cười nói: \"Đã không còn sớm, trở về nghỉ ngơi đi, ngày mai gia tộc sẽ có khách quý tới, con cũng đừng để thất lễ.\"\r\n\r\n\"Khách quý? Ai ạ?\" Tiêu Viêm tò mò hỏi.\r\n\r\n\"Ngày mai sẽ biết.\" Nháy nháy mắt với Tiêu Viêm, Tiêu Chiến cười to mà đi, chỉ lưu lại Tiêu Viêm với cảm giác bất đắc dĩ.\r\n\r\n\"Yên tâm đi phụ thân, con sẽ cố hết sức!\" Vuốt ve chiếc nhẫn cổ xưa trên tay, Tiêu Viêm ngẩng đầu lên lẩm bẩm nói.\r\n\r\nTrong lúc Tiêu Viêm ngẩng đầu đó, chiếc nhẫn màu đen cổ xưa trên tay bỗng nhiên hiện lên một tia ánh sáng cực kỳ yếu ớt quỷ dị, ánh sát chỉ chớp lên trong nháy mắt, không có bất cứ ai phát hiện ra…', 2, '2025-12-29 14:16:16'),
(3, 1, 'chuong 3', '', 'aaaa', 1, '2026-04-18 13:12:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `novel_favorites`
--

CREATE TABLE `novel_favorites` (
  `user_id` int(11) NOT NULL,
  `novel_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `novel_favorites`
--

INSERT INTO `novel_favorites` (`user_id`, `novel_id`, `created_at`) VALUES
(3, 1, '2026-04-18 13:11:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reading_history`
--

CREATE TABLE `reading_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('novel','comic') NOT NULL COMMENT 'Loại truyện',
  `item_id` varchar(255) NOT NULL COMMENT 'ID truyện hoặc Slug',
  `item_name` varchar(255) NOT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `chapter_name` varchar(100) DEFAULT NULL COMMENT 'Tên chương vừa đọc',
  `chapter_url` text NOT NULL COMMENT 'Link để đọc tiếp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reading_history`
--

INSERT INTO `reading_history` (`id`, `user_id`, `type`, `item_id`, `item_name`, `item_image`, `chapter_name`, `chapter_url`, `updated_at`) VALUES
(15, 1, 'comic', 'xich-long-chi-tu', 'Xích Long Chi Tử', 'https://img.otruyenapi.com/uploads/comics/xich-long-chi-tu-thumb.jpg', 'Xích Long Chi Tử - Chap 1', '/WebDocTruyen/comic_read.php?api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjkxMmNiNmJhMmNhOWY4Y2JhNTk4YTQ4&name=X%C3%ADch+Long+Chi+T%E1%BB%AD+-+Chap+1&slug=xich-long-chi-tu', '2026-01-09 12:20:50'),
(16, 1, 'comic', 'world-embryo-remastered', 'World Embryo Remastered', 'https://img.otruyenapi.com/uploads/comics/world-embryo-remastered-thumb.jpg', 'World Embryo Remastered - Chap 1', '/WebDocTruyen/comic_read.php?api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjk0M2Q2MDJlMGQ3NTNmMzJlNGZiYTA4&name=World+Embryo+Remastered+-+Chap+1&slug=world-embryo-remastered', '2026-01-09 12:02:06'),
(25, 1, 'comic', 'tuyet-the-sat-thu-hoi-quy-tai-hoc-vien', 'Tuyệt Thế Sát Thủ Hồi Quy Tại Học Viện', 'https://img.otruyenapi.com/uploads/comics/tuyet-the-sat-thu-hoi-quy-tai-hoc-vien-thumb.jpg', 'Tuyệt Thế Sát Thủ Hồi Quy Tại Học Viện - Chap 2', '/WebDocTruyen/comic_read.php?api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjkzYmM0ZTJlMGQ3NTNmMzJlNGMwZTJk&name=Tuy%E1%BB%87t+Th%E1%BA%BF+S%C3%A1t+Th%E1%BB%A7+H%E1%BB%93i+Quy+T%E1%BA%A1i+H%E1%BB%8Dc+Vi%E1%BB%87n+-+Chap+2&slug=tuyet-the-sat-thu-hoi-quy-tai-hoc-vien', '2026-01-09 13:38:39'),
(33, 3, 'comic', 'tu-than-phieu-nguyet', 'Tử Thần Phiêu Nguyệt', 'https://img.otruyenapi.com/uploads/comics/tu-than-phieu-nguyet-thumb.jpg', 'Tử Thần Phiêu Nguyệt - Chap 0', '/WebDocTruyen/comic_read.php?api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjU4N2M3OGVlMTIwZGRmMjE5OGYzNDI4&name=T%E1%BB%AD+Th%E1%BA%A7n+Phi%C3%AAu+Nguy%E1%BB%87t+-+Chap+0&slug=tu-than-phieu-nguyet', '2026-01-09 13:29:08'),
(35, 1, 'comic', 'vua-hiep-si-da-tro-lai-voi-mot-vi-than', 'Vua Hiệp Sĩ Đã Trở Lại Với Một Vị Thần', 'https://img.otruyenapi.com/uploads/comics/vua-hiep-si-da-tro-lai-voi-mot-vi-than-thumb.jpg', 'Vua Hiệp Sĩ Đã Trở Lại Với Một Vị Thần - Chap 5', '/WebDocTruyen/comic_read.php?api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjU4ZjdiNTdhYzUyODIwZjU2NGFlYzZi&name=Vua+Hi%E1%BB%87p+S%C4%A9+%C4%90%C3%A3+Tr%E1%BB%9F+L%E1%BA%A1i+V%E1%BB%9Bi+M%E1%BB%99t+V%E1%BB%8B+Th%E1%BA%A7n+-+Chap+5&slug=vua-hiep-si-da-tro-lai-voi-mot-vi-than', '2026-01-09 14:10:44'),
(36, 1, 'comic', 'yeu-than-ky', 'Yêu Thần Ký', 'https://img.otruyenapi.com/uploads/comics/yeu-than-ky-thumb.jpg', 'Yêu Thần Ký - Chap 663', '/WebDocTruyen/comic_read.php?api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjk1ODhlMDBlMGQ3NTNmMzJlNTBiYWU0&slug=yeu-than-ky&name=Y%C3%AAu+Th%E1%BA%A7n+K%C3%BD+-+Chap+663', '2026-01-10 05:05:07'),
(51, 1, 'novel', '1', 'Đấu phá thương khung', 'https://nhanvat.wiki/wp-content/uploads/2023/01/dau-pha-thuong-khung.jpg', 'Chương 1: Thiên tài rơi rụng', '/WebDocTruyen/index.php?route=novel/read&id=1', '2026-04-18 11:26:12'),
(52, 1, 'comic', 'vo-dich-thien-ha-chuyen-the-dau-quan-phe-dich', 'Vô Địch Thiên Hạ, Chuyển Thế Đầu Quân Phe Địch', 'https://img.otruyenapi.com/uploads/comics/vo-dich-thien-ha-chuyen-the-dau-quan-phe-dich-thumb.jpg', 'Vô Địch Thiên Hạ, Chuyển Thế Đầu Quân Phe Địch - Chap 1', '/WebDocTruyen/comic_read.php?api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjk1MGM4NGM3Yjg5YjViMjU3MDVhMzFi&name=V%C3%B4+%C4%90%E1%BB%8Bch+Thi%C3%AAn+H%E1%BA%A1%2C+Chuy%E1%BB%83n+Th%E1%BA%BF+%C4%90%E1%BA%A7u+Qu%C3%A2n+Phe+%C4%90%E1%BB%8Bch+-+Chap+1&slug=vo-dich-thien-ha-chuyen-the-dau-quan-phe-dich', '2026-01-10 13:09:05'),
(59, 3, 'comic', 'ly-do-ket-hon', 'Lý Do Kết Hôn', 'https://img.otruyenapi.com/uploads/comics/ly-do-ket-hon-thumb.jpg', 'Lý Do Kết Hôn - Chap 1', 'http://localhost/webdoctruyen/index.php?route=comic/read&api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjVmMTVmNTE4ZDE5MTA0YjljNzQwMmJi&name=L%C3%BD+Do+K%E1%BA%BFt+H%C3%B4n+-+Chap+1&slug=ly-do-ket-hon', '2026-04-18 16:21:11'),
(60, 3, 'novel', '1', 'Đấu phá thương khung', 'https://nhanvat.wiki/wp-content/uploads/2023/01/dau-pha-thuong-khung.jpg', 'Chương 1: Thiên tài rơi rụng', '/webdoctruyen/index.php?route=novel/read&id=1', '2026-04-18 11:33:07'),
(61, 3, 'comic', 'em-cho-co-muon-chut-lua-nhe', 'Em Cho Cô Mượn Chút Lửa Nhé?', 'https://img.otruyenapi.com/uploads/comics/em-cho-co-muon-chut-lua-nhe-thumb.jpg', 'Em Cho Cô Mượn Chút Lửa Nhé? - Chap 1.1', 'http://localhost/webdoctruyen/index.php?route=comic/read&api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjYyOWRlYjAxNjRjMjU2ZWFkYWEwMjhm&name=Em+Cho+C%C3%B4+M%C6%B0%E1%BB%A3n+Ch%C3%BAt+L%E1%BB%ADa+Nh%C3%A9%3F+-+Chap+1.1&slug=em-cho-co-muon-chut-lua-nhe', '2026-04-18 16:18:28'),
(62, 3, 'comic', 'trong-khai-di-the-ta-dung-guong-than-tro-thanh-vo-dich', 'Trọng Khải Dị Thế: Ta Dùng Gương Thần Trở Thành Vô Địch', 'https://img.otruyenapi.com/uploads/comics/trong-khai-di-the-ta-dung-guong-than-tro-thanh-vo-dich-thumb.jpg', 'Trọng Khải Dị Thế: Ta Dùng Gương Thần Trở Thành Vô Địch - Chap 1', 'http://localhost/webdoctruyen/index.php?route=comic/read&api=aHR0cHM6Ly9zdjEub3RydXllbmNkbi5jb20vdjEvYXBpL2NoYXB0ZXIvNjkwNmRmNGYyY2I2NzhkN2IxY2U2NGQ3&name=Tr%E1%BB%8Dng+Kh%E1%BA%A3i+D%E1%BB%8B+Th%E1%BA%BF%3A+Ta+D%C3%B9ng+G%C6%B0%C6%A1ng+Th%E1%BA%A7n+Tr%E1%BB%9F+Th%C3%A0nh+V%C3%B4+%C4%90%E1%BB%8Bch+-+Chap+1&slug=trong-khai-di-the-ta-dung-guong-than-tro-thanh-vo-dich', '2026-04-18 16:21:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','mod','admin') DEFAULT 'user',
  `status` enum('active','banned') DEFAULT 'active',
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `avatar`, `created_at`) VALUES
(1, 'admin', 'admin@web.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 'uploads/avatar_1_1767831762.jpg', '2025-12-29 13:49:07'),
(2, 'user1', 'user1@gmail.com', '$2y$10$L3FKPo5cmMMTihz7UsIzd.fCV5zNhLtVYa44XFU2psCfT7qbFEpSq', 'user', 'active', NULL, '2025-12-29 14:40:54'),
(3, 'banuser', 'banuser@gmail.com', '$2y$10$9GcNG7IGROTgqFy5kTf0A.txc6filbk07FPuG3lPqq2J5pkYrt7Sq', 'mod', 'active', 'https://i2.hako.vip/ln/users/avatars/u59990-d4002344-2d90-480d-a78c-d4b28c9f4a57.jpg', '2026-01-02 13:51:42'),
(4, 'tuanngao', 't@gmail.com', '$2y$10$zhAxtWJQ/1b1mRmrqEz1b.GgNFkuxQEeeFmSfQ9jlm2/2hUfONwxS', 'user', 'active', NULL, '2026-04-18 13:32:52'),
(5, 'huy', '1@gmail.com', '$2y$10$c.A0XeTuMArlNPlj1Bt0LuZikeyLdHiE7nKCU60K9pd.ikA8yt6Ue', 'user', 'active', NULL, '2026-04-18 14:19:51'),
(6, 'test67', 'test67@gmail.com', '$2y$10$spbU7JOQJXVWsa64kjVjLeD1Pj9agbXvK2QkjWlP/5e2MssQ5gyAa', 'user', 'active', NULL, '2026-04-18 16:25:57');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Chỉ mục cho bảng `comics_cache`
--
ALTER TABLE `comics_cache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `comic_favorites`
--
ALTER TABLE `comic_favorites`
  ADD PRIMARY KEY (`user_id`,`comic_slug`);

--
-- Chỉ mục cho bảng `comic_views`
--
ALTER TABLE `comic_views`
  ADD PRIMARY KEY (`comic_slug`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cmt_user` (`user_id`);

--
-- Chỉ mục cho bảng `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`user_id`,`comment_id`),
  ADD KEY `fk_cl_cmt` (`comment_id`);

--
-- Chỉ mục cho bảng `forum_categories`
--
ALTER TABLE `forum_categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `forum_post_likes`
--
ALTER TABLE `forum_post_likes`
  ADD PRIMARY KEY (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Chỉ mục cho bảng `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Chỉ mục cho bảng `novels`
--
ALTER TABLE `novels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `novel_categories`
--
ALTER TABLE `novel_categories`
  ADD PRIMARY KEY (`novel_id`,`category_id`),
  ADD KEY `fk_nc_cat` (`category_id`);

--
-- Chỉ mục cho bảng `novel_chapters`
--
ALTER TABLE `novel_chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `novel_id` (`novel_id`);

--
-- Chỉ mục cho bảng `novel_favorites`
--
ALTER TABLE `novel_favorites`
  ADD PRIMARY KEY (`user_id`,`novel_id`),
  ADD KEY `fk_nfav_novel` (`novel_id`);

--
-- Chỉ mục cho bảng `reading_history`
--
ALTER TABLE `reading_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_history` (`user_id`,`type`,`item_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `comics_cache`
--
ALTER TABLE `comics_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `forum_categories`
--
ALTER TABLE `forum_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `novels`
--
ALTER TABLE `novels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `novel_chapters`
--
ALTER TABLE `novel_chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `reading_history`
--
ALTER TABLE `reading_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `comic_favorites`
--
ALTER TABLE `comic_favorites`
  ADD CONSTRAINT `fk_cfav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_cmt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `fk_cl_cmt` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `forum_post_likes`
--
ALTER TABLE `forum_post_likes`
  ADD CONSTRAINT `forum_post_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_post_likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD CONSTRAINT `forum_topics_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_topics_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `novel_categories`
--
ALTER TABLE `novel_categories`
  ADD CONSTRAINT `fk_nc_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nc_novel` FOREIGN KEY (`novel_id`) REFERENCES `novels` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `novel_chapters`
--
ALTER TABLE `novel_chapters`
  ADD CONSTRAINT `fk_chap_novel` FOREIGN KEY (`novel_id`) REFERENCES `novels` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `novel_favorites`
--
ALTER TABLE `novel_favorites`
  ADD CONSTRAINT `fk_nfav_novel` FOREIGN KEY (`novel_id`) REFERENCES `novels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nfav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
