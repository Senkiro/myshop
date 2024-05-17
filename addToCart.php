<?php
include_once ("shared/connect.php");

if (!isset($_SESSION['user_id'])) {
    echo "Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng của bạn.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['car_id'])) {
    // Sanitize and validate the input
    $car_id = $_POST['car_id'];
    if (!is_numeric($car_id)) {
        echo "Invalid product ID.";
        exit();
    }

    // Check if the product already exists in the cart
    $user_id = $_SESSION['user_id'];
    $check_sql = "SELECT * FROM cart WHERE user_id = $user_id AND car_id = $car_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "Sản phẩm đã tồn tại trong giỏ hàng.";
        exit();
    }

    // If the product doesn't exist, add it to the cart
    $insert_sql = "INSERT INTO cart (user_id, car_id, quantity, price) VALUES ($user_id, $car_id, 1, (SELECT price FROM cars WHERE car_id = $car_id))";
    if ($conn->query($insert_sql) === TRUE) {
        echo "Sản phẩm đã được thêm vào giỏ hàng.";
    } else {
        echo "Lỗi khi thêm sản phẩm vào giỏ hàng: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>