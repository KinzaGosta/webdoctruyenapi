<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'mod')) {
    header("Location: ../index.php"); exit;
}

// XÁC ĐỊNH URL QUAY LẠI
$is_admin = ($_SESSION['role'] === 'admin');
$back_url = $is_admin ? 'novels.php' : 'mod_novels.php';
$dashboard_url = $is_admin ? 'index.php' : 'mod_index.php';

if (!isset($_GET['novel_id'])) { header("Location: " . $back_url); exit; }
$novel_id = intval($_GET['novel_id']);
$novel = $conn->query("SELECT title FROM novels WHERE id = $novel_id")->fetch_assoc();

if (isset($_GET['delete_id'])) {
    $chap_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM novel_chapters WHERE id = $chap_id");
    header("Location: novel_chapters.php?novel_id=" . $novel_id); exit;
}

$chapters = $conn->query("SELECT * FROM novel_chapters WHERE novel_id = $novel_id ORDER BY order_index DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Chương</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white border-bottom pb-3">
            <span class="fs-4 fw-bold"><i class="fas fa-user-shield"></i> Quản Trị</span>
        </div>
        <ul class="nav nav-pills flex-column mb-auto mt-3">
            <li><a href="<?= $dashboard_url ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="<?= $back_url ?>" class="active"><i class="fas fa-book me-2"></i> Quản lý Truyện</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= $back_url ?>" class="text-decoration-none text-muted mb-2 d-block"><i class="fas fa-arrow-left"></i> Quay lại danh sách truyện</a>
                <h2 class="fw-bold mb-0">📖 <?= htmlspecialchars($novel['title']) ?></h2>
            </div>
            <a href="chapter_add.php?novel_id=<?= $novel_id ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Chương</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th width="80">STT</th><th>Tên chương</th><th>Ngày đăng</th><th class="text-center">Hành động</th></tr></thead>
                    <tbody>
                        <?php if ($chapters->num_rows > 0): ?>
                            <?php while ($row = $chapters->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $row['order_index'] ?></span></td>
                                <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="chapter_edit.php?id=<?= $row['id'] ?>&novel_id=<?= $novel_id ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?novel_id=<?= $novel_id ?>&delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa chương này?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4">Chưa có chương nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>