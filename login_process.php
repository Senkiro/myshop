<?php
include_once("shared/connect.php");

$username = $_POST['username'];
$password = $_POST['password'];


$sql = "SELECT * FROM Users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $_SESSION['username'] = $username;
    header("Location: welcome.php"); 
} else {

    echo "Tên đăng nhập hoặc mật khẩu không đúng";
}
$conn->close();
?>
