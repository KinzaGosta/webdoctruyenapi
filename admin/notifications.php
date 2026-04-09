<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Bạn không có quyền truy cập.");
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target = $_POST['target'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $specific_id = intval($_POST['user_id'] ?? 0);

    if (empty($title) || empty($content)) {
        $msg = "<div class='alert alert-danger'>Vui lòng nhập đủ thông tin!</div>";
    } else {
        $sql = "";
        if ($target == 'all') $sql = "SELECT id FROM users";
        elseif ($target == 'mods') $sql = "SELECT id FROM users WHERE role = 'mod'";
        elseif ($target == 'admins') $sql = "SELECT id FROM users WHERE role = 'admin'";
        elseif ($target == 'specific' && $specific_id > 0) $sql = "SELECT id FROM users WHERE id = $specific_id";

        if ($sql) {
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, type, title, message) VALUES (NULL, ?, 'system', ?, ?)");
                $c = 0;
                while ($row = $result->fetch_assoc()) {
                    $stmt->bind_param("iss", $row['id'], $title, $content); $stmt->execute(); $c++;
                }
                $msg = "<div class='alert alert-success'>Đã gửi cho <b>$c</b> người!</div>";
            } else { $msg = "<div class='alert alert-warning'>Không tìm thấy người nhận.</div>"; }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Gửi Thông Báo</title>
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
            <li><a href="manage_comments.php"><i class="fas fa-comments me-2"></i> Quản lý Bình luận</a></li>
            <li><a href="notifications.php" class="active"><i class="fas fa-bullhorn me-2"></i> Gửi Thông Báo</a></li>
            <li><a href="../index.php" class="mt-5 text-warning"><i class="fas fa-home me-2"></i> Về trang chủ</a></li>
            <li><a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="container-fluid p-4">
        <h2 class="fw-bold mb-4 text-dark">📢 Gửi Thông Báo Hệ Thống</h2>
        <?= $msg ?>
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Gửi đến:</label>
                            <select name="target" class="form-select" onchange="toggleId(this.value)">
                                <option value="all">🌐 Tất cả thành viên</option>
                                <option value="mods">🛡️ Chỉ Moderators</option>
                                <option value="admins">👑 Chỉ Admins</option>
                                <option value="specific">👤 Thành viên cụ thể</option>
                            </select>
                            <input type="number" name="user_id" id="input-userid" class="form-control mt-2 d-none" placeholder="Nhập ID User...">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Tiêu đề:</label>
                            <input type="text" name="title" class="form-control" placeholder="Vd: Bảo trì hệ thống" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nội dung:</label>
                        <textarea name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary px-5"><i class="fas fa-paper-plane"></i> Gửi ngay</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleId(val) {
        document.getElementById('input-userid').classList.toggle('d-none', val !== 'specific');
    }
</script>
</body>
</html>