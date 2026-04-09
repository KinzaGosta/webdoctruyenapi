<?php
session_start();
require_once '../config/database.php';

// Check quyền
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('<div class="container mt-5 alert alert-danger">Bạn không có quyền truy cập trang này!</div>');
}

// Xóa bình luận
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $conn->query("DELETE FROM comments WHERE id = $id");
    header("Location: manage_comments.php"); exit;
}

// Lấy danh sách bình luận (Join với users để lấy thông tin người gửi)
$sql = "SELECT c.*, u.username, u.avatar, u.role 
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.created_at DESC LIMIT 50";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Bình Luận</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <a href="../index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none border-bottom pb-3">
            <span class="fs-4 fw-bold"><i class="fas fa-user-shield"></i> Admin Panel</span>
        </a>
        <ul class="nav nav-pills flex-column mb-auto mt-3">
            <li><a href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="novels.php"><i class="fas fa-book me-2"></i> Quản lý Truyện</a></li>
            <li><a href="categories.php"><i class="fas fa-folder me-2"></i> Quản lý Thể loại</a></li>
            <li><a href="manage_users.php"><i class="fas fa-users me-2"></i> Quản lý Thành viên</a></li>
            <li><a href="manage_comments.php" class="active"><i class="fas fa-comments me-2"></i> Quản lý Bình luận</a></li>
            <li><a href="notifications.php"><i class="fas fa-bullhorn me-2"></i> Gửi Thông Báo</a></li>
            <li><a href="../index.php" class="mt-5 text-warning"><i class="fas fa-home me-2"></i> Về trang chủ</a></li>
            <li><a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">💬 Quản lý Bình Luận</h2>
        </div>
        
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">ID</th>
                                <th width="220">Người gửi</th>
                                <th>Nội dung</th>
                                <th width="200">Nơi bình luận</th>
                                <th width="150">Ngày giờ</th>
                                <th width="100" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php 
                                        $avt = 'https://ui-avatars.com/api/?name='.$row['username'];
                                        if (!empty($row['avatar'])) {
                                            $avt = (strpos($row['avatar'], 'http') === 0) ? $row['avatar'] : '../' . $row['avatar'];
                                        }

                                        $location = '<span class="text-muted">Không rõ</span>';
                                        $link = "#";
                                        
                                        if ($row['novel_id']) {
                                            $location = '<span class="badge bg-primary">Truyện Chữ</span> ID: ' . $row['novel_id'];
                                            $link = "../novel_detail.php?id=" . $row['novel_id'];
                                        } elseif ($row['comic_slug']) {
                                            $location = '<span class="badge bg-warning text-dark">Truyện Tranh</span>';
                                            $link = "../comic_detail.php?slug=" . $row['comic_slug'];
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="../profile.php?id=<?= $row['user_id'] ?>" target="_blank" title="Xem hồ sơ">
                                                    <img src="<?= $avt ?>" class="rounded-circle me-2 avatar-img avatar-sm">
                                                </a>
                                                
                                                <div>
                                                    <a href="../profile.php?id=<?= $row['user_id'] ?>" target="_blank" class="text-decoration-none text-dark fw-bold">
                                                        <?= htmlspecialchars($row['username']) ?>
                                                    </a>
                                                    <?php if($row['role'] === 'admin'): ?>
                                                        <small class="d-block text-danger text-xs">Quản trị viên</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="text-break text-limit"><?= htmlspecialchars($row['content']) ?></div>
                                        </td>
                                        <td>
                                            <a href="<?= $link ?>" target="_blank" class="text-decoration-none text-secondary small">
                                                <?= $location ?> <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        </td>
                                        <td class="text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="?del=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa vĩnh viễn bình luận này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Chưa có bình luận nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>