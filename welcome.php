<?php
include_once("shared/connect.php");

// Số bản ghi trên mỗi trang
$limit = 5;

// Xác định trang hiện tại từ tham số URL, mặc định là trang 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Lấy giá trị khoảng giá và thương hiệu từ tham số URL
$price_from = isset($_GET['price_from']) ? (int)$_GET['price_from'] : 0;
$price_to = isset($_GET['price_to']) && (int)$_GET['price_to'] > 0 ? (int)$_GET['price_to'] : 1000000000;
$selected_brand = isset($_GET['brand']) ? $_GET['brand'] : '';

// Xác định vị trí bắt đầu cho truy vấn SQL
$start = ($page - 1) * $limit;

// Đếm tổng số bản ghi với điều kiện lọc
$sql = "SELECT COUNT(*) AS total FROM cars WHERE price BETWEEN $price_from AND $price_to";
if ($selected_brand !== '') {
    $sql .= " AND brand = '" . $conn->real_escape_string($selected_brand) . "'";
}
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];

// Tính tổng số trang
$total_pages = ceil($total_records / $limit);

// Truy vấn dữ liệu cho trang hiện tại với điều kiện lọc
$sql = "SELECT car_id, brand, model, year, color, price, image FROM cars WHERE price BETWEEN $price_from AND $price_to";
if ($selected_brand !== '') {
    $sql .= " AND brand = '" . $conn->real_escape_string($selected_brand) . "'";
}
$sql .= " LIMIT $start, $limit";
$result = $conn->query($sql);

// Truy vấn để lấy danh sách các thương hiệu
$brand_sql = "SELECT DISTINCT brand FROM cars";
$brand_result = $conn->query($brand_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <!-- Banner và thanh điều hướng -->
        <div>Banner và thanh điều hướng ở đây</div>

        <!-- Thông tin người dùng và giỏ hàng -->
        <div>
            <?php if(isset($_SESSION['username'])) { ?>
                <p>Xin chào, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Đăng xuất</a></p>
                <!-- Hiển thị giỏ hàng -->
                <!-- Hiển thị các sản phẩm trong giỏ hàng -->
            <?php } else { ?>
                <p><a href="login.php">Đăng nhập</a> | <a href="register.php">Đăng ký</a></p>
            <?php } ?>
        </div>
    </header>

    <main>
        <!-- Nội dung chính của trang chủ -->
        <h1>Chào mừng đến với cửa hàng của chúng tôi!</h1>

        <!-- Hiển thị form lọc thương hiệu và khoảng giá -->
        <form method="GET" action="">
            <label for="brand">Chọn thương hiệu:</label>
            <select name="brand" id="brand">
                <option value="">Tất cả</option>
                <?php
                if ($brand_result->num_rows > 0) {
                    while($brand_row = $brand_result->fetch_assoc()) {
                        $brand = $brand_row['brand'];
                        echo "<option value='$brand'" . ($selected_brand == $brand ? " selected" : "") . ">$brand</option>";
                    }
                }
                ?>
            </select>

            <label for="price_from">Giá từ:</label>
            <input type="number" name="price_from" id="price_from" value="<?php echo $price_from; ?>" min="0">

            <label for="price_to">Giá đến:</label>
            <input type="number" name="price_to" id="price_to" value="<?php echo $price_to; ?>" min="0">

            <button type="submit">Lọc</button>
        </form>

        <a href="add_product.php" class="btn btn-success">
	<i class="glyphicon glyphicon-plus"></i> Thêm sản phẩm
</a>


        <!-- Hiển thị danh sách sản phẩm -->
        <h1>Danh sách ô tô</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Year</th>
                <th>Color</th>
                <th>Price</th>
                <th>Img</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                // Hiển thị dữ liệu của mỗi hàng
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["car_id"]. "</td>";
                    echo "<td>" . $row["brand"]. "</td>";
                    echo "<td>" . $row["model"]. "</td>";
                    echo "<td>" . $row["year"]. "</td>";
                    echo "<td>" . $row["color"]. "</td>";
                    echo "<td>" . $row["price"]. "</td>";
                    echo "<td><img src='" . $row["image"] . "' alt='" . $row["model"] . "'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
            }
            ?>
        </table>

        <!-- Hiển thị liên kết phân trang -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $url = "?page=$i&price_from=$price_from&price_to=$price_to&brand=$selected_brand";
                if ($i == $page) {
                    echo "<a class='active' href='$url'>$i</a>";
                } else {
                    echo "<a href='$url'>$i</a>";
                }
            }
            ?>
        </div>
    </main>

    <footer>
        <!-- Chân trang -->
    </footer>
</body>
</html>

<?php
$conn->close();
?>

