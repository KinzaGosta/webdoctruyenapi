<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

$_POST['action'] = 'edit_user';
$_POST['id'] = 2; // user1
$_POST['username'] = 'user1_edit';
$_POST['email'] = 'user1_edit@gmail.com';
$_POST['role'] = 'user';
$_POST['password'] = '';

require '../api/admin.php';
