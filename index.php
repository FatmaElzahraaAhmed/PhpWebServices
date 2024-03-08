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

if (!empty($searchTerm)) {
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