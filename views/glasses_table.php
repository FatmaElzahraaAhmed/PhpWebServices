<?php
require_once "vendor/autoload.php";

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

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

if (isset($_GET['show_all'])) {
    $items = $capsule->table("items")->select()->get();
    $searchResults = [];
    $hasMoreRecords = false;
} elseif (!empty($searchTerm)) {
    $searchResults = $capsule->table("items")
        ->where('product_name', 'LIKE', '%' . $searchTerm . '%')
        ->orWhere('PRODUCT_code', 'LIKE', '%' . $searchTerm . '%')
        ->orWhere('category', 'LIKE', '%' . $searchTerm . '%')
        ->orWhere('CouNtry', 'LIKE', '%' . $searchTerm . '%')
        ->orWhere('id', 'LIKE', '%' . $searchTerm . '%')
        ->get();

    $items = [];
    $hasMoreRecords = false;
} else {
    $items = $capsule->table("items")->select()->skip($offset)->take(5)->get();

    $hasMoreRecords = $capsule->table("items")->select()->offset($offset + 5)->limit(1)->exists();
}

require_once("views/glasses_table.php");
?>

<body>
    <center>
        <form action="index.php" method="GET">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search">
            <button type="submit">Submit</button>
            <button type="submit" name="show_all">Show All</button>
        </form>

        <?php if (!empty($searchResults) && count($searchResults) > 0) : ?>
            <table border="1">
                <tr>
                    <td>ID</td>
                    <td>Name</td>
                    <td>Details</td>
                </tr>
                <?php foreach ($searchResults as $result) : ?>
                    <tr>
                        <td><?= $result->id ?></td>
                        <td><?= $result->product_name ?></td>
                        <td><a href="views/details.php?id=<?= $result->id ?>">More</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <a href="index.php">Back to List</a>
        <?php elseif (!empty($items)) : ?>
            <table border="1">
                <tr>
                    <td>ID</td>
                    <td>Name</td>
                    <td>Details</td>
                </tr>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td><?= $item->id ?></td>
                        <td><?= $item->product_name ?></td>
                        <td><a href="views/details.php?id=<?= $item->id ?>">More</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php if ($offset >= 5) : ?>
                <a href="?offset=<?= $offset - 5 ?>">Previous</a>
            <?php endif; ?>
            <?php if ($hasMoreRecords) : ?>
                <a href="?offset=<?= $offset + 5 ?>">Next</a>
            <?php endif; ?>
        <br>
        <a href="index.php">Back to List</a>
        <br>
        <a href="views/addGlass.php">Add new Glass</a>
        <?php else : ?>
            <center>No items found.</center>
        <?php endif; ?>
    </center>
</body>
