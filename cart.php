<?php
include_once("shared/connect.php");

if (!isset($_SESSION['user_name'])) {
    echo "Vui lòng đăng nhập để xem giỏ hàng của bạn.";
    exit();
}

$user_id = $_SESSION['user_name'];

$sql = "SELECT oders.car_id, cars.brand, cars.model, cart.quantity, cart.price
        FROM oders
        JOIN cars ON cart.car_id = cars.car_id
        WHERE cart.user_id = $user_id";


$conn->close();
?>
