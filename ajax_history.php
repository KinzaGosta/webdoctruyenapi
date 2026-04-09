<?php
// File: ajax_history.php
session_start();
require_once 'config/database.php';

// Đặt header trả về JSON để JS phía client hiểu
header('Content-Type: application/json');

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

// 2. Xử lý xóa 1 mục
if ($action === 'delete_one') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // QUAN TRỌNG: Thêm điều kiện AND user_id = ? để đảm bảo 
        // người dùng chỉ xóa được lịch sử của chính mình (bảo mật)
        $stmt = $conn->prepare("DELETE FROM reading_history WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi cơ sở dữ liệu']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID không hợp lệ']);
    }
    exit;
}

// 3. Xử lý xóa tất cả (nếu cần sau này)
if ($action === 'delete_all') {
    $stmt = $conn->prepare("DELETE FROM reading_history WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    }
    exit;
}

// Mặc định trả về lỗi nếu không khớp action nào
echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ']);
?>