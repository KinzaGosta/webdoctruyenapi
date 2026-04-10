<?php
// 1. KẾT NỐI DATABASE & HEADER
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
} elseif (file_exists('db_connect.php')) {
    require_once 'db_connect.php';
} else {
    die("Lỗi: Không tìm thấy file kết nối database!");
}

// Nhúng Header
if (file_exists('app/views/includes/header.php')) {
    require_once 'app/views/includes/header.php';
} elseif (file_exists('header.php')) {
    require_once 'header.php';
}

// KHÔNG CẦN LINK CSS RIÊNG NỮA VÌ ĐÃ CÓ TRONG HEADER RỒI (custom.css)

// 2. XỬ LÝ LOGIC
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$cat_id = 0;
$cat_name = "";

if (!empty($slug) && isset($conn)) {
    $stmt = $conn->prepare("SELECT id, name FROM categories WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result_cat = $stmt->get_result();

    if ($result_cat->num_rows > 0) {
        $row_cat = $result_cat->fetch_assoc();
        $cat_id = $row_cat['id'];
        $cat_name = $row_cat['name'];
    }
}
?>

<div class="container" style="min-height: 600px;"> <?php if ($cat_id > 0): ?>
        <div class="category-header">
            <h3>
                <i class="fas fa-layer-group text-warning me-2"></i>Truyện: <span class="text-primary"><?php echo htmlspecialchars($cat_name); ?></span>
            </h3>
        </div>

        <?php
        $sql_story = "SELECT n.* FROM novels n 
                      JOIN novel_categories nc ON n.id = nc.novel_id 
                      WHERE nc.category_id = ? 
                      ORDER BY n.updated_at DESC";
        
        $stmt_story = $conn->prepare($sql_story);
        $stmt_story->bind_param("i", $cat_id);
        $stmt_story->execute();
        $result_story = $stmt_story->get_result();

        if ($result_story->num_rows > 0): 
        ?>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3"> <?php while ($row = $result_story->fetch_assoc()): 
                    $anh_bia = !empty($row['cover_image']) ? $row['cover_image'] : 'https://via.placeholder.com/180x260?text=No+Image';
                    $link_truyen = "index.php?route=novel/detail&id=" . $row['id']; 
                    $label = ($row['status'] == 'completed') ? '<span class="badge bg-success badge-overlay">Full</span>' : ''; // Ví dụ nhãn
                ?>
                    <div class="col">
                        <div class="book-card">
                            <a href="<?php echo $link_truyen; ?>" class="book-thumb">
                                <?php echo $label; ?>
                                <img src="<?php echo $anh_bia; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy">
                            </a>
                            
                            <div class="book-body">
                                <div class="book-title">
                                    <a href="<?php echo $link_truyen; ?>" title="<?php echo htmlspecialchars($row['title']); ?>">
                                        <?php echo htmlspecialchars($row['title']); ?>
                                    </a>
                                </div>
                                
                                <div class="book-info">
                                    <span><i class="fas fa-user-edit"></i> <?php echo !empty($row['author']) ? htmlspecialchars($row['author']) : 'Unknown'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
        <?php else: ?>
            <div class="alert alert-warning text-center mt-4">
                <i class="fas fa-search me-2"></i>Hiện tại chưa có truyện nào thuộc thể loại <strong><?php echo htmlspecialchars($cat_name); ?></strong>.
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-danger text-center mt-5">
            <h3>404 - Not Found</h3>
            <p>Không tìm thấy thể loại hoặc đường dẫn không hợp lệ!</p>
            <a href="index.php" class="btn btn-primary">Về trang chủ</a>
        </div>
    <?php endif; ?>
    
</div>

<?php 
// Nhúng Footer
if (file_exists('app/views/includes/footer.php')) {
    require_once 'app/views/includes/footer.php';
} elseif (file_exists('footer.php')) {
    require_once 'footer.php';
}
?>