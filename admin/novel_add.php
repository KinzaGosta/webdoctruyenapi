<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'mod')) {
    header("Location: ../index.php"); exit;
}

$cats = $conn->query("SELECT * FROM categories");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $desc = trim($_POST['description']);
    $image = trim($_POST['cover_image']);
    $status = $_POST['status'];
    $posted_by = $_SESSION['user_id'];
    $slug = createSlug($title);
    $selected_cats = $_POST['categories'] ?? [];

    $stmt = $conn->prepare("INSERT INTO novels (title, slug, author, description, cover_image, status, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $title, $slug, $author, $desc, $image, $status, $posted_by);

    if ($stmt->execute()) {
        $novel_id = $conn->insert_id;
        foreach ($selected_cats as $cat_id) {
            $conn->query("INSERT INTO novel_categories (novel_id, category_id) VALUES ($novel_id, $cat_id)");
        }
        header("Location: novels.php"); exit;
    } else { $error = "Lỗi: " . $conn->error; }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Truyện Mới</title>
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
        <h2 class="fw-bold mb-4">➕ Thêm Truyện Mới</h2>
        <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tên truyện</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tác giả</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Thể loại</label>
                        <div class="border p-3 rounded bg-light d-flex flex-wrap gap-3">
                            <?php while($c = $cats->fetch_assoc()): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $c['id'] ?>" id="cat_<?= $c['id'] ?>">
                                    <label class="form-check-label" for="cat_<?= $c['id'] ?>"><?= $c['name'] ?></label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Link Ảnh bìa</label>
                            <input type="text" name="cover_image" class="form-control" placeholder="https://...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="ongoing">Đang tiến hành</option>
                                <option value="completed">Đã hoàn thành</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả / Giới thiệu</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4">Lưu Truyện</button>
                        <a href="novels.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>