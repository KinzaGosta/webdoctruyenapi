<?php
// File: config/database.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');     // User mặc định của XAMPP
define('DB_PASS', '');         // Pass mặc định của XAMPP là rỗng
define('DB_NAME', 'webdoctruyen');

// Bật chế độ báo lỗi chi tiết (Rất quan trọng để debug)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4"); // Hỗ trợ tiếng Việt đầy đủ
} catch (Exception $e) {
    die("<h3>Lỗi kết nối Database!</h3>Chi tiết: " . $e->getMessage());
}
?>