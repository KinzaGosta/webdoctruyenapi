<?php
// File: api/forum.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once dirname(__DIR__) . '/app/models/ForumModel.php';
header('Content-Type: application/json; charset=utf-8');

$forumModel = new ForumModel();
$user_id = $_SESSION['user_id'] ?? 0;
$user_role = $_SESSION['role'] ?? 'user';
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create_topic':
        if ($user_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
            exit;
        }
        $category_id = intval($_POST['category_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        if ($category_id > 0 && !empty($title) && !empty($content)) {
            $topic_id = $forumModel->createTopic($category_id, $user_id, $title, $content);
            if ($topic_id) {
                echo json_encode(['status' => 'success', 'topic_id' => $topic_id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập đủ thông tin']);
        }
        break;

    case 'create_post':
        if ($user_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
            exit;
        }
        $topic_id = intval($_POST['topic_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        
        if ($topic_id > 0 && !empty($content)) {
            $result = $forumModel->createPost($topic_id, $user_id, $content);
            if ($result) {
                // Gửi thông báo cho chủ topic
                $forumModel->notifyReply($topic_id, $user_id, $_SESSION['username']);
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nội dung không hợp lệ']);
        }
        break;

    case 'delete_topic':
        if ($user_role !== 'admin' && $user_role !== 'mod') {
            echo json_encode(['status' => 'error', 'message' => 'Không có quyền']);
            exit;
        }
        $topic_id = intval($_POST['topic_id'] ?? 0);
        if ($topic_id > 0) {
            if ($forumModel->deleteTopic($topic_id)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không thể xóa']);
            }
        }
        break;

    case 'delete_post':
        $post_id = intval($_POST['post_id'] ?? 0);
        if ($post_id > 0) {
            $check = $conn->query("SELECT user_id FROM forum_posts WHERE id = $post_id");
            if ($check && $check->num_rows > 0) {
                $owner = $check->fetch_assoc()['user_id'];
                if ($user_role === 'admin' || $user_role === 'mod' || $user_id == $owner) {
                    if ($forumModel->deletePost($post_id)) {
                        echo json_encode(['status' => 'success']);
                        exit;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Không có quyền']);
                    exit;
                }
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Không thể xóa']);
        break;

    case 'edit_post':
        $post_id = intval($_POST['post_id'] ?? 0);
        $content = trim(htmlspecialchars($_POST['content'] ?? ''));
        if ($post_id > 0 && !empty($content)) {
            $check = $conn->query("SELECT user_id FROM forum_posts WHERE id = $post_id");
            if ($check && $check->num_rows > 0) {
                $owner = $check->fetch_assoc()['user_id'];
                if ($user_role === 'admin' || $user_role === 'mod' || $user_id == $owner) {
                    $stmt = $conn->prepare("UPDATE forum_posts SET content = ? WHERE id = ?");
                    $stmt->bind_param("si", $content, $post_id);
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success']);
                        exit;
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Không có quyền']);
                    exit;
                }
            }
        }
        echo json_encode(['status' => 'error', 'message' => 'Lỗi cập nhật']);
        break;

    case 'like_post':
        if ($user_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Cần đăng nhập']);
            exit;
        }
        $post_id = intval($_POST['post_id'] ?? 0);
        if ($post_id > 0) {
            $check = $conn->query("SELECT * FROM forum_post_likes WHERE user_id=$user_id AND post_id=$post_id");
            if ($check && $check->num_rows > 0) {
                // Đã like -> Unlike
                $conn->query("DELETE FROM forum_post_likes WHERE user_id=$user_id AND post_id=$post_id");
                $conn->query("UPDATE forum_posts SET like_count = like_count - 1 WHERE id=$post_id");
            } else {
                // Chưa like -> Like
                $conn->query("INSERT INTO forum_post_likes (user_id, post_id) VALUES ($user_id, $post_id)");
                $conn->query("UPDATE forum_posts SET like_count = like_count + 1 WHERE id=$post_id");
            }
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Hành động không hợp lệ']);
        break;
}
?>
