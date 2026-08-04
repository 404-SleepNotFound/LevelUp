<?php
$servername = "localhost:3306";
$username = "quizchai_levelup";
$password = "levelup1234";
$dbname = "quizchai_levelup";

try {
    // เพิ่ม charset=utf8mb4 เพื่อป้องกันปัญหาตัวอักษรภาษาไทยต่างดาว
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 💡 เพิ่มตัวแปร $pdo ให้ชี้ไปยังการเชื่อมต่อเดียวกับ $conn
    // ช่วยให้หน้า course-builder และไฟล์อื่นๆ ที่เรียกใช้ $pdo ทำงานได้ทันที
    $pdo = $conn;

    // echo "Connected successfully";
} catch (PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
}