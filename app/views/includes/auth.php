<?php
// File: includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lưu ý: Cần đảm bảo biến $conn (kết nối CSDL) hoạt động được trong hàm.
// Bạn có thể phải include database.php ở file cha gọi auth.php, hoặc dùng global.

function requireLogin() {
    // 1. Kiểm tra session cơ bản
    if (!isset($_SESSION['user_id'])) {
        header("Location: /WebDocTruyen/login.php");
        exit;
    }

    // 2. [MỚI] KIỂM TRA TRẠNG THÁI TỪ DB (Chống việc đã bị Ban mà vẫn dùng session cũ)
    global $conn; // Gọi biến kết nối từ bên ngoài vào
    
    // Nếu $conn chưa tồn tại (do chưa include config), ta tự include lại để chắc chắn
    if (!isset($conn)) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/WebDocTruyen/config/database.php';
    }

    $id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($user['status'] === 'banned') {
            // Hủy session ngay lập tức
            session_unset();
            session_destroy();
            
            // Chuyển hướng về trang thông báo lỗi hoặc login kèm thông báo
            die("<h3>Tài khoản bị khóa!</h3><p>Tài khoản của bạn đã bị vô hiệu hóa do vi phạm quy định.</p><a href='/WebDocTruyen/index.php'>Về trang chủ</a>");
        }
    } else {
        // Trường hợp User ID trong session không tồn tại trong DB (VD: Admin xóa user đó rồi)
        session_unset();
        session_destroy();
        header("Location: /WebDocTruyen/login.php");
        exit;
    }
}

