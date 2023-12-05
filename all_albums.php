<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums List</title>
    <link href="/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .albums-container {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .album-link {
            display: block;
            margin-bottom: 10px;
        }

        .album-link:hover {
            text-decoration: none;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="albums-container">
        <?php
        require_once("config.php");

        $selectAlbumsQuery = "SELECT * FROM albums";
        $result = $conn->query($selectAlbumsQuery);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<a class='btn btn-outline-secondary album-link' href='view_album.php?id={$row['id']}'>{$row['title']}</a>";
            }
        } else {
            echo "<p>No albums found.</p>";
        }

        $conn->close();
        ?>
        <a href="/">назад</a>
    </div>

</body>

</html>