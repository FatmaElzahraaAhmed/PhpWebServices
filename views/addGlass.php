<?php
require_once "../vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
try {
    $capsule->addConnection([
        "driver" => DRIVER,
        "host" => HOST,
        "database" => DATABASE,
        "username" => USERNAME,
        "password" => PASSWORD
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
} catch (Exception $ex) {
    die($ex->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['photo'])) {
        $photoPath = '../Resources/images/';
        $photoName = $_FILES['photo']['name'];
        $photoTemp = $_FILES['photo']['tmp_name'];
        $photoDestination = $photoPath . $photoName;

        if (move_uploaded_file($photoTemp, $photoDestination)) {
            $highestId = $capsule->table("items")->max('id');

            $newId = $highestId + 1;

            $productCode = htmlspecialchars(trim($_POST['product_code']));
            $productName = htmlspecialchars(trim($_POST['product_name']));
            $listPrice = floatval($_POST['list_price']);
            $reorderLevel = isset($_POST['reorder_level']) ? intval($_POST['reorder_level']) : null;
            $unitsInStock = isset($_POST['units_in_stock']) ? intval($_POST['units_in_stock']) : null;
            $category = htmlspecialchars(trim($_POST['category']));
            $country = htmlspecialchars(trim($_POST['country']));
            $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : null;
            $discontinued = isset($_POST['discontinued']) ? 1 : 0;
            $date = isset($_POST['date']) ? $_POST['date'] : null;

            $capsule->table("items")->insert([
                'id' => $newId,
                'PRODUCT_code' => $productCode,
                'product_name' => $productName,
                'Photo' => $photoName,
                'list_price' => $listPrice,
                'reorder_level' => $reorderLevel,
                'Units_In_Stock' => $unitsInStock,
                'category' => $category,
                'CouNtry' => $country,
                'Rating' => $rating,
                'discontinued' => $discontinued,
                'date' => $date,
            ]);

            echo "Product added successfully";
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "No file uploaded or an error occurred during upload.";
    }
}
?>

<html>

<body>
    <form action="addGlass.php" method="POST" enctype="multipart/form-data">
        <label for="product_code">Product Code:</label>
        <input type="text" name="product_code" required><br>
        <br>
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" required><br>
        <br>

        <label for="photo">Photo:</label>
        <input type="file" name="photo" accept="image/*" required><br>
        <br>

        <label for="list_price">List Price:</label>
        <input type="number" step="0.01" name="list_price" required><br>
        <br>

        <label for="reorder_level">Reorder Level:</label>
        <input type="number" name="reorder_level"><br>

        <br>
        <label for="units_in_stock">Units In Stock:</label>
        <input type="number" name="units_in_stock"><br>
        <br>

        <label for="category">Category:</label>
        <input type="text" name="category"><br>
        <br>

        <label for="country">Country:</label>
        <input type="text" name="country"><br>
        <br>

        <label for="rating">Rating:</label>
        <input type="number" step="0.01" name="rating"><br>
        <br>

        <label for="discontinued">Discontinued:</label>
        <input type="checkbox" name="discontinued"><br>
        <br>

        <label for="date">Date:</label>
        <input type="datetime-local" name="date"><br>
        <br>

        <input type="submit" value="Add Product">
    </form>

    <a href="../index.php">Back to list</a>
</body>

</html>