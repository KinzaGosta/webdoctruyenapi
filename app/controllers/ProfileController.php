<?php
// File: app/controllers/ProfileController.php
require_once 'core/Controller.php';

class ProfileController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?route=auth/login");
            exit;
        }
        $this->render('profile/index', []);
    }
}
?>
