<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'mod')) {
    header("Location: ../index.php"); exit;
}

// SETUP SIDEBAR
$is_admin = ($_SESSION['role'] === 'admin');
$back_url_novels = $is_admin ? 'novels.php' : 'mod_novels.php';
$dashboard_url = $is_admin ? 'index.php' : 'mod_index.php';

if (!isset($_GET['id']) || !isset($_GET['novel_id'])) { die("Thiếu thông tin!"); }
$chap_id = intval($_GET['id']);
$novel_id = intval($_GET['novel_id']);

$stmt = $conn->prepare("SELECT * FROM novel_chapters WHERE id = ?");
$stmt->bind_param("i", $chap_id);
$stmt->execute();
$chapter = $stmt->get_result()->fetch_assoc();
if (!$chapter) { die("Chương không tồn tại!"); }

$novel = $conn->query("SELECT title FROM novels WHERE id = $novel_id")->fetch_assoc();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $order = intval($_POST['order_index']);
    $slug = createSlug($title);

    $sql = "UPDATE novel_chapters SET title = ?, slug = ?, content = ?, order_index = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $title, $slug, $content, $order, $chap_id);

    if ($stmt->execute()) {
        $conn->query("UPDATE novels SET updated_at = NOW() WHERE id = $novel_id");
        echo "<script>alert('Cập nhật thành công!'); window.location.href='novel_chapters.php?novel_id=$novel_id';</script>";
    } else {
        $msg = "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Chương</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white border-bottom pb-3"><span class="fs-4 fw-bold"><i class="fas fa-user-shield"></i> Quản Trị</span></div>
        <ul class="nav nav-pills flex-column mb-auto mt-3">
            <li><a href="<?= $dashboard_url ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="<?= $back_url_novels ?>" class="active"><i class="fas fa-book me-2"></i> Quản lý Truyện</a></li>
            <?php if($is_admin): ?>
                <li><a href="categories.php"><i class="fas fa-folder me-2"></i> Quản lý Thể loại</a></li>
                <li><a href="manage_users.php"><i class="fas fa-users me-2"></i> Quản lý Thành viên</a></li>
                <li><a href="manage_comments.php"><i class="fas fa-comments me-2"></i> Quản lý Bình luận</a></li>
                <li><a href="notifications.php"><i class="fas fa-bullhorn me-2"></i> Gửi Thông Báo</a></li>
            <?php else: ?>
                <li><a href="mod_categories.php"><i class="fas fa-folder me-2"></i> Quản lý Thể loại</a></li>
            <?php endif; ?>
            <li class="mt-4 border-top pt-3"></li>
            <li><a href="../index.php" class="text-warning"><i class="fas fa-home me-2"></i> Về trang chủ</a></li>
            <li><a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="container-fluid p-4">
        <h2 class="fw-bold mb-4">✏️ Sửa Chương: <span class="text-secondary"><?= htmlspecialchars($novel['title']) ?></span></h2>
        <?php if($msg): ?><div class="alert alert-danger"><?= $msg ?></div><?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-bold">STT Chương</label>
                            <input type="number" name="order_index" class="form-control" value="<?= $chapter['order_index'] ?>" required>
                        </div>
                        <div class="col-md-9 mb-3">
                            <label class="form-label fw-bold">Tên Chương</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($chapter['title']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung</label>
                        <textarea name="content" class="form-control" rows="15" required><?= htmlspecialchars($chapter['content']) ?></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4">💾 Lưu Thay Đổi</button>
                        <a href="novel_chapters.php?novel_id=<?= $novel_id ?>" class="btn btn-secondary">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>