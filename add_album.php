<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once("config.php");
    $title = $_POST["title"];
    $description = $_POST["description"];

    $insertAlbumQuery = "INSERT INTO albums (title, description) VALUES (?, ?)";
    $stmt = $conn->prepare($insertAlbumQuery);
    $stmt->bind_param("ss", $title, $description);
    $stmt->execute();


    if ($stmt->affected_rows > 0) {
        header("Location: all_albums.php");
        echo "Альбом успешно добавлен!";
    } else {
        echo "Ошибка добавления: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
