<?php
// File: api/search.php
require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

$keyword = $_POST['keyword'] ?? (isset($_GET['keyword']) ? $_GET['keyword'] : '');
$keyword = trim($keyword);

if (strlen($keyword) < 1) {
    echo json_encode(['status' => 'success', 'data' => []]);
    exit;
}

$results = [];

// 1. TÌM TRUYỆN CHỮ
$sql = "SELECT id, title, cover_image, author FROM novels WHERE title LIKE ? LIMIT 5";
$likeKey = "%" . $keyword . "%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $likeKey);
$stmt->execute();
$db_result = $stmt->get_result();

$novel_list = [];
if ($db_result->num_rows > 0) {
    while ($row = $db_result->fetch_assoc()) {
        $novel_list[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'cover_image' => $row['cover_image'] ? $row['cover_image'] : 'assets/images/no-image.jpg',
            'author' => $row['author']
        ];
    }
}
if (!empty($novel_list)) {
    $results[] = [
        'type' => 'novel',
        'title' => '🖋️ Truyện Chữ',
        'items' => $novel_list
    ];
}

// 2. TÌM TRUYỆN TRANH (API)
$api_url = "https://otruyenapi.com/v1/api/tim-kiem?keyword=" . urlencode($keyword);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$comic_list = [];
if ($response) {
    $data = json_decode($response, true);
    if (isset($data['data']['items']) && count($data['data']['items']) > 0) {
        $img_domain = "https://img.otruyenapi.com/uploads/comics/";
        $count = 0;
        foreach ($data['data']['items'] as $comic) {
            if ($count >= 3) break;
            $comic_list[] = [
                'slug' => $comic['slug'],
                'name' => $comic['name'],
                'thumb' => $img_domain . $comic['thumb_url'],
                'meta' => 'Chapter mới nhất'
            ];
            $count++;
        }
    }
}
if (!empty($comic_list)) {
    $results[] = [
        'type' => 'comic',
        'title' => '🖼️ Truyện Tranh',
        'items' => $comic_list
    ];
}

echo json_encode(['status' => 'success', 'data' => $results]);
?>
