<?php
include_once ("shared/connect.php");

// Số bản ghi trên mỗi trang
$limit = 5;

// Xác định trang hiện tại từ tham số URL, mặc định là trang 1
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;

// Lấy giá trị khoảng giá và thương hiệu từ tham số URL
$price_from = isset($_GET['price_from']) ? (int) $_GET['price_from'] : 0;
$price_to = isset($_GET['price_to']) && (int) $_GET['price_to'] > 0 ? (int) $_GET['price_to'] : 1000000000;
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

$sql = "SELECT car_id, brand, model, year, color, price, image FROM cars WHERE price BETWEEN $price_from AND $price_to  ";
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        main {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        img {
            max-width: 80px;
            height: auto;
            border-radius: 6px;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f2f2f2;
            color: #333;
            transition: background-color 0.3s;
        }

        .pagination a.active,
        .pagination a:hover {
            background-color: #333;
            color: #fff;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form label {
            margin-right: 10px;
        }

        .filter-form select,
        .filter-form input[type="number"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .filter-form button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #555;
        }

        .add-product-btn {
            margin-bottom: 20px;
        }

        .add-product-btn a {
            padding: 10px 20px;
            border-radius: 4px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .add-product-btn a:hover {
            background-color: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            animation: slide-down 0.3s ease;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            margin-top: 20px;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }
    </style>
</head>

<body>
    <header>
        <h1>Chào mừng đến với cửa hàng của chúng tôi!</h1>
    </header>

    <div>
        <?php if (isset($_SESSION['username'])) { ?>
            <p>Xin chào, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Đăng xuất</a></p>

        <?php } else { ?>
            <p><a href="login.php">Đăng nhập</a> | <a href="register.php">Đăng ký</a></p>
        <?php } ?>
    </div>
    </div>

    <a href="cart.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="buy-now">Gio Hang</a>

    </div>
    <main>
        <div class="filter-form">
            <form method="GET" action="">
                <label for="brand">Chọn thương hiệu:</label>
                <select name="brand" id="brand">
                    <option value="">Tất cả</option>
                    <?php
                    if ($brand_result->num_rows > 0) {
                        while ($brand_row = $brand_result->fetch_assoc()) {
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

                <button type="submit">Tìm kiếm</button>
            </form>
        </div>


        <table>
            <tr>
                <th>ID</th>
                <th>Thương hiệu</th>
                <th>Model</th>
                <th>Năm sản xuất</th>
                <th>Màu sắc</th>
                <th>Giá</th>
                <th>Ảnh</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["car_id"] . "</td>";
                    echo "<td>" . $row["brand"] . "</td>";
                    echo "<td class='model-info' >" . $row["model"] . "</td>";
                    echo "<td>" . $row["year"] . "</td>";
                    echo "<td>" . $row["color"] . "</td>";
                    echo "<td>" . number_format($row["price"], 0, ',', '.') . '₫' . "</td>";
                    echo "<td><img src='" . $row["image"] . "' alt='" . $row["model"] . "' class='product-image'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
            }
            ?>
        </table>

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

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Thông tin sản phẩm</h2>
            <div id="modalImageContainer"></div>
            <p><strong>Brand:</strong> <span id="modalBrand"></span></p>
            <p><strong>Model:</strong> <span id="modalModel"></span></p>
            <p><strong>Year:</strong> <span id="modalYear"></span></p>
            <p><strong>Color:</strong> <span id="modalColor"></span></p>
            <p><strong>Price:</strong> <span id="modalPrice"></span></p>
            <button class="addToCartBtn" id="addToCartBtn" data-product-id="<?php echo $row["car_id"]; ?>">Thêm vào giỏ
                hàng</button>

        </div>
    </div>


    <script>
        // Get the modal
        var modal = document.getElementById("productModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        span.onclick = function () {
            modal.style.display = "none";
        };

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        // Add click event listener to all elements with class 'model-info'
        var modelInfos = document.getElementsByClassName("model-info");
        for (var i = 0; i < modelInfos.length; i++) {
            modelInfos[i].addEventListener("click", function () {
                // Retrieve product information from the clicked row
                var row = this.parentNode;
                var brand = row.childNodes[1].textContent;
                var model = row.childNodes[2].textContent;
                var year = row.childNodes[3].textContent;
                var color = row.childNodes[4].textContent;
                var price = row.childNodes[5].textContent;
                var imageSrc = row.querySelector(".product-image").src;

                // Set product information in the modal
                document.getElementById("modalBrand").textContent = brand;
                document.getElementById("modalModel").textContent = model;
                document.getElementById("modalYear").textContent = year;
                document.getElementById("modalColor").textContent = color;
                document.getElementById("modalPrice").textContent = price;

                // Set product image in the modal
                var imageContainer = document.getElementById("modalImageContainer");
                imageContainer.innerHTML = "<img src='" + imageSrc + "' alt='" + model + "' style='max-width: 200px;'>";

                // Display the modal
                modal.style.display = "block";
            });
        }
        // Get the modal
        var modal = document.getElementById("productModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function () {
            modal.style.display = "none";
        };

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        // Add click event listener to all elements with class 'model-info'
        var modelInfos = document.getElementsByClassName("model-info");
        for (var i = 0; i < modelInfos.length; i++) {
            modelInfos[i].addEventListener("click", function () {
                // Retrieve product information from the clicked row
                var row = this.parentNode;
                var brand = row.childNodes[1].textContent;
                var model = row.childNodes[2].textContent;
                var year = row.childNodes[3].textContent;
                var color = row.childNodes[4].textContent;
                var price = row.childNodes[5].textContent;
                var imageSrc = row.querySelector(".product-image").src;

                // Set product information in the modal
                document.getElementById("modalBrand").textContent = brand;
                document.getElementById("modalModel").textContent = model;
                document.getElementById("modalYear").textContent = year;
                document.getElementById("modalColor").textContent = color;
                document.getElementById("modalPrice").textContent = price;

                // Set product image in the modal
                var imageContainer = document.getElementById("modalImageContainer");
                imageContainer.innerHTML = "<img src='" + imageSrc + "' alt='" + model + "' style='max-width: 200px;'>";

                // Display the modal
                modal.style.display = "block";
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Get the modal
            var modal = document.getElementById("productModal");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks on <span> (x), close the modal
            span.onclick = function () {
                modal.style.display = "none";
            };

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };

            // Add event listener to all elements with class 'model-info'
            document.querySelectorAll(".model-info").forEach(function (element) {
                element.addEventListener("click", function () {
                    // Retrieve product information from the clicked row
                    var row = this.parentNode;
                    var brand = row.childNodes[1].textContent;
                    var model = row.childNodes[2].textContent;
                    var year = row.childNodes[3].textContent;
                    var color = row.childNodes[4].textContent;
                    var price = row.childNodes[5].textContent;
                    var imageSrc = row.querySelector(".product-image").src;

                    // Set product information in the modal
                    document.getElementById("modalBrand").textContent = brand;
                    document.getElementById("modalModel").textContent = model;
                    document.getElementById("modalYear").textContent = year;
                    document.getElementById("modalColor").textContent = color;
                    document.getElementById("modalPrice").textContent = price;

                    // Set product image in the modal
                    var imageContainer = document.getElementById("modalImageContainer");
                    imageContainer.innerHTML = "<img src='" + imageSrc + "' alt='" + model + "' style='max-width: 200px;'>";

                    // Display the modal
                    modal.style.display = "block";
                });
            });

            // Add event listener to the 'Add to Cart' button
            document.getElementById("addToCartBtn").addEventListener("click", function () {
                // Get product ID from data attribute
                var productId = this.dataset.productId;
                addToCart(productId);
            });

            // Function to add item to cart via AJAX
            function addToCart(productId) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "addToCart.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        // Close modal after adding to cart
                        modal.style.display = "none";
                    }
                };
                xhr.send("product_id=" + productId);
            }
        });


    </script>

    <footer>

    </footer>

</body>

</html>

<?php
$conn->close();
?>