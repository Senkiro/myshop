<?php
include_once("shared/connect.php");

// Check if the product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_dashboard.php"); 
    exit();
}

$product_id = $_GET['id'];

// Fetch product details from the database
$sql = "SELECT * FROM cars WHERE car_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if product ID is valid
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        echo "ID invalid";
        exit();
    }

    // Retrieve form data
    $product_id = $_POST['product_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $color = $_POST['color'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // File upload handling
    $target_file = $row['image'];
    if ($_FILES['image']['name']) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if exists
            $old_image = $row['image'];
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        } else {
            echo "Fail to upload img";
            exit();
        }
    }

    // Update product information in the database
    $sql_update = "UPDATE cars SET brand=?, model=?, year=?, color=?, price=?,quantity=?, image=? WHERE car_id=?";
    if ($stmt = $conn->prepare($sql_update)) {
        $stmt->bind_param("ssissssi", $brand, $model, $year, $color, $price, $quantity, $target_file, $product_id);
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?update_success=1");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "SQL error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
</head>
<body>
    <h1>Update car info</h1>    
    <form action="update_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand" value="<?php echo $row['brand']; ?>" required><br>

        <label for="model">Model:</label>
        <input type="text" id="model" name="model" value="<?php echo $row['model']; ?>" required><br>

        <label for="year">Year:</label>
        <input type="number" id="year" name="year" value="<?php echo $row['year']; ?>" required><br>

        <label for="color">Color:</label>
        <input type="text" id="color" name="color" value="<?php echo $row['color']; ?>" required><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo $row['price']; ?>" required><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $row['quantity']; ?>" required><br>

        <label for="image">Update img:</label>
        <input type="file" id="image" name="image" accept="image/*"><br>

        <label for="existing_image">Current img:</label><br>
        <img src="<?php echo $row['image']; ?>" alt="Current Image" style="max-width: 200px;"><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
