<?php

require_once("config.php");

$imageId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$imageId) {
    echo "ID изображения не указано.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $editTitle = $_POST["edit_title"];
    $editDescription = $_POST["edit_description"];

    $updateImageQuery = "UPDATE images SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($updateImageQuery);
    $stmt->bind_param("ssi", $editTitle, $editDescription, $imageId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Данные изображения успешно обновлены!";
    } else {
        echo "Ошибка обновления данных изображения: " . $stmt->error;
    }

    $stmt->close();


    $albumIdQuery = "SELECT album_id FROM images WHERE id = ?";
    $stmtAlbumId = $conn->prepare($albumIdQuery);
    $stmtAlbumId->bind_param("i", $imageId);
    $stmtAlbumId->execute();
    $resultAlbumId = $stmtAlbumId->get_result();
    $albumIdData = $resultAlbumId->fetch_assoc();

    if (!$albumIdData) {
        echo "Не удалось получить albumId для изображения с ID $imageId.";
        exit;
    }

    $albumId = $albumIdData['album_id'];


    $stmtAlbumId->close();
    $conn->close();

    header("Location: view_album.php?id=" . $albumId);
    exit;
} else {
    echo "Недопустимый метод запроса.";
    exit;
}
