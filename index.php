<?php

require_once("config.php");

$path = isset($_GET['path']) ? $_GET['path'] : '';

$routes = [
    // 'add_album' => 'add_album.php',
    'view_album' => 'view_album.php',
    'add_album_form' => 'add_album_form.php',
];

if (array_key_exists($path, $routes)) {
    require_once($routes[$path]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
        <a class="btn btn-outline-secondary" href="all_albums.php">Показать альбомы</a>
        <a class="btn btn-outline-secondary" href="add_album_form.php">Добавить альбом</a>
    </div>
</body>

</html>