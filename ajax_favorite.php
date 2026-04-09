<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']);
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_POST['type'] ?? 'novel'; // Mặc định là novel (truyện chữ)

// ==========================================
// TRƯỜNG HỢP 1: TRUYỆN CHỮ (Giữ nguyên logic cũ)
// ==========================================
if ($type === 'novel') {
    $novel_id = intval($_POST['id']);
    
    // Check tồn tại
    $check = $conn->query("SELECT * FROM novel_favorites WHERE user_id = $user_id AND novel_id = $novel_id");

    if ($check->num_rows > 0) {
        $conn->query("DELETE FROM novel_favorites WHERE user_id = $user_id AND novel_id = $novel_id");
        $conn->query("UPDATE novels SET favorite_count = favorite_count - 1 WHERE id = $novel_id");
        echo json_encode(['status' => 'removed', 'message' => 'Đã bỏ thích truyện chữ!']);
    } else {
        $stmt = $conn->prepare("INSERT INTO novel_favorites (user_id, novel_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $novel_id);
        $stmt->execute();
        $conn->query("UPDATE novels SET favorite_count = favorite_count + 1 WHERE id = $novel_id");
        echo json_encode(['status' => 'added', 'message' => 'Đã lưu truyện chữ!']);
    }
} 

// ==========================================
// TRƯỜNG HỢP 2: TRUYỆN TRANH (Xử lý mới)
// ==========================================
elseif ($type === 'comic') {
    $slug = $_POST['slug'];
    $name = $_POST['name'] ?? '';
    $thumb = $_POST['thumb'] ?? '';

    // Check tồn tại theo Slug
    $check = $conn->prepare("SELECT * FROM comic_favorites WHERE user_id = ? AND comic_slug = ?");
    $check->bind_param("is", $user_id, $slug);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        // Đã có -> Xóa
        $del = $conn->prepare("DELETE FROM comic_favorites WHERE user_id = ? AND comic_slug = ?");
        $del->bind_param("is", $user_id, $slug);
        $del->execute();
        echo json_encode(['status' => 'removed', 'message' => 'Đã bỏ thích truyện tranh!']);
    } else {
        // Chưa có -> Thêm (Lưu cả tên và ảnh để sau này hiển thị cho nhanh)
        $stmt = $conn->prepare("INSERT INTO comic_favorites (user_id, comic_slug, comic_name, comic_thumb) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $slug, $name, $thumb);
        $stmt->execute();
        echo json_encode(['status' => 'added', 'message' => 'Đã lưu truyện tranh!']);
    }
}
?>