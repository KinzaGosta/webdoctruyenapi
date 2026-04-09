<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check quyền
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'mod')) {
    header("Location: ../index.php"); exit;
}

if (!isset($_GET['id'])) { header("Location: novels.php"); exit; }
$id = intval($_GET['id']);

// Lấy thông tin truyện
$novel = $conn->query("SELECT * FROM novels WHERE id = $id")->fetch_assoc();
if (!$novel) { die("Truyện không tồn tại!"); }

// Lấy thể loại đã chọn
$current_cats = [];
$res_cat = $conn->query("SELECT category_id FROM novel_categories WHERE novel_id = $id");
while($r = $res_cat->fetch_assoc()) { $current_cats[] = $r['category_id']; }

// Lấy tất cả thể loại
$all_cats = $conn->query("SELECT * FROM categories");

// Xử lý Lưu
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $desc = trim($_POST['description']);
    $image = trim($_POST['cover_image']);
    $status = $_POST['status'];
    $slug = createSlug($title);
    $selected_cats = $_POST['categories'] ?? [];

    $stmt = $conn->prepare("UPDATE novels SET title=?, slug=?, author=?, description=?, cover_image=?, status=? WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $slug, $author, $desc, $image, $status, $id);
    
    if ($stmt->execute()) {
        $conn->query("DELETE FROM novel_categories WHERE novel_id = $id");
        foreach ($selected_cats as $cat_id) {
            $cat_id = intval($cat_id);
            $conn->query("INSERT INTO novel_categories (novel_id, category_id) VALUES ($id, $cat_id)");
        }
        echo "<script>alert('Cập nhật thành công!'); window.location.href='novels.php';</script>";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Truyện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<div class="d-flex">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3">
        <a href="../index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none border-bottom pb-3"><span class="fs-4 fw-bold"><i class="fas fa-user-shield"></i> Admin Panel</span></a>
        <ul class="nav nav-pills flex-column mb-auto mt-3">
            <li><a href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="novels.php" class="active"><i class="fas fa-book me-2"></i> Quản lý Truyện</a></li>
            <li><a href="../index.php" class="mt-5 text-warning"><i class="fas fa-home me-2"></i> Về trang chủ</a></li>
        </ul>
    </div>

    <div class="container-fluid p-4">
        <h2 class="fw-bold mb-4">✏️ Sửa Truyện: <span class="text-primary"><?= htmlspecialchars($novel['title']) ?></span></h2>
        
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tên truyện</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($novel['title']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tác giả</label>
                            <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($novel['author']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Thể loại</label>
                        <div class="border p-3 rounded bg-light d-flex flex-wrap gap-3">
                            <?php while($c = $all_cats->fetch_assoc()): ?>
                                <?php $checked = in_array($c['id'], $current_cats) ? 'checked' : ''; ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $c['id'] ?>" id="cat_<?= $c['id'] ?>" <?= $checked ?>>
                                    <label class="form-check-label" for="cat_<?= $c['id'] ?>"><?= $c['name'] ?></label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Link Ảnh bìa</label>
                            <input type="text" name="cover_image" class="form-control" value="<?= htmlspecialchars($novel['cover_image']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="ongoing" <?= $novel['status'] == 'ongoing' ? 'selected' : '' ?>>Đang tiến hành</option>
                                <option value="completed" <?= $novel['status'] == 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                                <option value="dropped" <?= $novel['status'] == 'dropped' ? 'selected' : '' ?>>Tạm ngưng</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả / Giới thiệu</label>
                        <textarea name="description" class="form-control" rows="6"><?= htmlspecialchars($novel['description']) ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4">💾 Lưu Thay Đổi</button>
                        <a href="novels.php" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>