<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'mod')) {
    header("Location: ../index.php"); exit;
}
$is_admin = ($_SESSION['role'] === 'admin');

if (isset($_GET['delete_id'])) {
    $conn->query("DELETE FROM novels WHERE id = ".intval($_GET['delete_id']));
    header("Location: novels.php"); exit;
}

$sql = "SELECT n.*, GROUP_CONCAT(c.name SEPARATOR ', ') as category_names 
        FROM novels n LEFT JOIN novel_categories nc ON n.id = nc.novel_id LEFT JOIN categories c ON nc.category_id = c.id
        GROUP BY n.id ORDER BY n.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Truyện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <a href="../index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none border-bottom pb-3">
            <span class="fs-4 fw-bold"><i class="fas fa-user-shield"></i> <?= $is_admin ? 'Admin Panel' : 'Mod Panel' ?></span>
        </a>
        <ul class="nav nav-pills flex-column mb-auto mt-3">
            <li><a href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="novels.php" class="active"><i class="fas fa-book me-2"></i> Quản lý Truyện</a></li>
            <li><a href="categories.php"><i class="fas fa-folder me-2"></i> Quản lý Thể loại</a></li>
            <?php if($is_admin): ?>
                <li><a href="manage_users.php"><i class="fas fa-users me-2"></i> Quản lý Thành viên</a></li>
                <li><a href="manage_comments.php"><i class="fas fa-comments me-2"></i> Quản lý Bình luận</a></li>
                <li><a href="notifications.php"><i class="fas fa-bullhorn me-2"></i> Gửi Thông Báo</a></li>
            <?php endif; ?>
            <li class="mt-4 border-top pt-3"></li>
            <li><a href="../index.php" class="text-warning"><i class="fas fa-home me-2"></i> Về trang chủ</a></li>
            <li><a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">📚 Danh sách Truyện</h2>
            <a href="novel_add.php" class="btn btn-success"><i class="fas fa-plus-circle"></i> Thêm Mới</a>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark"><tr><th>ID</th><th>Ảnh</th><th>Tên Truyện</th><th>Thể loại</th><th>Trạng thái</th><th class="text-center">Hành động</th></tr></thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><img src="<?= $row['cover_image'] ?>" class="img-cover"></td>
                            <td><strong><?= htmlspecialchars($row['title']) ?></strong><br><small><?= htmlspecialchars($row['author']) ?></small></td>
                            <td><small><?= $row['category_names'] ?></small></td>
                            <td><span class="badge bg-secondary"><?= $row['status'] ?></span></td>
                            <td class="text-center">
                                <a href="novel_chapters.php?novel_id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white"><i class="fas fa-list"></i></a>
                                <a href="novel_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>