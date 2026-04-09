<?php
// File: api/auth.php
// Đăng nhập, đăng ký, đăng xuất qua API
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing action']);
    exit;
}

$action = $_POST['action'];

switch ($action) {
    case 'login':
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'banned') {
                    echo json_encode(['status' => 'error', 'message' => '🚫 Tài khoản đã bị KHÓA vĩnh viễn!']);
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['avatar'] = !empty($user['avatar']) ? $user['avatar'] : null;

                    echo json_encode(['status' => 'success', 'message' => 'Đăng nhập thành công', 'data' => ['role' => $user['role']]]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => '❌ Sai mật khẩu!']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => '❌ Tài khoản không tồn tại!']);
        }
        break;

    case 'register':
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($password !== $confirm_pass) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu xác nhận không khớp!']);
            break;
        }

        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Tên đăng nhập hoặc Email đã tồn tại!']);
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_pass, $role);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Đăng ký thành công! Bạn có thể đăng nhập ngay.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $conn->error]);
            }
        }
        break;
        
    case 'logout':
        session_unset();
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'Đã đăng xuất']);
        break;
        
    case 'check_session':
        // Real-time ban check
        if (isset($_SESSION['user_id'])) {
            $check_id = $_SESSION['user_id'];
            $stmt_status = $conn->prepare("SELECT status, role, avatar FROM users WHERE id = ?");
            if ($stmt_status) {
                $stmt_status->bind_param("i", $check_id);
                $stmt_status->execute();
                $res_status = $stmt_status->get_result();

                if ($res_status->num_rows > 0) {
                    $u_data = $res_status->fetch_assoc();
                    if ($u_data['status'] === 'banned') {
                        session_unset();
                        session_destroy();
                        echo json_encode(['status' => 'error', 'message' => 'banned']);
                        exit;
                    } else {
                        // Thỏa mãn, đồng bộ lại quyền nếu có sự thay đổi (từ user lên mod)
                        if ($_SESSION['role'] !== $u_data['role']) {
                            $_SESSION['role'] = $u_data['role'];
                        }
                        
                        echo json_encode([
                            'status' => 'success', 
                            'data' => [
                                'user_id' => $_SESSION['user_id'],
                                'username' => $_SESSION['username'],
                                'role' => $_SESSION['role'],
                                'avatar' => $u_data['avatar']
                            ]
                        ]);
                        exit;
                    }
                }
            }
            // Không thấy user
            session_unset();
            session_destroy();
            echo json_encode(['status' => 'error', 'message' => 'not_found']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
