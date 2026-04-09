<?php
session_start();
require_once '../config/database.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); exit;
}

$user_id = $_SESSION['user_id'];

// 2. LẤY QUYỀN
$stmt = $conn->prepare("SELECT username, role, avatar FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_user = $stmt->get_result()->fetch_assoc();

if (!$current_user || ($current_user['role'] !== 'admin' && $current_user['role'] !== 'mod')) {
    header("Location: ../index.php"); exit;
}

$_SESSION['role'] = $current_user['role'];
$_SESSION['username'] = $current_user['username'];
$role = $current_user['role'];
$is_admin = ($role === 'admin'); 

// 3. SỐ LIỆU
if ($is_admin) {
    $count_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
    $count_novels = $conn->query("SELECT COUNT(*) as total FROM novels")->fetch_assoc()['total'];
    $count_comments = $conn->query("SELECT COUNT(*) as total FROM comments")->fetch_assoc()['total'];
    $view_novel = $conn->query("SELECT SUM(views) as total FROM novels")->fetch_assoc()['total'] ?? 0;
    $view_comic = $conn->query("SELECT SUM(view_count) as total FROM comic_views")->fetch_assoc()['total'] ?? 0;
    $total_views = $view_novel + $view_comic;
    
    $sql_reports = "SELECT n.*, u.username, u.avatar FROM notifications n JOIN users u ON n.sender_id = u.id WHERE n.receiver_id = $user_id AND n.type = 'report' AND n.is_read = 0 ORDER BY n.created_at DESC LIMIT 10";
    $reports = $conn->query($sql_reports);
} else {
    $count_novels = $conn->query("SELECT COUNT(*) as total FROM novels")->fetch_assoc()['total'];
    $count_cats = $conn->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
    $count_my_novels = $conn->query("SELECT COUNT(*) as total FROM novels WHERE posted_by = $user_id")->fetch_assoc()['total'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $is_admin ? 'Admin' : 'Mod' ?> Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <a href="../index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none border-bottom pb-3">
            <span class="fs-4 fw-bold"><i class="fas fa-user-shield me-2"></i> <?= $is_admin ? 'Admin Panel' : 'Mod Panel' ?></span>
        </a>
        
        <ul class="nav nav-pills flex-column mb-auto mt-3">
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="novels.php"><i class="fas fa-book me-2"></i> Quản lý Truyện</a></li>
            <li><a href="categories.php"><i class="fas fa-folder me-2"></i> Quản lý Thể loại</a></li>
            
            <?php if ($is_admin): ?>
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
            <h2 class="fw-bold text-dark">Tổng Quan Hệ Thống</h2>
            <span class="badge bg-secondary p-2">Xin chào: <?= htmlspecialchars($current_user['username']) ?></span>
        </div>

        <div class="row g-4 mb-5">
            <?php if ($is_admin): ?>
                <div class="col-md-3"><div class="card card-stat bg-gradient-1"><div class="card-body"><h6 class="text-uppercase mb-2">Thành Viên</h6><h2 class="fw-bold"><?= number_format($count_users) ?></h2><i class="fas fa-users stat-icon"></i></div></div></div>
                <div class="col-md-3"><div class="card card-stat bg-gradient-2"><div class="card-body"><h6 class="text-uppercase mb-2">Truyện Chữ</h6><h2 class="fw-bold"><?= number_format($count_novels) ?></h2><i class="fas fa-book stat-icon"></i></div></div></div>
                <div class="col-md-3"><div class="card card-stat bg-gradient-3"><div class="card-body"><h6 class="text-uppercase mb-2">Tổng Lượt Xem</h6><h2 class="fw-bold"><?= number_format($total_views) ?></h2><i class="fas fa-eye stat-icon"></i></div></div></div>
                <div class="col-md-3"><div class="card card-stat bg-gradient-4"><div class="card-body"><h6 class="text-uppercase mb-2">Bình Luận</h6><h2 class="fw-bold"><?= number_format($count_comments) ?></h2><i class="fas fa-comments stat-icon"></i></div></div></div>
            <?php else: ?>
                <div class="col-md-4"><div class="card card-stat bg-gradient-1 h-100"><div class="card-body"><h6 class="text-uppercase mb-2">Tổng Số Truyện</h6><h2 class="fw-bold"><?= number_format($count_novels) ?></h2><i class="fas fa-book stat-icon"></i></div></div></div>
                <div class="col-md-4"><div class="card card-stat bg-gradient-2 h-100"><div class="card-body"><h6 class="text-uppercase mb-2">Tổng Thể Loại</h6><h2 class="fw-bold"><?= number_format($count_cats) ?></h2><i class="fas fa-tags stat-icon"></i></div></div></div>
                <div class="col-md-4"><div class="card card-stat bg-gradient-3 h-100"><div class="card-body"><h6 class="text-uppercase mb-2">Truyện Của Bạn</h6><h2 class="fw-bold"><?= number_format($count_my_novels) ?></h2><i class="fas fa-user-edit stat-icon"></i></div></div></div>
            <?php endif; ?>
        </div>

        <?php if ($is_admin && $reports->num_rows > 0): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom"><h5 class="mb-0 fw-bold text-danger">Báo Lỗi Mới</h5></div>
                <div class="card-body p-0"><div class="list-group list-group-flush">
                    <?php while($rp = $reports->fetch_assoc()): ?>
                        <div class="list-group-item p-3" id="report-<?= $rp['id'] ?>">
                            <div class="d-flex justify-content-between">
                                <strong><?= htmlspecialchars($rp['title']) ?></strong>
                                <button class="btn btn-sm btn-success" onclick="markDone(<?= $rp['id'] ?>)">Đã xử lý</button>
                            </div>
                            <p class="mb-0 text-muted small"><?= htmlspecialchars($rp['message']) ?></p>
                        </div>
                    <?php endwhile; ?>
                </div></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($is_admin): ?>
<script>
function markDone(id) {
    if(!confirm('Đã xử lý xong?')) return;
    let fd = new FormData(); fd.append('action', 'mark_read'); fd.append('id', id);
    fetch('../ajax_notification.php', { method: 'POST', body: fd }).then(r => r.json()).then(d => { if(d.status==='success') document.getElementById('report-'+id).remove(); });
}
</script>
<?php endif; ?>
</body>
</html>