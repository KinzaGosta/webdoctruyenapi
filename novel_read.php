<?php
require_once 'config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Lấy ID chương
if (!isset($_GET['id'])) {
    die("Lỗi: Thiếu ID chương");
}
$chap_id = intval($_GET['id']);

// 2. Lấy nội dung chương
$sql = "SELECT * FROM novel_chapters WHERE id = $chap_id";
$chapter = $conn->query($sql)->fetch_assoc();

if (!$chapter) {
    die("Chương không tồn tại!");
}

$novel_id = $chapter['novel_id'];

// 3. Lấy thông tin truyện
$novel = $conn->query("SELECT title, cover_image FROM novels WHERE id = $novel_id")->fetch_assoc();

// --- [MỚI] LẤY DANH SÁCH TẤT CẢ CHƯƠNG ĐỂ TẠO DROPDOWN ---
$sql_all = "SELECT id, title FROM novel_chapters WHERE novel_id = $novel_id ORDER BY order_index ASC";
$all_chapters_result = $conn->query($sql_all);
// --------------------------------------------------------

// LƯU LỊCH SỬ
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $type = 'novel';
    $current_url = $_SERVER['REQUEST_URI'];

    $sql_history = "INSERT INTO reading_history (user_id, type, item_id, item_name, item_image, chapter_name, chapter_url) 
                    VALUES (?, ?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    chapter_name = VALUES(chapter_name), 
                    chapter_url = VALUES(chapter_url), 
                    updated_at = NOW()";

    $stmt_hist = $conn->prepare($sql_history);
    $stmt_hist->bind_param("issssss", $uid, $type, $novel_id, $novel['title'], $novel['cover_image'], $chapter['title'], $current_url);
    $stmt_hist->execute();
}

// 4. Tìm chương Trước & Sau
$current_idx = $chapter['order_index'];
$prev = $conn->query("SELECT id FROM novel_chapters WHERE novel_id = $novel_id AND order_index < $current_idx ORDER BY order_index DESC LIMIT 1")->fetch_assoc();
$next = $conn->query("SELECT id FROM novel_chapters WHERE novel_id = $novel_id AND order_index > $current_idx ORDER BY order_index ASC LIMIT 1")->fetch_assoc();

// Link Nav
$prev_link = $prev ? "novel_read.php?id=" . $prev['id'] : '#';
$next_link = $next ? "novel_read.php?id=" . $next['id'] : '#';
$disable_prev = $prev ? '' : 'disabled';
$disable_next = $next ? '' : 'disabled';

require_once 'includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/read.css">

<div class="read-wrapper">
    <div class="read-container">

        <div class="text-center mb-3">
            <h4 class="fw-bold mb-1"><?= htmlspecialchars($chapter['title']) ?></h4>
            <p class="text-muted small">
                Truyện: <a href="novel_detail.php?id=<?= $novel_id ?>" class="text-decoration-none text-secondary fw-bold"><?= htmlspecialchars($novel['title']) ?></a>
                <span class="mx-1">|</span>
                Ngày: <?= date('d/m/Y', strtotime($chapter['created_at'])) ?>
            </p>
        </div>

        <div class="chapter-nav">
            <a href="<?= $prev_link ?>" class="btn btn-secondary btn-nav <?= $disable_prev ?>">⬅ Trước</a>

            <select class="form-select chapter-select" onchange="location = this.value;">
                <?php
                if ($all_chapters_result->num_rows > 0) {
                    while ($row = $all_chapters_result->fetch_assoc()) {
                        $selected = ($row['id'] == $chap_id) ? 'selected' : '';
                        echo '<option value="novel_read.php?id=' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['title']) . '</option>';
                    }
                }
                ?>
            </select>

            <a href="<?= $next_link ?>" class="btn btn-primary btn-nav <?= $disable_next ?>">Sau ➡</a>
        </div>

        <div class="novel-content">
            <?php
            echo nl2br(htmlspecialchars($chapter['content']));
            ?>
        </div>

        <div class="chapter-nav mt-4">
            <a href="<?= $prev_link ?>" class="btn btn-secondary btn-nav <?= $disable_prev ?>">⬅ Trước</a>
            <a href="<?= $next_link ?>" class="btn btn-primary btn-nav <?= $disable_next ?>">Sau ➡</a>
        </div>

        <div class="mt-5">
            <?php
            $cmt_type = 'novel';
            $cmt_obj_id = $novel_id;
            if (file_exists('includes/comment_section.php')) {
                include 'includes/comment_section.php';
            }
            ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>