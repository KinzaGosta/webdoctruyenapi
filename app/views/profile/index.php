<?php
// File: index.php?route=profile/index
require_once 'config/database.php';
require_once 'app/views/includes/header.php';

// 1. XÁC ĐỊNH NGƯỜI ĐANG XEM VÀ HỒ SƠ CẦN XEM
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Nếu có ?id=... trên URL thì xem của người đó, nếu không thì xem của chính mình
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $profile_id = intval($_GET['id']);
} else {
    $profile_id = $current_user_id;
}

// Nếu không xác định được ID nào (Chưa đăng nhập & không truyền ID) -> Về Login
if ($profile_id == 0) {
    echo "<script>location.href='index.php?route=auth/login';</script>";
    exit;
}

// 2. KIỂM TRA QUYỀN CHỦ SỞ HỮU
$is_owner = ($current_user_id === $profile_id);

// 3. LẤY THÔNG TIN USER
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<div class='container mt-5 alert alert-danger'>Thành viên không tồn tại! <a href='index.php'>Về trang chủ</a></div>";
    require_once 'app/views/includes/footer.php';
    exit;
}

$msg = '';
$msg_type = '';

// 4. XỬ LÝ POST (CHỈ CHẠY NẾU LÀ CHÍNH CHỦ)
if ($is_owner && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // A. Upload Avatar (File)
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); } 
        $ext = pathinfo($_FILES["avatar_file"]["name"], PATHINFO_EXTENSION);
        $new_name = "avatar_" . $profile_id . "_" . time() . "." . $ext;
        $target = $target_dir . $new_name;
        
        if(getimagesize($_FILES["avatar_file"]["tmp_name"]) !== false && move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $target)) {
            $conn->query("UPDATE users SET avatar = '$target' WHERE id = $profile_id");
            $_SESSION['avatar'] = $target; 
            $user['avatar'] = $target;
            $msg = "Đổi ảnh thành công!"; $msg_type = "success";
        } else { $msg = "Lỗi upload ảnh."; $msg_type = "danger"; }
    
    // B. Upload Avatar (Link)
    } elseif (isset($_POST['avatar_url']) && !empty($_POST['avatar_url'])) {
        $url = trim($_POST['avatar_url']);
        $conn->query("UPDATE users SET avatar = '$url' WHERE id = $profile_id");
        $_SESSION['avatar'] = $url;
        $user['avatar'] = $url;
        $msg = "Đã cập nhật ảnh từ link!"; $msg_type = "success";

    // C. Đổi Thông Tin
    } elseif (isset($_POST['update_profile'])) {
        $name = trim($_POST['username']);
        $email = trim($_POST['email']);
        $check = $conn->query("SELECT id FROM users WHERE (username = '$name' OR email = '$email') AND id != $profile_id");
        if ($check->num_rows > 0) { $msg = "Tên hoặc Email đã tồn tại!"; $msg_type = "danger"; }
        else {
            $conn->query("UPDATE users SET username = '$name', email = '$email' WHERE id = $profile_id");
            $_SESSION['username'] = $name;
            $user['username'] = $name; $user['email'] = $email;
            $msg = "Cập nhật thành công!"; $msg_type = "success";
        }

    // D. Đổi Mật Khẩu
    } elseif (isset($_POST['change_pass'])) {
        $curr = $_POST['current_password'];
        $new = $_POST['new_password'];
        $cf = $_POST['confirm_password'];
        if (!password_verify($curr, $user['password'])) { $msg = "Mật khẩu cũ sai!"; $msg_type = "danger"; }
        elseif ($new !== $cf) { $msg = "Mật khẩu xác nhận không khớp!"; $msg_type = "danger"; }
        else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password = '$hash' WHERE id = $profile_id");
            $msg = "Đổi mật khẩu thành công!"; $msg_type = "success";
        }
    }
}

// 5. LẤY DỮ LIỆU HIỂN THỊ
// Tủ truyện
$res_novel = $conn->query("SELECT n.id, n.title, n.cover_image FROM novel_favorites f JOIN novels n ON f.novel_id = n.id WHERE f.user_id = $profile_id ORDER BY f.created_at DESC");
$res_comic = $conn->query("SELECT comic_slug, comic_name, comic_thumb FROM comic_favorites WHERE user_id = $profile_id ORDER BY created_at DESC");

// Lịch sử đọc
$res_history = null;
if ($conn->query("SHOW TABLES LIKE 'reading_history'")->num_rows > 0) {
    // Lấy 12 mục gần nhất
    $res_history = $conn->query("SELECT * FROM reading_history WHERE user_id = $profile_id ORDER BY updated_at DESC LIMIT 12");
}

// Bình luận
$sql_cmt = "SELECT c.*, n.title as novel_title FROM comments c LEFT JOIN novels n ON c.novel_id = n.id WHERE c.user_id = $profile_id ORDER BY c.created_at DESC LIMIT 10";
$res_comments = $conn->query($sql_cmt);
?>

<link rel="stylesheet" href="assets/css/profile.css">
<link rel="stylesheet" href="assets/css/custom.css">

<?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show container mt-3"><?= $msg ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body text-center pt-4">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?= $user['avatar'] ? $user['avatar'] : 'https://ui-avatars.com/api/?name='.$user['username'] ?>" 
                             class="rounded-circle shadow profile-avatar" id="avatar-preview">
                        
                    <?php if ($is_owner): ?>
                        <label for="avatar-input" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow-sm profile-upload-btn" title="Đổi ảnh">
                            <i class="fas fa-camera"></i>
                        </label>
                    <?php endif; ?>
                </div>

                <?php if ($is_owner): ?>
                    <form method="POST" enctype="multipart/form-data" id="avatar-form">
                        <input type="file" name="avatar_file" id="avatar-input" class="d-none" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                    </form>
                    <div class="mb-3">
                        <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#urlInput">
                            <i class="fas fa-link"></i> Dùng link ảnh
                        </button>
                        <div class="collapse mt-2" id="urlInput">
                            <form method="POST">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="avatar_url" class="form-control" placeholder="https://..." required>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="mt-2 mb-3">
                            <a href="index.php?route=chat/index&uid=<?= $profile_id ?>" class="btn btn-primary w-100 fw-bold">
                                <i class="fas fa-comment-dots"></i> Nhắn tin
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                    
                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['username']) ?></h4>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div class="mb-3">
                        <?php if($user['role'] === 'admin'): ?><span class="badge bg-danger">🛡️ Quản Trị Viên</span>
                        <?php elseif($user['role'] === 'mod'): ?><span class="badge bg-warning text-dark">🛡️ Moderator</span>
                        <?php else: ?><span class="badge bg-primary">👤 Thành Viên</span><?php endif; ?>
                        
                        <?php if($user['status'] === 'banned'): ?><span class="badge bg-dark ms-1">🚫 Banned</span><?php endif; ?>
                    </div>

                    <ul class="list-group list-group-flush text-start small mb-3">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>📅 Tham gia:</span>
                            <span class="fw-bold"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                        </li>
                    </ul>

                    <?php if ($is_owner): ?>
                        <hr>
                        <div class="d-grid"><a href="index.php?route=auth/logout" class="btn btn-outline-danger btn-sm">Đăng xuất</a></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item"><button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#history-tab">🕒 Lịch sử</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#novel-tab">📝 Tủ Truyện Chữ</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#comic-tab">🖼️ Tủ Truyện Tranh</button></li>
                        <li class="nav-item"><button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#comments-tab">💬 Bình luận</button></li>
                        
                        <?php if ($is_owner): ?>
                            <li class="nav-item"><button class="nav-link fw-bold text-danger" data-bs-toggle="tab" data-bs-target="#settings-tab">⚙️ Cài đặt</button></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        
                        <div class="tab-pane fade show active" id="history-tab">
                            <?php if ($res_history && $res_history->num_rows > 0): ?>
                                
                                <?php if ($is_owner): ?>
                                    <div class="d-flex justify-content-end mb-3">
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteAllHistory()">
                                            <i class="fas fa-trash"></i> Xóa tất cả lịch sử
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <div class="row g-3">
                                    <?php while ($row = $res_history->fetch_assoc()): ?>
                                        <div class="col-6 col-md-3">
                                            <div class="card h-100 shadow-sm border-0 position-relative">
                                                <?php if ($is_owner): ?>
                                                    <button class="position-absolute top-0 end-0 btn btn-sm btn-danger m-1 py-0 px-2 rounded-circle shadow-sm btn-remove-item" onclick="removeHistory(<?= $row['id'] ?>, this)">&times;</button>
                                                <?php endif; ?>
                                                
                                                <a href="<?= $row['chapter_url'] ?>">
                                                    <img src="<?= $row['item_image'] ?>" class="card-img-top rounded profile-cover-img" onerror="this.src='https://via.placeholder.com/150'">
                                                </a>
                                                <div class="card-body p-2">
                                                    <h6 class="text-truncate mb-1"><a href="<?= $row['chapter_url'] ?>" class="text-dark text-decoration-none fw-bold"><?= htmlspecialchars($row['item_name']) ?></a></h6>
                                                    <small class="d-block text-primary mb-1 text-truncate"><?= htmlspecialchars($row['chapter_name']) ?></small>
                                                    <small class="text-muted text-timestamp"><?= date('d/m H:i', strtotime($row['updated_at'])) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">Chưa có lịch sử đọc.</div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="novel-tab">
                            <?php if ($res_novel->num_rows > 0): ?>
                                <div class="row g-3">
                                    <?php while ($row = $res_novel->fetch_assoc()): ?>
                                        <div class="col-6 col-md-3">
                                            <div class="card h-100 shadow-sm border-0">
                                                <a href="index.php?route=novel/detail&id=<?= $row['id'] ?>">
                                                    <img src="<?= $row['cover_image'] ?>" class="card-img-top rounded profile-cover-img">
                                                </a>
                                                <div class="card-body p-2 text-center">
                                                    <h6 class="text-truncate mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                                                    <?php if ($is_owner): ?>
                                                        <button class="btn btn-sm btn-outline-danger border-0 py-0" onclick="removeFav('novel', <?= $row['id'] ?>, this)">Bỏ theo dõi</button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">Tủ truyện trống.</div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="comic-tab">
                            <?php if ($res_comic->num_rows > 0): ?>
                                <div class="row g-3">
                                    <?php while ($row = $res_comic->fetch_assoc()): ?>
                                        <div class="col-6 col-md-3">
                                            <div class="card h-100 shadow-sm border-0">
                                                <a href="index.php?route=comic/detail&slug=<?= $row['comic_slug'] ?>">
                                                    <img src="<?= $row['comic_thumb'] ?>" class="card-img-top rounded profile-cover-img">
                                                </a>
                                                <div class="card-body p-2 text-center">
                                                    <h6 class="text-truncate mb-1"><?= htmlspecialchars($row['comic_name']) ?></h6>
                                                    <?php if ($is_owner): ?>
                                                        <button class="btn btn-sm btn-outline-danger border-0 py-0" onclick="removeFav('comic', '<?= $row['comic_slug'] ?>', this)">Bỏ theo dõi</button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">Tủ truyện trống.</div>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="comments-tab">
                            <?php if ($res_comments && $res_comments->num_rows > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while ($cmt = $res_comments->fetch_assoc()): ?>
                                        <?php
                                            $link = $cmt['novel_id'] ? "index.php?route=novel/detail&id=".$cmt['novel_id'] : "index.php?route=comic/detail&slug=".$cmt['comic_slug'];
                                            $name = $cmt['novel_title'] ? $cmt['novel_title'] : "Truyện tranh: ".$cmt['comic_slug'];
                                        ?>
                                        <div class="list-group-item px-0 py-3 position-relative" id="cmt-item-<?= $cmt['id'] ?>">
                                            
                                            <?php if ($is_owner): ?>
                                                <button class="btn btn-sm text-danger position-absolute top-0 end-0 mt-2" 
                                                        onclick="deleteCommentProfile(<?= $cmt['id'] ?>)" 
                                                        title="Xóa bình luận này">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>

                                            <div class="d-flex justify-content-between mb-1 pe-4">
                                                <small class="text-muted">Tại: <a href="<?= $link ?>" class="fw-bold text-decoration-none"><?= htmlspecialchars($name) ?></a></small>
                                                <small class="text-secondary"><?= date('d/m/Y H:i', strtotime($cmt['created_at'])) ?></small>
                                            </div>
                                            <p class="mb-0 text-dark fst-italic">"<?= htmlspecialchars($cmt['content']) ?>"</p>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 text-muted">Chưa có bình luận nào.</div>
                            <?php endif; ?>
                        </div>

                        <?php if ($is_owner): ?>
                            <div class="tab-pane fade" id="settings-tab">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm bg-light">
                                            <div class="card-body">
                                                <h6 class="fw-bold text-primary mb-3">Thông tin cá nhân</h6>
                                                <form method="POST">
                                                    <div class="mb-3"><label class="small text-muted">Tên hiển thị</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required></div>
                                                    <div class="mb-3"><label class="small text-muted">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required></div>
                                                    <button type="submit" name="update_profile" class="btn btn-primary w-100">Lưu thay đổi</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm bg-light">
                                            <div class="card-body">
                                                <h6 class="fw-bold text-danger mb-3">Đổi mật khẩu</h6>
                                                <form method="POST">
                                                    <div class="mb-2"><input type="password" name="current_password" class="form-control" placeholder="Mật khẩu cũ" required></div>
                                                    <div class="mb-2"><input type="password" name="new_password" class="form-control" placeholder="Mật khẩu mới" required></div>
                                                    <div class="mb-3"><input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu mới" required></div>
                                                    <button type="submit" name="change_pass" class="btn btn-danger w-100">Xác nhận đổi</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Xóa 1 truyện khỏi tủ
function removeFav(type, idOrSlug, btn) {
    if(!confirm('Xóa truyện này khỏi tủ?')) return;
    let fd = new FormData();
    fd.append('type', type);
    if(type === 'novel') fd.append('id', idOrSlug); else fd.append('slug', idOrSlug);
    fd.append('action', 'toggle_favorite');
    fetch('api/user.php', { method: 'POST', body: fd }).then(r => r.json()).then(data => { if(data.status === 'removed') btn.closest('.col-6').remove(); });
}

// Xóa 1 dòng lịch sử
function removeHistory(id, btn) {
    if(!confirm('Xóa khỏi lịch sử?')) return;
    let fd = new FormData();
    fd.append('action', 'history_delete_one');
    fd.append('id', id);
    fetch('api/user.php', { method: 'POST', body: fd }).then(r => r.json()).then(data => { if(data.status === 'success') btn.closest('.col-6').remove(); });
}

// Xóa comment tại profile
function deleteCommentProfile(cmtId) {
    if (!confirm('Bạn có chắc muốn xóa bình luận này không?')) return;
    let fd = new FormData();
    fd.append('action', 'delete');
    fd.append('cmt_id', cmtId);
    fetch('api/comments.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            let item = document.getElementById('cmt-item-' + cmtId);
            if (item) {
                item.style.transition = 'all 0.3s';
                item.style.opacity = '0';
                setTimeout(() => item.remove(), 300);
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra!');
        }
    });
}

// MỚI: Xóa toàn bộ lịch sử
function deleteAllHistory() {
    if(!confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa TOÀN BỘ lịch sử đọc không?\nHành động này không thể hoàn tác!')) return;
    
    let fd = new FormData();
    fd.append('action', 'history_delete_all');
    
    fetch('api/user.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                // Reload lại trang để làm sạch danh sách
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể xóa'));
            }
        })
        .catch(err => alert('Lỗi kết nối'));
}
</script>