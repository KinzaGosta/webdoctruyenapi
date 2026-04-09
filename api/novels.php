<?php
// File: api/novels.php
require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_home_novels':
        $sql = "SELECT n.*, 
                (SELECT title FROM novel_chapters WHERE novel_id = n.id ORDER BY id DESC LIMIT 1) as latest_chap_title,
                (SELECT created_at FROM novel_chapters WHERE novel_id = n.id ORDER BY id DESC LIMIT 1) as latest_chap_date
                FROM novels n 
                ORDER BY n.updated_at DESC LIMIT 12";
        
        $result = $conn->query($sql);
        if (!$result) {
            $sql = "SELECT * FROM novels ORDER BY updated_at DESC LIMIT 12";
            $result = $conn->query($sql);
        }
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo json_encode(['status' => 'success', 'data' => $data]);
        break;

    case 'get_list':
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 24;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $count_sql = "SELECT COUNT(*) as total FROM novels";
        $total_records = $conn->query($count_sql)->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);

        $sql = "SELECT * FROM novels ORDER BY updated_at DESC LIMIT $offset, $limit";
        $result = $conn->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        echo json_encode([
            'status' => 'success', 
            'data' => $data, 
            'pagination' => [
                'page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_records
            ]
        ]);
        break;

    case 'get_by_category':
        $slug = $_GET['slug'] ?? '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 24;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $cat_stmt = $conn->prepare("SELECT id, name FROM categories WHERE slug = ?");
        $cat_stmt->bind_param("s", $slug);
        $cat_stmt->execute();
        $cat_res = $cat_stmt->get_result();

        if ($cat_res->num_rows == 0) {
            echo json_encode(['status' => 'error', 'message' => 'Category not found']);
            exit;
        }
        $cat = $cat_res->fetch_assoc();
        $cat_id = $cat['id'];

        $count_sql = "SELECT COUNT(*) as total FROM novels WHERE category_id = $cat_id";
        $total_records = $conn->query($count_sql)->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);

        $sql = "SELECT * FROM novels WHERE category_id = $cat_id ORDER BY updated_at DESC LIMIT $offset, $limit";
        $result = $conn->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        echo json_encode([
            'status' => 'success', 
            'category_name' => $cat['name'],
            'data' => $data, 
            'pagination' => [
                'page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_records
            ]
        ]);
        break;

    case 'get_detail':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        $stmt = $conn->prepare("SELECT * FROM novels WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Truyện không tồn tại']);
            exit;
        }
        
        $novel = $res->fetch_assoc();
        
        // Cập nhật lượt xem
        $conn->query("UPDATE novels SET views = views + 1 WHERE id = $id");
        $novel['views']++;

        $chap_stmt = $conn->prepare("SELECT id, title, order_index FROM novel_chapters WHERE novel_id = ? ORDER BY order_index DESC");
        $chap_stmt->bind_param("i", $id);
        $chap_stmt->execute();
        $chap_res = $chap_stmt->get_result();
        
        $chapters = [];
        while ($c = $chap_res->fetch_assoc()) {
            $chapters[] = $c;
        }
        
        echo json_encode([
            'status' => 'success', 
            'data' => [
                'novel' => $novel,
                'chapters' => $chapters
            ]
        ]);
        break;

    case 'get_chapter':
        $chap_id = isset($_GET['chap_id']) ? intval($_GET['chap_id']) : 0;
        
        $stmt = $conn->prepare("SELECT c.*, n.title as novel_title, n.id as novel_id, n.cover_image 
                                FROM novel_chapters c 
                                JOIN novels n ON c.novel_id = n.id 
                                WHERE c.id = ?");
        $stmt->bind_param("i", $chap_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Chương không tồn tại']);
            exit;
        }
        $chapter = $res->fetch_assoc();
        
        // Không đếm view theo từng chương cho novel vì DB ko có cột views trong novel_chapters

        // Xem có chương trước/sau không
        $novel_id = $chapter['novel_id'];
        
        $prev_stmt = $conn->prepare("SELECT id FROM novel_chapters WHERE novel_id = ? AND id < ? ORDER BY id DESC LIMIT 1");
        $prev_stmt->bind_param("ii", $novel_id, $chap_id);
        $prev_stmt->execute();
        $prev_res = $prev_stmt->get_result();
        $prev_id = ($prev_res->num_rows > 0) ? $prev_res->fetch_assoc()['id'] : null;

        $next_stmt = $conn->prepare("SELECT id FROM novel_chapters WHERE novel_id = ? AND id > ? ORDER BY id ASC LIMIT 1");
        $next_stmt->bind_param("ii", $novel_id, $chap_id);
        $next_stmt->execute();
        $next_res = $next_stmt->get_result();
        $next_id = ($next_res->num_rows > 0) ? $next_res->fetch_assoc()['id'] : null;

        // Xử lý Lịch sử Đọc nếu user đã login (có thể gọi từ API hoặc PHP xử lý)
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (isset($_SESSION['user_id'])) {
            $u_id = $_SESSION['user_id'];
            $item_name = $chapter['novel_title'];
            $chap_name = $chapter['title'];
            $item_url = "novel_detail.php?id=" . $novel_id;
            $chap_url = "novel_read.php?id=" . $chap_id;
            $img = $chapter['cover_image'] ? $chapter['cover_image'] : 'assets/images/no-image.jpg';

            $hist = $conn->query("SELECT id FROM reading_history WHERE user_id=$u_id AND item_type='novel' AND item_url='$item_url'");
            if ($hist->num_rows > 0) {
                $conn->query("UPDATE reading_history SET chapter_name='$chap_name', chapter_url='$chap_url', updated_at=NOW() WHERE user_id=$u_id AND item_type='novel' AND item_url='$item_url'");
            } else {
                $conn->query("INSERT INTO reading_history (user_id, item_type, item_name, item_url, chapter_name, chapter_url, item_image) VALUES ($u_id, 'novel', '$item_name', '$item_url', '$chap_name', '$chap_url', '$img')");
            }
        }

        echo json_encode([
            'status' => 'success', 
            'data' => [
                'chapter' => $chapter,
                'prev_id' => $prev_id,
                'next_id' => $next_id
            ]
        ]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
