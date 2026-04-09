<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'mod')) {
    header("Location: ../index.php"); exit;
}
$is_admin = ($_SESSION['role'] === 'admin');

if (isset($_POST['add_cat'])) {
    $name = trim($_POST['name']); $slug = createSlug($name);
    if($conn->query("SELECT id FROM categories WHERE slug='$slug'")->num_rows == 0) {
        $conn->query("INSERT INTO categories (name, slug) VALUES ('$name', '$slug')");
        header("Location: categories.php"); exit;
    }
}
if (isset($_GET['del'])) {
    $conn->query("DELETE FROM categories WHERE id=".intval($_GET['del']));
    header("Location: categories.php"); exit;
}
$cats = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thể Loại</title>
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
            <li><a href="novels.php"><i class="fas fa-book me-2"></i> Quản lý Truyện</a></li>
            <li><a href="categories.php" class="active"><i class="fas fa-folder me-2"></i> Quản lý Thể loại</a></li>
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
        <h2 class="fw-bold mb-4 text-dark">📂 Quản lý Thể Loại</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white fw-bold">Thêm Thể Loại</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3"><label>Tên thể loại</label><input type="text" name="name" class="form-control" required></div>
                            <button type="submit" name="add_cat" class="btn btn-success w-100">Lưu</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-dark"><tr><th>ID</th><th>Tên</th><th>Slug</th><th class="text-center">Hành động</th></tr></thead>
                            <tbody>
                                <?php while($row = $cats->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><strong><?= $row['name'] ?></strong></td>
                                    <td><code><?= $row['slug'] ?></code></td>
                                    <td class="text-center"><a href="?del=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</a></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>