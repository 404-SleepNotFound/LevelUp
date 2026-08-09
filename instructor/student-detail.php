<?php
$ROOT_PATH = '../';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once $ROOT_PATH . 'includes/header.php';

// รับค่า ID ผู้เรียนที่ส่งมาจาก URL
$student_id = isset($_GET['id']) ? trim($_GET['id']) : '';