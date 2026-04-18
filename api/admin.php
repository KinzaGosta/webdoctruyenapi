<?php
// File: api/admin.php
require_once '../config/database.php';
require_once '../app/views/includes/functions.php'; // For createSlug if available, else we can inline it.

header("Content-Type: application/json; charset=UTF-8");

// --- 1. AUTHENTICATION & AUTHORIZATION ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role !== 'admin' && $role !== 'mod') {
    echo json_encode(['status' => 'error', 'message' => 'Permission Denied']);
    exit;
}

$is_admin = ($role === 'admin');
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// --- Helper Functions ---
function responseJson($status, $data = [], $message = '') {
    echo json_encode(['status' => $status, 'data' => $data, 'message' => $message]);
    exit;
}

function adminOnly($is_admin) {
    if (!$is_admin) responseJson('error', [], 'Chỉ Admin mới có quyền thao tác!');
}

function createSlugFromText($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/([^a-z0-9\-]+)/', '-', $str);
    $str = trim($str, '-');
    return $str;
}

// --- 2. ROUTING ---
switch ($action) {

    // ==========================================
    // MODULE: DASHBOARD
    // ==========================================
    case 'dashboard_stats':
        $data = [];
        if ($is_admin) {
            $data['count_users'] = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
            $data['count_novels'] = $conn->query("SELECT COUNT(*) as total FROM novels")->fetch_assoc()['total'];
            $data['count_comments'] = $conn->query("SELECT COUNT(*) as total FROM comments")->fetch_assoc()['total'];
            $view_novel = $conn->query("SELECT SUM(views) as total FROM novels")->fetch_assoc()['total'] ?? 0;
            $view_comic = $conn->query("SELECT SUM(view_count) as total FROM comic_views")->fetch_assoc()['total'] ?? 0;
            $data['total_views'] = $view_novel + $view_comic;
            
            $sql_reports = "SELECT n.*, u.username, u.avatar FROM notifications n JOIN users u ON n.sender_id = u.id WHERE n.receiver_id = $user_id AND n.type = 'report' AND n.is_read = 0 ORDER BY n.created_at DESC LIMIT 10";
            $reports_res = $conn->query($sql_reports);
            $reports = [];
            while ($r = $reports_res->fetch_assoc()) { $reports[] = $r; }
            $data['reports'] = $reports;
        } else {
            $data['count_novels'] = $conn->query("SELECT COUNT(*) as total FROM novels")->fetch_assoc()['total'];
            $data['count_cats'] = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
            $data['count_my_novels'] = $conn->query("SELECT COUNT(*) as total FROM novels WHERE posted_by = $user_id")->fetch_assoc()['total'];
        }
        $data['username'] = $_SESSION['username'];
        $data['is_admin'] = $is_admin;
        responseJson('success', $data);
        break;

    // ==========================================
    // MODULE: CATEGORIES
    // ==========================================
    case 'get_categories':
        $res = $conn->query("SELECT * FROM categories ORDER BY id DESC");
        $cats = [];
        while($r = $res->fetch_assoc()) { $cats[] = $r; }
        responseJson('success', $cats);
        break;

    case 'add_category':
        $name = trim($_POST['name'] ?? '');
        if (!$name) responseJson('error', [], 'Tên không hợp lệ');
        $slug = createSlugFromText($name);
        
        $check = $conn->query("SELECT id FROM categories WHERE slug='$slug'");
        if ($check->num_rows > 0) responseJson('error', [], 'Thể loại hoặc Slug đã tồn tại');
        
        $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $slug);
        if ($stmt->execute()) responseJson('success', [], 'Đã thêm thành công');
        else responseJson('error', [], 'Lỗi DB');
        break;

    case 'delete_category':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM categories WHERE id=$id");
        responseJson('success', [], 'Đã xoá');
        break;

    // ==========================================
    // MODULE: NOVELS
    // ==========================================
    case 'get_novels':
        $sql = "SELECT n.*, (SELECT GROUP_CONCAT(c.name SEPARATOR ', ') FROM novel_categories nc JOIN categories c ON nc.category_id = c.id WHERE nc.novel_id = n.id) as category_names 
                FROM novels n ORDER BY n.id DESC";
        $res = $conn->query($sql);
        $novels = [];
        if ($res && $res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) { $novels[] = $r; }
        }
        responseJson('success', $novels);
        break;

    case 'add_novel':
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $image = trim($_POST['cover_image'] ?? '');
        $status = $_POST['status'] ?? 'ongoing';
        $slug = createSlugFromText($title);
        $cats_arr = json_decode($_POST['categories'] ?? '[]');

        if (!$title || !$author) responseJson('error', [], 'Thiếu Tên hoặc Tác giả');

        $stmt = $conn->prepare("INSERT INTO novels (title, slug, author, description, cover_image, status, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $title, $slug, $author, $desc, $image, $status, $user_id);
        
        if ($stmt->execute()) {
            $novel_id = $conn->insert_id;
            if (is_array($cats_arr)) {
                foreach ($cats_arr as $cat_id) {
                    $c_id = intval($cat_id);
                    $conn->query("INSERT INTO novel_categories (novel_id, category_id) VALUES ($novel_id, $c_id)");
                }
            }
            responseJson('success', [], 'Thêm truyện thành công');
        } else {
            responseJson('error', [], 'Lỗi hệ thống');
        }
        break;

    case 'delete_novel':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM novels WHERE id=$id");
        responseJson('success', [], 'Đã xoá truyện');
        break;

    case 'get_novel':
        $id = intval($_GET['id'] ?? 0);
        $res = $conn->query("SELECT * FROM novels WHERE id=$id");
        if ($res && $res->num_rows > 0) {
            $novel = $res->fetch_assoc();
            // Get cats
            $c_res = $conn->query("SELECT category_id FROM novel_categories WHERE novel_id=$id");
            $cats = [];
            while($c = $c_res->fetch_assoc()) { $cats[] = $c['category_id']; }
            $novel['categories'] = $cats;
            responseJson('success', $novel);
        } else {
            responseJson('error', [], 'Không tìm thấy truyện');
        }
        break;

    case 'update_novel':
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $image = trim($_POST['cover_image'] ?? '');
        $status = $_POST['status'] ?? 'ongoing';
        $cats_arr = json_decode($_POST['categories'] ?? '[]');

        if (!$title || !$id) responseJson('error', [], 'Thiếu thông tin bắt buộc');

        $stmt = $conn->prepare("UPDATE novels SET title=?, author=?, description=?, cover_image=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $title, $author, $desc, $image, $status, $id);
        
        if ($stmt->execute()) {
            $conn->query("DELETE FROM novel_categories WHERE novel_id=$id");
            if (is_array($cats_arr)) {
                foreach ($cats_arr as $cat_id) {
                    $c_id = intval($cat_id);
                    $conn->query("INSERT INTO novel_categories (novel_id, category_id) VALUES ($id, $c_id)");
                }
            }
            responseJson('success', [], 'Cập nhật thành công');
        } else {
            responseJson('error', [], 'Lỗi hệ thống');
        }
        break;

    // ==========================================
    // MODULE: CHAPTERS
    // ==========================================
    case 'get_chapters':
        $novel_id = intval($_GET['novel_id'] ?? 0);
        $res = $conn->query("SELECT * FROM novel_chapters WHERE novel_id=$novel_id ORDER BY order_index ASC");
        $chaps = [];
        if ($res && $res->num_rows > 0) {
            while($r = $res->fetch_assoc()) { $chaps[] = $r; }
        }
        responseJson('success', $chaps);
        break;

    case 'delete_chapter':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM novel_chapters WHERE id=$id");
        responseJson('success', [], 'Đã xoá chương');
        break;

    case 'get_chapter':
        $id = intval($_GET['id'] ?? 0);
        $res = $conn->query("SELECT * FROM novel_chapters WHERE id=$id");
        if ($res && $res->num_rows > 0) {
            responseJson('success', $res->fetch_assoc());
        } else {
            responseJson('error', [], 'Chương không tồn tại');
        }
        break;

    case 'add_chapter':
        $novel_id = intval($_POST['novel_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $order_index = floatval($_POST['order_index'] ?? 0);

        if (!$novel_id || !$title || !$content) responseJson('error', [], 'Thiếu dữ liệu');

        $stmt = $conn->prepare("INSERT INTO novel_chapters (novel_id, title, content, order_index) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issd", $novel_id, $title, $content, $order_index);
        
        if ($stmt->execute()) {
            $conn->query("UPDATE novels SET updated_at = NOW() WHERE id = $novel_id");

            // --- Send Notifications to Followers ---
            $f_res = $conn->query("SELECT user_id FROM novel_favorites WHERE novel_id = $novel_id");
            if ($f_res && $f_res->num_rows > 0) {
                // Get novel title for notification
                $n_res = $conn->query("SELECT title FROM novels WHERE id = $novel_id");
                $novel_title = $n_res->fetch_assoc()['title'] ?? 'Truyện';
                
                $notif_title = "Chương mới ra lò!";
                $notif_msg = "Truyện '$novel_title' vừa cập nhật $title";
                $nurl = "index.php?route=novel/detail&id=$novel_id";
                
                $notif_stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, type, target_url, title, message) VALUES (0, ?, 'system', ?, ?, ?)");
                while ($row = $f_res->fetch_assoc()) {
                    $rid = $row['user_id'];
                    $notif_stmt->bind_param("isss", $rid, $nurl, $notif_title, $notif_msg);
                    $notif_stmt->execute();
                }
            }
            // ---------------------------------------

            responseJson('success', [], 'Thêm chương mới thành công');
        } else {
            responseJson('error', [], 'Lỗi hệ thống');
        }
        break;

    case 'update_chapter':
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $order_index = floatval($_POST['order_index'] ?? 0);

        if (!$id || !$title || !$content) responseJson('error', [], 'Thiếu dữ liệu');

        $stmt = $conn->prepare("UPDATE novel_chapters SET title=?, content=?, order_index=? WHERE id=?");
        $stmt->bind_param("ssdi", $title, $content, $order_index, $id);
        
        if ($stmt->execute()) {
            responseJson('success', [], 'Cập nhật thành công');
        } else {
            responseJson('error', [], 'Lỗi hệ thống');
        }
        break;

    // ==========================================
    // MODULE: USERS, COMMENTS, NOTIFICATIONS (ADMIN)
    // ==========================================
    case 'mark_notification_read':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $id AND receiver_id = $user_id");
        responseJson('success', [], 'Đã xử lý');
        break;

    case 'get_users':
        adminOnly($is_admin);
        $res = $conn->query("SELECT id, username, email, avatar, password, role, status, created_at FROM users ORDER BY id DESC");
        $users = [];
        while($r = $res->fetch_assoc()) { 
            // Fix null avatar
            if(empty($r['avatar'])) {
                $r['avatar'] = 'https://ui-avatars.com/api/?name='.$r['username'].'&background=random';
            }
            $users[] = $r; 
        }
        responseJson('success', $users);
        break;

    case 'reset_password':
        adminOnly($is_admin);
        $id = intval($_POST['id'] ?? 0);
        $new_pwd = $_POST['password'] ?? '';
        if (!$id || !$new_pwd) responseJson('error', [], 'Thiếu thông tin');
        $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hashed' WHERE id = $id");
        responseJson('success', [], 'Đã đổi mật khẩu');
        break;
        
    case 'edit_user':
        adminOnly($is_admin);
        $id = intval($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'user');
        $pwd = trim($_POST['password'] ?? '');

        if (!$id || !$username || !$email) responseJson('error', [], 'Thiếu thông tin bắt buộc');
        
        if (!empty($pwd)) {
            $hashed = password_hash($pwd, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $email, $role, $hashed, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $email, $role, $id);
        }
        
        if ($stmt->execute()) {
            if ($id == $user_id) {
                $_SESSION['role'] = $role;
                $_SESSION['username'] = $username;
            }
            responseJson('success', [], 'Cập nhật thành công');
        } else {
            responseJson('error', [], 'Lỗi hệ thống');
        }
        break;

    case 'delete_user_account':
        adminOnly($is_admin);
        $id = intval($_POST['id'] ?? 0);
        if ($id == $user_id) responseJson('error', [], 'Không thể tự xóa bản thân');
        $conn->query("DELETE FROM users WHERE id = $id");
        responseJson('success', [], 'Đã xóa tài khoản');
        break;

    case 'update_user_role':
        adminOnly($is_admin);
        $id = intval($_POST['id'] ?? 0);
        $new_role = $_POST['role'] ?? 'user';
        $conn->query("UPDATE users SET role = '$new_role' WHERE id = $id");
        if ($id == $user_id) {
            $_SESSION['role'] = $new_role;
        }
        responseJson('success', [], 'Thành công');
        break;
        
    case 'toggle_user_status':
        adminOnly($is_admin);
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'active';
        $conn->query("UPDATE users SET status = '$status' WHERE id = $id AND role != 'admin'");
        responseJson('success', [], 'Thành công');
        break;
        
    case 'get_comments':
        adminOnly($is_admin);
        // Query global comments
        $res = $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id ORDER BY c.id DESC LIMIT 50");
        $cmts = [];
        if ($res && $res->num_rows > 0) {
            while($r = $res->fetch_assoc()) { $cmts[] = $r; }
        }
        responseJson('success', $cmts);
        break;

    case 'delete_comment':
        adminOnly($is_admin);
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM comments WHERE id = $id");
        responseJson('success', [], 'Đã xóa bình luận');
        break;

    case 'send_notification':
        adminOnly($is_admin);
        $uid = intval($_POST['user_id'] ?? 0); // 0 means Global (System update)
        $title = trim($_POST['title'] ?? '');
        $msg = trim($_POST['message'] ?? '');
        
        if (!$title) responseJson('error', [], 'Bảng tin cần tiêu đề');

        if ($uid > 0) { // Send Personal
            $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, type, title, message) VALUES (?, ?, 'system', ?, ?)");
            $stmt->bind_param("iiss", $user_id, $uid, $title, $msg);
            $stmt->execute();
        } else { // Global Update
            // Send to ALL users? Or just create a system log?
            // Actually, inserting for ALL users can be heavy manually. Let's do a simple bulk insert.
            $users = $conn->query("SELECT id FROM users");
            $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, type, title, message) VALUES (?, ?, 'system', ?, ?)");
            while($u = $users->fetch_assoc()) {
                $rid = $u['id'];
                $stmt->bind_param("iiss", $user_id, $rid, $title, $msg);
                $stmt->execute();
            }
        }
        responseJson('success', [], 'Đã gửi thông báo thành công');
        break;

    default:
        responseJson('error', [], 'Invalid Admin Action');
        break;
}
