<?php
include_once("shared/connect.php");

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM Users WHERE username=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    session_start(); 
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $user['role']; 

    if ($_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } elseif ($_SESSION['role'] === 'customer') {
        header("Location: user_dashboard.php");
    } 
} else {
    echo "Tên đăng nhập hoặc mật khẩu không đúng";
}

$stmt->close();
$conn->close();
?>
