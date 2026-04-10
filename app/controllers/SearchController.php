<?php
// File: app/controllers/SearchController.php
require_once 'core/Controller.php';

class SearchController extends Controller {

    public function index() {
        $keyword = $_GET['q'] ?? '';
        $keyword = trim($keyword);

        // Nếu từ khóa rỗng, quay về trang chủ
        if ($keyword == '') {
            header("Location: index.php");
            exit;
        }

        // =========================================================
        // A. TÌM TRUYỆN CHỮ (DATABASE)
        // =========================================================
        require_once 'config/database.php';
        $conn = $GLOBALS['conn'];
        $sql = "SELECT * FROM novels WHERE title LIKE ? OR author LIKE ?";
        $likeKey = "%" . $keyword . "%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $likeKey, $likeKey);
        $stmt->execute();
        $db_results = $stmt->get_result();

        // =========================================================
        // B. TÌM TRUYỆN TRANH (API)
        // =========================================================
        $api_url = "https://otruyenapi.com/v1/api/tim-kiem?keyword=" . urlencode($keyword);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $api_results = [];
        $img_domain = "https://img.otruyenapi.com/uploads/comics/";

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['data']['items'])) {
                $api_results = $data['data']['items'];
            }
        }

        $this->render('search/index', [
            'keyword' => $keyword,
            'db_results' => $db_results,
            'api_results' => $api_results,
            'img_domain' => $img_domain
        ]);
    }
}
?>
