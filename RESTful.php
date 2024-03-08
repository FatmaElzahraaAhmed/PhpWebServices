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
} catch (Exception $e) {
    die("Error " . $e->getMessage());
}



header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo handleGet();
        break;
    case 'POST':
        echo handlePost($capsule);
        break;
    default:
        http_response_code(405);
        echo "Method not supported";
        break;
}



function handleGet()
{
    $urlParts = explode('/', $_SERVER['REQUEST_URI']);

    $resource = $urlParts[2];

    $resourceId = (isset($urlParts[3]) && is_numeric($urlParts[3])) ? (int) $urlParts[3] : 0;

    global $capsule;

    if ($resource === 'glasses') {
        if ($resourceId == 0) {;
            return json_encode($capsule->table("items")->select()->get());
        }

        $item = $capsule->table("items")->select()->where("id", $resourceId)->first();

        if (empty($item)) {
            return ["error" => "No item found"];
        }
        return json_encode($item);
    } else {
        http_response_code(404);
        return json_encode(["error" => "Resource not found"]);
    }
}
function handlePost($capsule)
{
    $urlParts = explode('/', $_SERVER['REQUEST_URI']);

    if (isset($urlParts[2])) {
        $resource = $urlParts[2];


        if ($resource === 'glasses') {
            if (empty($_POST)) {
                http_response_code(400);
                return json_encode(["error" => "No data sent"]);
            }
            $highestId = $capsule->table("items")->max('id');

            $newId = $highestId + 1;

            $file = $_FILES['Photo'];
            $uploadDirectory = "Resources/images/";
            $targetFile = $uploadDirectory . basename($file['name']);

            $data = $_POST;
            $data['id'] = $newId;
            $data['Photo'] = $targetFile;

            $capsule->table("items")->insert($data);

            return json_encode($data);
        }
    }
    http_response_code(404);
    return json_encode(["error" => "Resource not found"]);
}
