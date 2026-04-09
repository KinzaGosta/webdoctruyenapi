<?php
// Tắt báo lỗi để tránh hỏng JSON
error_reporting(0);
ini_set('display_errors', 0);

// 1. SỬA LỖI GIỜ: Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

header('Content-Type: application/json; charset=utf-8');

require_once 'config/database.php';
session_start();

$action = $_POST['action'] ?? '';

// ==================================================
// 1. THÊM BÌNH LUẬN
// ==================================================
if ($action == 'add') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập!']); 
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $content = trim(htmlspecialchars($_POST['content'] ?? ''));
    $type = $_POST['type'] ?? 'novel';
    $obj_id = $_POST['obj_id'] ?? 0;
    $parent_id = intval($_POST['parent_id'] ?? 0);

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Nội dung trống!']);
        exit;
    }

    $novel_id = ($type == 'novel') ? intval($obj_id) : NULL;
    $comic_slug = ($type == 'comic') ? $obj_id : NULL;

    $stmt = $conn->prepare("INSERT INTO comments (user_id, novel_id, comic_slug, content, parent_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisss", $user_id, $novel_id, $comic_slug, $content, $parent_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $conn->error]);
    }
    exit;
}

// ==================================================
// 2. LẤY DANH SÁCH (Cập nhật để biết cmt nào là của mình)
// ==================================================
if ($action == 'list') {
    $type = $_POST['type'] ?? 'novel';
    $obj_id = $_POST['obj_id'] ?? 0;
    $user_current = $_SESSION['user_id'] ?? 0;

    $where = "";
    if ($type == 'novel') {
        $where = "c.novel_id = " . intval($obj_id);
    } else {
        $where = "c.comic_slug = '" . $conn->real_escape_string($obj_id) . "'";
    }

    $sql = "SELECT c.*, u.username, u.avatar, 
            (SELECT COUNT(*) FROM comment_likes cl WHERE cl.comment_id = c.id AND cl.user_id = $user_current) as is_liked
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE $where 
            ORDER BY c.created_at DESC";
    
    $result = $conn->query($sql);

    if (!$result) { echo json_encode([]); exit; }

    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $name = $row['username'] ? $row['username'] : 'User';
        $row['avatar'] = $row['avatar'] ? $row['avatar'] : 'https://ui-avatars.com/api/?name='.$name.'&background=random';
        $row['time_ago'] = time_elapsed_string($row['created_at']);
        
        // Đánh dấu nếu bình luận này là của người đang đăng nhập
        $row['is_mine'] = ($row['user_id'] == $user_current) ? true : false;
        
        $comments[] = $row;
    }

    // Sắp xếp Cha - Con
    $tree = [];
    $map = [];
    foreach ($comments as $cmt) {
        $cmt['replies'] = [];
        $map[$cmt['id']] = $cmt;
    }
    foreach ($comments as $cmt) {
        if ($cmt['parent_id'] != 0) {
            if (isset($map[$cmt['parent_id']])) {
                $map[$cmt['parent_id']]['replies'][] = &$map[$cmt['id']];
            }
        } else {
            $tree[] = &$map[$cmt['id']];
        }
    }

    echo json_encode(array_values($tree));
    exit;
}

// ==================================================
// 3. XÓA BÌNH LUẬN (MỚI)
// ==================================================
if ($action == 'delete') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']); exit;
    }
    
    $cmt_id = intval($_POST['cmt_id']);
    $user_id = $_SESSION['user_id'];

    // Kiểm tra quyền sở hữu trước khi xóa
    $check = $conn->query("SELECT id FROM comments WHERE id = $cmt_id AND user_id = $user_id");
    if ($check->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền xóa bình luận này']); exit;
    }

    // Xóa comment (Cẩn thận: Nếu database không có ON DELETE CASCADE, bạn cần xóa các reply trước)
    // Ở đây ta dùng DELETE đơn giản, giả sử DB đã xử lý hoặc chấp nhận mất con
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $cmt_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi SQL']);
    }
    exit;
}

// ==================================================
// 4. SỬA BÌNH LUẬN (MỚI)
// ==================================================
if ($action == 'edit') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']); exit;
    }

    $cmt_id = intval($_POST['cmt_id']);
    $content = trim(htmlspecialchars($_POST['content']));
    $user_id = $_SESSION['user_id'];

    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Nội dung trống']); exit;
    }

    // Kiểm tra quyền sở hữu
    $check = $conn->query("SELECT id FROM comments WHERE id = $cmt_id AND user_id = $user_id");
    if ($check->num_rows == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Không có quyền sửa']); exit;
    }

    $stmt = $conn->prepare("UPDATE comments SET content = ? WHERE id = ?");
    $stmt->bind_param("si", $content, $cmt_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi SQL']);
    }
    exit;
}

// ==================================================
// 5. XỬ LÝ LIKE
// ==================================================
if ($action == 'like') {
    if (!isset($_SESSION['user_id'])) exit;
    $uid = $_SESSION['user_id'];
    $cmt_id = intval($_POST['cmt_id']);

    $check = $conn->query("SELECT * FROM comment_likes WHERE user_id=$uid AND comment_id=$cmt_id");
    if ($check && $check->num_rows > 0) {
        $conn->query("DELETE FROM comment_likes WHERE user_id=$uid AND comment_id=$cmt_id");
        $conn->query("UPDATE comments SET like_count = like_count - 1 WHERE id=$cmt_id");
    } else {
        $conn->query("INSERT INTO comment_likes (user_id, comment_id) VALUES ($uid, $cmt_id)");
        $conn->query("UPDATE comments SET like_count = like_count + 1 WHERE id=$cmt_id");
    }
    echo 'ok';
    exit;
}

// Hàm tính thời gian (Đã sửa để dùng múi giờ chuẩn)
function time_elapsed_string($datetime, $full = false) {
    try {
        // Lấy giờ hiện tại theo múi giờ VN
        $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        // Lấy giờ bình luận, ép về múi giờ VN
        $ago = new DateTime($datetime, new DateTimeZone('Asia/Ho_Chi_Minh'));
        
        $diff = $now->diff($ago);

        $w = floor($diff->d / 7);
        $diff->d -= $w * 7;

        $string = ['y' => 'năm','m' => 'tháng','w' => 'tuần','d' => 'ngày','h' => 'giờ','i' => 'phút','s' => 'giây'];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' trước' : 'vừa xong';
    } catch (Exception $e) {
        return 'vừa xong';
    }
}
?>