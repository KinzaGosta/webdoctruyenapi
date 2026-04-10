<?php
require_once 'app/models/Model.php';

class ComicModel extends Model {
    public function getComicDetail($slug) {
        $api_url = "https://otruyenapi.com/v1/api/truyen-tranh/" . $slug;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['data']['item'])) {
                return $data['data']['item'];
            }
        }
        return false;
    }

    public function increaseView($slug, $comicName, $comicThumb) {
        $random_start = rand(1000, 5000);
        $sql_view = "INSERT INTO comic_views (comic_slug, comic_name, comic_thumb, view_count) 
                     VALUES (?, ?, ?, ?) 
                     ON DUPLICATE KEY UPDATE 
                     view_count = view_count + 1,
                     comic_name = VALUES(comic_name), 
                     comic_thumb = VALUES(comic_thumb)";
        $stmt = $this->conn->prepare($sql_view);
        $stmt->bind_param("sssi", $slug, $comicName, $comicThumb, $random_start);
        $stmt->execute();

        $res = $this->conn->query("SELECT view_count FROM comic_views WHERE comic_slug = '$slug'");
        return $res->fetch_assoc()['view_count'] ?? 0;
    }

    public function getFollowersCount($slug) {
        $res = $this->conn->query("SELECT COUNT(*) as total FROM comic_favorites WHERE comic_slug = '$slug'");
        return $res->fetch_assoc()['total'] ?? 0;
    }

    public function isFavorite($userId, $slug) {
        $check = $this->conn->query("SELECT * FROM comic_favorites WHERE user_id = $userId AND comic_slug = '$slug'");
        return $check->num_rows > 0;
    }
}
?>
