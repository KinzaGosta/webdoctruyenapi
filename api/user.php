<?php
// File: api/user.php
// Quản lý thông tin, lịch sử, yêu thích, thông báo của User

require_once '../config/database.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header("Content-Type: application/json; charset=UTF-8");

function response($status, $msg, $data = []) {
    echo json_encode(['status' => $status, 'message' => $msg, 'data' => $data]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    response('error', 'Bạn chưa đăng nhập');
}

$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {
    // ==========================================
    // 1. LỊCH SỬ ĐỌC (HISTORY)
    // ==========================================
    case 'history_add':
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
        $item_name = isset($_POST['item_name']) ? $_POST['item_name'] : '';
        $item_image = isset($_POST['item_image']) ? $_POST['item_image'] : '';
        $chapter_name = isset($_POST['chapter_name']) ? $_POST['chapter_name'] : '';
        $chapter_url = isset($_POST['chapter_url']) ? $_POST['chapter_url'] : '';

        if($type && $item_id) {
            $sql = "INSERT INTO reading_history (user_id, type, item_id, item_name, item_image, chapter_name, chapter_url) 
                    VALUES (?, ?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    chapter_name = VALUES(chapter_name), 
                    chapter_url = VALUES(chapter_url), 
                    updated_at = NOW()";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssss", $user_id, $type, $item_id, $item_name, $item_image, $chapter_name, $chapter_url);
            $stmt->execute();
            response('success', 'Đã lưu lịch sử');
        } else {
            response('error', 'Thiếu dữ liệu');
        }
        break;

    case 'history_get':
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
        $stmt = $conn->prepare("SELECT * FROM reading_history WHERE user_id = ? ORDER BY updated_at DESC LIMIT ?");
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        response('success', '', $data);
        break;

    case 'history_delete_one':
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM reading_history WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $id, $user_id);
            if ($stmt->execute()) { response('success', 'Đã xóa khỏi lịch sử'); } 
            else { response('error', 'Lỗi cơ sở dữ liệu'); }
        } else {
            response('error', 'ID không hợp lệ');
        }
        break;

    case 'history_delete_all':
        $stmt = $conn->prepare("DELETE FROM reading_history WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) { response('success', 'Đã xóa toàn bộ lịch sử'); }
        else { response('error', 'Lỗi cơ sở dữ liệu'); }
        break;

    // ==========================================
    // 2. YÊU THÍCH (FAVORITE)
    // ==========================================
    case 'toggle_favorite':
        $type = $_POST['type'] ?? 'novel';
        if ($type === 'novel') {
            $novel_id = intval($_POST['id']);
            $check = $conn->query("SELECT * FROM novel_favorites WHERE user_id = $user_id AND novel_id = $novel_id");
            if ($check->num_rows > 0) {
                $conn->query("DELETE FROM novel_favorites WHERE user_id = $user_id AND novel_id = $novel_id");
                $conn->query("UPDATE novels SET favorite_count = favorite_count - 1 WHERE id = $novel_id");
                response('removed', 'Đã bỏ thích truyện chữ!');
            } else {
                $stmt = $conn->prepare("INSERT INTO novel_favorites (user_id, novel_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $user_id, $novel_id);
                $stmt->execute();
                $conn->query("UPDATE novels SET favorite_count = favorite_count + 1 WHERE id = $novel_id");
                response('added', 'Đã lưu truyện chữ!');
            }
        } elseif ($type === 'comic') {
            $slug = $_POST['slug'];
            $name = $_POST['name'] ?? '';
            $thumb = $_POST['thumb'] ?? '';
            $check = $conn->prepare("SELECT * FROM comic_favorites WHERE user_id = ? AND comic_slug = ?");
            $check->bind_param("is", $user_id, $slug);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $del = $conn->prepare("DELETE FROM comic_favorites WHERE user_id = ? AND comic_slug = ?");
                $del->bind_param("is", $user_id, $slug);
                $del->execute();
                response('removed', 'Đã bỏ thích truyện tranh!');
            } else {
                $stmt = $conn->prepare("INSERT INTO comic_favorites (user_id, comic_slug, comic_name, comic_thumb) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $user_id, $slug, $name, $thumb);
                $stmt->execute();
                response('added', 'Đã lưu truyện tranh!');
            }
        }
        break;

    // ==========================================
    // 3. THÔNG BÁO (NOTIFICATION)
    // ==========================================
    case 'notif_report':
        $title = trim($_POST['title']);
        $msg = trim($_POST['message']);
        if (empty($title) || empty($msg)) response('error', 'Vui lòng nhập đủ thông tin');
        $admins = $conn->query("SELECT id FROM users WHERE role = 'admin'");
        if ($admins->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, type, title, message) VALUES (?, ?, 'report', ?, ?)");
            while ($row = $admins->fetch_assoc()) {
                $admin_id = $row['id'];
                $stmt->bind_param("iiss", $user_id, $admin_id, $title, $msg);
                $stmt->execute();
            }
            response('success', 'Đã gửi báo lỗi tới BQT!');
        } else {
            response('error', 'Không tìm thấy Admin nào để gửi.');
        }
        break;

    case 'notif_get':
        $sql = "SELECT n.*, u.username, u.avatar, u.role FROM notifications n LEFT JOIN users u ON n.sender_id = u.id WHERE n.receiver_id = ? ORDER BY n.created_at DESC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifs = [];
        $count_sql = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE receiver_id = $user_id AND is_read = 0");
        $unread_count = $count_sql->fetch_assoc()['total'];

        while ($row = $result->fetch_assoc()) {
            if ($row['type'] == 'system') {
                $row['sender_name'] = 'Hệ Thống';
                $row['sender_avatar'] = 'assets/system_logo.jpg';
            } else {
                $row['sender_name'] = $row['username'] ?? 'Người dùng ẩn';
                $row['sender_avatar'] = $row['avatar'] ?? 'https://ui-avatars.com/api/?name='.$row['sender_name'];
            }
            $row['time_ago'] = date('H:i d/m', strtotime($row['created_at']));
            $notifs[] = $row;
        }
        response('success', '', ['notifications' => $notifs, 'unread' => $unread_count]);
        break;

    case 'notif_mark_read':
        $notif_id = intval($_POST['id']);
        $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $notif_id AND receiver_id = $user_id");
        response('success', 'Đã đọc');
        break;

    case 'notif_delete':
        $notif_id = intval($_POST['id']);
        $conn->query("DELETE FROM notifications WHERE id = $notif_id AND receiver_id = $user_id");
        response('success', 'Đã xóa');
        break;

    case 'notif_delete_all':
        $stmt = $conn->prepare("DELETE FROM notifications WHERE receiver_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) { response('success', 'Đã xóa tất cả thông báo'); } 
        else { response('error', 'Lỗi khi xóa'); }
        break;

    default:
        response('error', 'Hành động không hợp lệ');
        break;
}
?>
