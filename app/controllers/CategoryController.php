<?php
// File: app/controllers/CategoryController.php
require_once 'core/Controller.php';

class CategoryController extends Controller {

    public function novel() {
        if (!isset($_GET['slug'])) {
            die('<div class="container mt-5 alert alert-danger">Lỗi: Cần cung cấp link phân loại chuyên mục chữ! <a href="index.php">Về trang chủ</a></div>');
        }
        $this->render('category/novel', []);
    }

    public function comic() {
        if (!isset($_GET['slug'])) {
            die('<div class="container mt-5 alert alert-danger">Lỗi: Cần cung cấp link thể loại truyện tranh! <a href="index.php">Về trang chủ</a></div>');
        }
        $this->render('category/comic', []);
    }
}
?>
