<?php
define('DB_HOST', 'ispolkom');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ispolkom');


$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);


if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
