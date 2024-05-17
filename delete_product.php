<?php
session_start();
include_once("shared/connect.php");

// Xác định ID của sản phẩm cần xóa từ tham số URL
if (!isset($_GET['id'])) {
    header("Location: welcome.php");
    exit();
}
$id = $_GET['id'];

// Xóa sản phẩm từ cơ sở dữ liệu
$sql = "DELETE FROM cars WHERE car_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Xóa sản phẩm thành công!";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Lỗi khi xóa sản phẩm: " . $stmt->error;
    }
} else {
    echo "Lỗi truy vấn cơ sở dữ liệu: " . $conn->error;
}
?>
