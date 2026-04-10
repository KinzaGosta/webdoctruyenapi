<?php
require_once 'config/database.php';
require_once 'app/views/includes/header.php';

$slug = $_GET['slug'] ?? 'action'; // Mặc định nếu không có

// Gọi API theo thể loại
$api_url = "https://otruyenapi.com/v1/api/the-loai/" . $slug;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$comics = [];
$cat_name = "Thể loại";
$img_domain = "https://img.otruyenapi.com/uploads/comics/";

if ($response) {
    $data = json_decode($response, true);
    if (isset($data['data']['items'])) {
        $comics = $data['data']['items'];
        $cat_name = $data['data']['titlePage'] ?? $slug;
    }
}
?>

<div class="container mt-4">
    <h3 class="mb-4 border-bottom pb-2">🖼️ Truyện Tranh: <span class="text-danger"><?= $cat_name ?></span></h3>
    
    <?php if (!empty($comics)): ?>
        <div class="row">
            <?php foreach ($comics as $comic): ?>
                <div class="col-6 col-md-3 col-lg-2 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="index.php?route=comic/detail&slug=<?= $comic['slug'] ?>">
                            <img src="<?= $img_domain . $comic['thumb_url'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body p-2">
                            <h6 class="text-truncate">
                                <a href="index.php?route=comic/detail&slug=<?= $comic['slug'] ?>" class="text-dark text-decoration-none">
                                    <?= $comic['name'] ?>
                                </a>
                            </h6>
                            <small class="text-muted">Chương mới nhất</small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Đang tải hoặc không tìm thấy truyện...</p>
    <?php endif; ?>
</div>