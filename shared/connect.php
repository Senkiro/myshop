<?php
session_start();
$conn = new mysqli("localhost:3308", "root", "", "myshop");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

