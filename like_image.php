<?php
session_start();
require_once("config.php");

$imageId = isset($_GET['image_id']) ? $_GET['image_id'] : null;

if (!$imageId) {
    echo "ID изображения не указано.";
    exit;
}

$likesQuery = "SELECT likes FROM images WHERE id = ?";
$stmtLikes = $conn->prepare($likesQuery);
$stmtLikes->bind_param("i", $imageId);
$stmtLikes->execute();
$resultLikes = $stmtLikes->get_result();
$likesData = $resultLikes->fetch_assoc();

$currentLikes = $likesData['likes'];

if (isset($_SESSION['liked_images'][$imageId])) {
    $currentLikes--;
    unset($_SESSION['liked_images'][$imageId]);
} else {
    $currentLikes++;
    $_SESSION['liked_images'][$imageId] = true;
}

$updateLikesQuery = "UPDATE images SET likes = ? WHERE id = ?";
$stmtUpdateLikes = $conn->prepare($updateLikesQuery);
$stmtUpdateLikes->bind_param("ii", $currentLikes, $imageId);
$stmtUpdateLikes->execute();

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
