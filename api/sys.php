<?php
// File: api/sys.php
// Quản lý các logic hệ thống chung như danh mục, cài đặt...

require_once '../config/database.php';
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing action']);
    exit;
}

$action = $_GET['action'];

switch ($action) {
    case 'get_categories':
        $check_table = $conn->query("SHOW TABLES LIKE 'categories'");
        if ($check_table && $check_table->num_rows > 0) {
            $cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
            $data = [];
            while ($row = $cats->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            echo json_encode(['status' => 'success', 'data' => []]);
        }
        break;

    case 'get_top_views':
        // Tương đương với logic trong top_view.php
        $type = isset($_GET['type']) ? $_GET['type'] : 'novel';
        if ($type === 'novel') {
            $result = $conn->query("SELECT * FROM novels ORDER BY views DESC LIMIT 5");
            $data = [];
            if ($result) {
                while($row = $result->fetch_assoc()) {
                    $row['cover_image'] = $row['cover_image'] ? $row['cover_image'] : 'assets/images/no-image.jpg';
                    $data[] = $row;
                }
            }
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else if ($type === 'comic') {
            $result = $conn->query("SELECT comic_slug as slug, comic_name as name, comic_thumb as thumb_url, view_count FROM comic_views ORDER BY view_count DESC LIMIT 5");
            $data = [];
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    // CƠ CHẾ SELF-HEALING (nếu thiếu tên/ảnh)
                    if (empty($row['name']) || empty($row['thumb_url'])) {
                        $slug_api = $row['slug'];
                        $api_url = "https://otruyenapi.com/v1/api/truyen-tranh/" . $slug_api;
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $api_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        if ($response) {
                            $res_data = json_decode($response, true);
                            if (isset($res_data['data']['item'])) {
                                $row['name'] = $res_data['data']['item']['name'];
                                $row['thumb_url'] = "https://img.otruyenapi.com/uploads/comics/" . $res_data['data']['item']['thumb_url'];
                                $stmt_update = $conn->prepare("UPDATE comic_views SET comic_name = ?, comic_thumb = ? WHERE comic_slug = ?");
                                $stmt_update->bind_param("sss", $row['name'], $row['thumb_url'], $slug_api);
                                $stmt_update->execute();
                            }
                        }
                    }
                    $data[] = $row;
                }
            }
            echo json_encode(['status' => 'success', 'data' => $data]);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
