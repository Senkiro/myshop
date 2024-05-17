<?php
session_start();
include_once("shared/connect.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = (int)$_POST['year'];
    $color = $_POST['color'];
    $price = (int)$_POST['price'];

    // Kiểm tra và tạo thư mục uploads nếu chưa tồn tại
    $target_dir = "uploads/"; 
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra nếu file ảnh thực sự là ảnh
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File không phải là ảnh.";
        $uploadOk = 0;
    }

    // Kiểm tra nếu file đã tồn tại
    if (file_exists($target_file)) {
        echo "File đã tồn tại.";
        $uploadOk = 0;
    }

    // Kiểm tra kích thước file (tối đa 10MB)
    if ($_FILES["image"]["size"] > 10485760) { // 10 MB
        echo "File quá lớn. Kích thước tối đa là 20MB.";
        $uploadOk = 0;
    }

    // Chỉ cho phép một số định dạng file nhất định
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Chỉ cho phép các file JPG, JPEG, PNG & GIF.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "File của bạn không được tải lên.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "File ". htmlspecialchars(basename($_FILES["image"]["name"])). " đã được tải lên.";
        } else {
            echo "Đã xảy ra lỗi khi tải file của bạn lên.";
        }
    }

    // Lưu sản phẩm vào cơ sở dữ liệu
    if ($uploadOk == 1) { // Chỉ chèn vào CSDL nếu tệp tải lên thành công
        $sql = "INSERT INTO cars (brand, model, year, color, price, image) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            // Chỉ định kiểu dữ liệu cho mỗi biến ràng buộc
            $stmt->bind_param("ssisss", $brand, $model, $year, $color, $price, $target_file);
            if ($stmt->execute()) {
                echo "Thêm sản phẩm thành công!";
                header("Location: admin_dashboard.php"); // Chuyển hướng người dùng về trang chính
                exit(); // Dừng việc thực hiện kịch bản tiếp theo
            } else {
                echo "Lỗi: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Lỗi chuẩn bị câu lệnh SQL: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <script src="ckeditor/ckeditor.js"></script>
</head>
<body>
    <h1>Thêm sản phẩm mới</h1>
    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <label for="brand">Thương hiệu:</label>
        <input type="text" id="brand" name="brand" required><br>

        <label for="model">Model:</label>
        <input type="text" id="model" name="model" required><br>

        <label for="year">Năm:</label>
        <input type="number" id="year" name="year" required><br>

        <label for="color">Màu:</label>
        <input type="text" id="color" name="color" required><br>

        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" required><br>

        <label for="image">Hình ảnh:</label>
        <input type="file" id="image" name="image" required accept="image/*"><br>

        <button type="submit">Thêm sản phẩm</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
