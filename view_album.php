<?php
session_start();
require_once("config.php");

$albumId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$albumId) {
    echo "ID альбома не указано.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $uploadDir = "uploads/";

    if (isset($_FILES["image"])) {
        $uploadPath = $uploadDir . basename($_FILES["image"]["name"]);

        $imageFileType = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "jpeg", "png", "gif", "jfif");

        if (!in_array($imageFileType, $allowedExtensions)) {
            echo "Только файлы JPG, JPEG, PNG и GIF разрешены.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $uploadPath)) {
                $insertImageQuery = "INSERT INTO images (album_id, title, description, file_path) VALUES (?, ?, ?, ?)";
                $title = $_POST["title"];
                $description = $_POST["description"];

                $stmt = $conn->prepare($insertImageQuery);
                $stmt->bind_param("isss", $albumId, $title, $description, $uploadPath);
                $stmt->execute();
                $stmt->close();
                header("Location: view_album.php?id=" . $albumId);
                exit;
            } else {
                echo "Ошибка при загрузке файла.";
            }
        }
    }
}

$albumQuery = "SELECT * FROM albums WHERE id = ?";
$stmt = $conn->prepare($albumQuery);
$stmt->bind_param("i", $albumId);
$stmt->execute();
$result = $stmt->get_result();
$album = $result->fetch_assoc();

if (!$album) {
    echo "Альбом с ID $albumId не найден.";
    exit;
}

$imagesQuery = "SELECT * FROM images WHERE album_id = ?";
$stmt = $conn->prepare($imagesQuery);
$stmt->bind_param("i", $albumId);
$stmt->execute();
$imagesResult = $stmt->get_result();
$images = $imagesResult->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        h2 {
            color: #007BFF;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .image-container {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .image-container img {
            width: 100%;
            height: auto;
            max-height: 150px;
            transition: transform 0.3s ease-in-out;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .modal-content {
            max-width: 50%;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .modal-image {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100vh;
        }

        form {
            margin-top: 10px;
        }

        label {
            margin-right: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"],
        textarea {
            margin-bottom: 10px;
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
    <title>View Album</title>
</head>

<body>
    <h2><?= $album['title'] ?></h2>
    <p><?= $album['description'] ?></p>

    <div class='grid-container'>
        <?php foreach ($images as $image) : ?>
            <div class='image-container' onclick='openModal(<?= $image['id'] ?>)'>
                <img src='<?= $image['file_path'] ?>' alt='<?= $image['title'] ?>'>
                <div class='modal' id='modal<?= $image['id'] ?>' onclick='closeModal(<?= $image['id'] ?>)'>
                    <div class='modal-content'>
                        <span class='close-modal'>&times;</span>
                        <img src='<?= $image['file_path'] ?>' class='modal-image' alt='<?= $image['title'] ?>'>
                        <form action='edit_image.php?id=<?= $image['id'] ?>' method='post'>
                            <label for='edit_title'>Заголовок:</label>
                            <input type='text' name='edit_title' value='<?= $image['title'] ?>'>
                            <br>
                            <label for='edit_description'>Описание:</label>
                            <input type='text' name='edit_description' value='<?= $image['description'] ?>'>
                            <br>
                            <button type='submit'>Сохранить</button>
                        </form>

                        <p>Комментарии:</p>
                        <ul>
                            <?php
                            $commentsQuery = "SELECT * FROM comments WHERE image_id = ?";
                            $stmtComments = $conn->prepare($commentsQuery);
                            $stmtComments->bind_param("i", $image['id']);
                            $stmtComments->execute();
                            $commentsResult = $stmtComments->get_result();
                            $comments = $commentsResult->fetch_all(MYSQLI_ASSOC);

                            foreach ($comments as $comment) {
                                echo "<li>{$comment['username']} : {$comment['comment']}</li>";
                            }
                            ?>
                        </ul>

                        <form action='add_comment.php' method='post'>
                            <input type='hidden' name='image_id' value='<?= $image['id'] ?>'>
                            <label for='comment'>Никнейм:</label>
                            <input type='text' name='username' required>
                            <label for='comment'>Добавить комментарий:</label>
                            <input type='text' name='comment' required>
                            <button type='submit'>Добавить</button>
                        </form>

                        <p>Лайки: <?= $image['likes'] ?></p>

                        <form action='like_image.php?image_id=<?= $image['id'] ?>' method='post'>
                            <button type='submit'>Лайк</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="view_album.php?id=<?= $albumId ?>" method="post" enctype="multipart/form-data">
        <label for="image">Выберите изображение:</label>
        <input type="file" name="image" required>
        <br>
        <label for="title">Заголовок:</label>
        <input type="text" name="title">
        <br>
        <label for="description">Описание:</label>
        <input type="text" name="description" required>
        <br>
        <button type="submit">Загрузить изображение</button>
    </form>

    <a href="all_albums.php">Показать альбомы</a>

    <script>
        function openModal(imageId) {
            var modal = document.getElementById('modal' + imageId);
            modal.style.display = 'flex';
        }

        function closeModal(imageId) {
            var modal = document.getElementById('modal' + imageId);
            modal.style.display = 'none';
        }
    </script>
</body>

</html>