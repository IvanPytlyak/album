<?php
session_start();
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $imageId = isset($_POST['image_id']) ? $_POST['image_id'] : null;
    $commentText = isset($_POST['comment']) ? $_POST['comment'] : null;

    $userName = isset($_POST['username']) ? $_POST['username'] : null;

    if (!$imageId || !$commentText) {
        echo "Недостаточно данных для добавления комментария.";
        exit;
    }

    $addCommentQuery = "INSERT INTO comments (image_id, username, comment) VALUES (?, ?, ?)";
    $stmtAddComment = $conn->prepare($addCommentQuery);
    $stmtAddComment->bind_param("iss", $imageId, $userName, $commentText);
    $stmtAddComment->execute();

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

    header("Location: view_album.php?id=" . $albumId);
    exit;
} else {
    echo "Недопустимый метод запроса.";
    exit;
}
