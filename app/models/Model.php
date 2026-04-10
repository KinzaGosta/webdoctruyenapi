<?php
// File: app/models/Model.php
require_once 'config/database.php';

class Model {
    protected $conn;

    public function __construct() {
        require_once 'config/database.php';
        $this->conn = $GLOBALS['conn'];
    }
}
?>
