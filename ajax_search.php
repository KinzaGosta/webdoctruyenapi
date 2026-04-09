<?php
require_once 'config/database.php';

$keyword = $_POST['keyword'] ?? '';
$keyword = trim($keyword);

if (strlen($keyword) < 1) {
    exit; // Nếu chưa nhập gì thì không làm gì cả
}

// 1. TÌM TRUYỆN CHỮ (Ưu tiên hiển thị trước vì nó nhanh - do nằm ở server mình)
$sql = "SELECT id, title, cover_image, author FROM novels WHERE title LIKE ? LIMIT 5";
$likeKey = "%" . $keyword . "%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $likeKey);
$stmt->execute();
$result = $stmt->get_result();

// Hiển thị kết quả Truyện Chữ
if ($result->num_rows > 0) {
    echo '<div class="search-group-title">🖋️ Truyện Chữ</div>';
    while ($row = $result->fetch_assoc()) {
        $img = $row['cover_image'] ? $row['cover_image'] : 'assets/images/no-image.jpg';
        echo '
        <a href="novel_detail.php?id='.$row['id'].'" class="search-item">
            <img src="'.$img.'">
            <div class="info">
                <div class="name">'.htmlspecialchars($row['title']).'</div>
                <div class="meta">'.htmlspecialchars($row['author']).'</div>
            </div>
        </a>';
    }
}

// 2. TÌM TRUYỆN TRANH (API)
// Lưu ý: Tìm API sẽ chậm hơn tìm DB. Nếu thấy lag, bạn có thể bỏ phần này đi.
$api_url = "https://otruyenapi.com/v1/api/tim-kiem?keyword=" . urlencode($keyword);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Chỉ đợi API tối đa 2 giây để tránh treo web
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $data = json_decode($response, true);
    if (isset($data['data']['items']) && count($data['data']['items']) > 0) {
        echo '<div class="search-group-title">🖼️ Truyện Tranh</div>';
        $img_domain = "https://img.otruyenapi.com/uploads/comics/";
        
        // Chỉ lấy 3 truyện đầu tiên để hiển thị cho gọn
        $count = 0;
        foreach ($data['data']['items'] as $comic) {
            if ($count >= 3) break;
            $thumb = $img_domain . $comic['thumb_url'];
            echo '
            <a href="comic_detail.php?slug='.$comic['slug'].'" class="search-item">
                <img src="'.$thumb.'">
                <div class="info">
                    <div class="name">'.$comic['name'].'</div>
                    <div class="meta">Chapter mới nhất</div>
                </div>
            </a>';
            $count++;
        }
    }
}

// Nếu không tìm thấy gì cả
if ($result->num_rows == 0 && (!isset($data['data']['items']) || count($data['data']['items']) == 0)) {
    echo '<div class="p-2 text-muted text-center">Không tìm thấy truyện nào...</div>';
}
?>