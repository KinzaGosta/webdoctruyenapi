    <?php
    // File: ajax_notification.php
    require_once 'config/database.php';
    session_start();

    // Helper: Trả về JSON
    function response($status, $msg, $data = []) {
        echo json_encode(['status' => $status, 'message' => $msg, 'data' => $data]);
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        response('error', 'Chưa đăng nhập');
    }

    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'] ?? '';

    // 1. USER GỬI BÁO LỖI (Gửi cho tất cả Admin)
    if ($action == 'user_report') {
        $title = trim($_POST['title']);
        $msg = trim($_POST['message']);
        
        if (empty($title) || empty($msg)) response('error', 'Vui lòng nhập đủ thông tin');

        // Lấy danh sách ID của tất cả Admin
        $admins = $conn->query("SELECT id FROM users WHERE role = 'admin'");
        
        if ($admins->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, type, title, message) VALUES (?, ?, 'report', ?, ?)");
            while ($row = $admins->fetch_assoc()) {
                $admin_id = $row['id'];
                $stmt->bind_param("iiss", $user_id, $admin_id, $title, $msg);
                $stmt->execute();
            }
            response('success', 'Đã gửi báo lỗi tới BQT!');
        } else {
            response('error', 'Không tìm thấy Admin nào để gửi.');
        }
    }

    // 2. LẤY DANH SÁCH THÔNG BÁO (Cho cái chuông)
    if ($action == 'get_notifications') {
        // Lấy 10 thông báo mới nhất
        $sql = "SELECT n.*, u.username, u.avatar, u.role 
                FROM notifications n 
                LEFT JOIN users u ON n.sender_id = u.id 
                WHERE n.receiver_id = ? 
                ORDER BY n.created_at DESC LIMIT 10";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifs = [];
        $unread_count = 0;

        // Đếm tổng số chưa đọc
        $count_sql = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE receiver_id = $user_id AND is_read = 0");
        $unread_count = $count_sql->fetch_assoc()['total'];

        while ($row = $result->fetch_assoc()) {
            // Xử lý hiển thị người gửi
            if ($row['type'] == 'system') {
                $row['sender_name'] = 'Hệ Thống';
                $row['sender_avatar'] = 'assets/system_logo.jpg'; // Bạn nhớ tạo ảnh này hoặc để link ảnh mạng
            } else {
                $row['sender_name'] = $row['username'] ?? 'Người dùng ẩn';
                $row['sender_avatar'] = $row['avatar'] ?? 'https://ui-avatars.com/api/?name='.$row['sender_name'];
            }
            
            $row['time_ago'] = date('H:i d/m', strtotime($row['created_at']));
            $notifs[] = $row;
        }

        response('success', '', ['notifications' => $notifs, 'unread' => $unread_count]);
    }

    // 3. ĐÁNH DẤU ĐÃ ĐỌC
    if ($action == 'mark_read') {
        $notif_id = intval($_POST['id']);
        $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $notif_id AND receiver_id = $user_id");
        response('success', 'Đã đọc');
    }

    // 4. XÓA THÔNG BÁO
    if ($action == 'delete_notif') {
        $notif_id = intval($_POST['id']);
        $conn->query("DELETE FROM notifications WHERE id = $notif_id AND receiver_id = $user_id");
        response('success', 'Đã xóa');
    }

    // 5. ADMIN GỬI THÔNG BÁO (Xử lý trong file admin riêng hoặc ở đây nếu muốn gộp)
    // Tôi sẽ để phần xử lý gửi của Admin ở file giao diện Admin bên dưới cho dễ quản lý.
    // ... (Các code cũ giữ nguyên)

    // 6. XÓA TẤT CẢ THÔNG BÁO CỦA USER
    if ($action == 'delete_all') {
    // Chỉ xóa các thông báo của người dùng hiện tại
        $stmt = $conn->prepare("DELETE FROM notifications WHERE receiver_id = ?");
        $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        response('success', 'Đã xóa tất cả thông báo');
    } else {
        response('error', 'Lỗi khi xóa');
    }
}
?>