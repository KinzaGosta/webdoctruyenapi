<?php
session_start();
require_once '../config/database.php';

// Check quyền
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('<div class="container mt-5 alert alert-danger">Bạn không có quyền truy cập trang này!</div>');
}

// =================================================================
// XỬ LÝ FORM
// =================================================================
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // A. THÊM
    if ($action == 'add') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $check = $conn->query("SELECT id FROM users WHERE username='$username' OR email='$email'");
        if ($check->num_rows > 0) {
            $msg = "<div class='alert alert-danger'>Tên đăng nhập hoặc Email đã tồn tại!</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
            $stmt->bind_param("ssss", $username, $email, $password, $role);
            if ($stmt->execute()) $msg = "<div class='alert alert-success'>Thêm thành công!</div>";
        }
    }
    // B. SỬA
    elseif ($action == 'edit') {
        $id = intval($_POST['user_id']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = $_POST['role'];
        
        $sql_pass = "";
        if (!empty($_POST['password'])) {
            $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql_pass = ", password='$new_pass'";
        }

        $conn->query("UPDATE users SET username='$username', email='$email', role='$role' $sql_pass WHERE id=$id");
        $msg = "<div class='alert alert-success'>Cập nhật thành công!</div>";
    }
    // C. KHÓA/MỞ
    elseif ($action == 'toggle_ban') {
        $id = intval($_POST['user_id']);
        if ($id == $_SESSION['user_id']) {
            $msg = "<div class='alert alert-warning'>Không thể tự khóa chính mình!</div>";
        } else {
            $new_status = ($_POST['current_status'] == 'active') ? 'banned' : 'active';
            $conn->query("UPDATE users SET status='$new_status' WHERE id=$id");
            $msg = "<div class='alert alert-success'>Đã thay đổi trạng thái!</div>";
        }
    }
    // D. XÓA
    elseif ($action == 'delete') {
        $id = intval($_POST['user_id']);
        if ($id == $_SESSION['user_id']) {
            $msg = "<div class='alert alert-warning'>Không thể tự xóa chính mình!</div>";
        } else {
            $conn->query("DELETE FROM users WHERE id=$id");
            $msg = "<div class='alert alert-success'>Đã xóa thành viên!</div>";
        }
    }
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý thành viên</title>
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
            <li><a href="manage_users.php" class="active"><i class="fas fa-users me-2"></i> Quản lý Thành viên</a></li>
            <li><a href="manage_comments.php"><i class="fas fa-comments me-2"></i> Quản lý Bình luận</a></li>
            <li><a href="notifications.php"><i class="fas fa-bullhorn me-2"></i> Gửi Thông Báo</a></li>
            <li><a href="../index.php" class="mt-5 text-warning"><i class="fas fa-home me-2"></i> Về trang chủ</a></li>
            <li><a href="../logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
        </ul>
    </div>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">👤 Quản lý Thành Viên</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> Thêm Mới
            </button>
        </div>

        <?= $msg ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">ID</th>
                                <th width="80" class="text-center">Avatar</th>
                                <th>Thông tin</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th width="150">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php 
                                        $avatar = 'https://ui-avatars.com/api/?name='.$row['username'];
                                        if (!empty($row['avatar'])) {
                                            $avatar = (strpos($row['avatar'], 'http') === 0) ? $row['avatar'] : '../' . $row['avatar'];
                                        }
                                        
                                        $role_badge = match($row['role']) { 'admin'=>'bg-danger', 'mod'=>'bg-warning text-dark', default=>'bg-secondary' };
                                        $status_badge = ($row['status'] == 'active') ? 'bg-success' : 'bg-dark';
                                    ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        
                                        <td class="text-center">
                                            <a href="../profile.php?id=<?= $row['id'] ?>" target="_blank" title="Xem hồ sơ">
                                                <img src="<?= $avatar ?>" class="rounded-circle avatar-img avatar-md">
                                            </a>
                                        </td>

                                        <td>
                                            <a href="../profile.php?id=<?= $row['id'] ?>" target="_blank" class="text-decoration-none text-dark fw-bold">
                                                <?= htmlspecialchars($row['username']) ?>
                                            </a>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                                        </td>

                                        <td><span class="badge <?= $role_badge ?>"><?= strtoupper($row['role']) ?></span></td>
                                        <td><span class="badge <?= $status_badge ?>"><?= ucfirst($row['status']) ?></span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info text-white" onclick='openEdit(<?= json_encode($row) ?>)' title="Sửa"><i class="fas fa-edit"></i></button>
                                            
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Đổi trạng thái user này?');">
                                                <input type="hidden" name="action" value="toggle_ban">
                                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="current_status" value="<?= $row['status'] ?>">
                                                <button class="btn btn-sm <?= ($row['status']=='active')?'btn-dark':'btn-success' ?>" title="Khóa/Mở khóa">
                                                    <?= ($row['status']=='active')?'<i class="fas fa-ban"></i>':'<i class="fas fa-unlock"></i>' ?>
                                                </button>
                                            </form>

                                            <form method="POST" class="d-inline" onsubmit="return confirm('Xóa vĩnh viễn user này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                                <button class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">Chưa có thành viên nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1"><div class="modal-dialog"><form method="POST" class="modal-content"><div class="modal-header"><h5 class="modal-title">Thêm User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="action" value="add"><div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div><div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div><div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div><div class="mb-3"><label>Role</label><select name="role" class="form-select"><option value="user">User</option><option value="mod">Mod</option><option value="admin">Admin</option></select></div></div><div class="modal-footer"><button class="btn btn-primary">Lưu</button></div></form></div></div>

<div class="modal fade" id="editUserModal" tabindex="-1"><div class="modal-dialog"><form method="POST" class="modal-content"><div class="modal-header"><h5 class="modal-title">Sửa User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="action" value="edit"><input type="hidden" name="user_id" id="edit_id"><div class="mb-3"><label>Username</label><input type="text" name="username" id="edit_username" class="form-control" required></div><div class="mb-3"><label>Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div><div class="mb-3"><label>Password Mới (để trống nếu không đổi)</label><input type="password" name="password" class="form-control"></div><div class="mb-3"><label>Role</label><select name="role" id="edit_role" class="form-select"><option value="user">User</option><option value="mod">Mod</option><option value="admin">Admin</option></select></div></div><div class="modal-footer"><button class="btn btn-primary">Cập nhật</button></div></form></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openEdit(user) {
        document.getElementById('edit_id').value = user.id;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }
</script>

</body>
</html>