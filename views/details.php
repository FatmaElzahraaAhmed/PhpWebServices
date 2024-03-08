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
$productId = isset($_GET['id']) ? (int)$_GET['id'] : null;

$product = $capsule->table("items")->find($productId);

?>

<!DOCTYPE html>
<html lang="en">


<body>
    <center>
        <h2>Product Details</h2>
        <?php if ($product) : ?>
            <table border="1">
                <tr>
                    <td>ID</td>
                    <td>PRODUCT Code</td>
                    <td>Name</td>
                    <td>Photo</td>
                    <td>List Price</td>
                    <td>Reorder Level</td>
                    <td>Units In Stock</td>
                    <td>Category</td>
                    <td>Country</td>
                    <td>Rating</td>
                    <td>Discontinued</td>
                    <td>Date</td>
                </tr>
                <tr>
                    <td><?= $product->id ?></td>
                    <td><?= $product->PRODUCT_code ?></td>
                    <td><?= $product->product_name ?></td>
                    <td><img src="../Resources/images/<?= $product->Photo ?>" alt=""></td>
                    <td><?= $product->list_price ?></td>
                    <td><?= $product->reorder_level ?></td>
                    <td><?= $product->Units_In_Stock ?></td>
                    <td><?= $product->category ?></td>
                    <td><?= $product->CouNtry ?></td>
                    <td><?= $product->Rating ?></td>
                    <td><?= $product->discontinued ?></td>
                    <td><?= $product->date ?></td>
                </tr>
            </table>
        <?php else : ?>
            <p>Product not found.</p>
        <?php endif; ?>
        <br>
        <a href="../index.php">Back to List</a>
    </center>
</body>

</html>