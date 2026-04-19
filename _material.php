<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['upload'])) {

    $user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$description = $_POST['description'];

$file = $_FILES['file']['name'];
$tmp = $_FILES['file']['tmp_name'];

$path = "uploads/" . $file;
move_uploaded_file($tmp, $path);

$stmt = $conn->prepare("
INSERT INTO materials (user_id, title, description, file_path)
VALUES (?, ?, ?, ?)
");

$stmt->bind_param("isss", $user_id, $title, $description, $path);
$stmt->execute();
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Материалы</title>
</head>
<body>

<nav class="navbar">
<form class="global-search" method="GET" action="search_users.php">

    <input type="text" name="q" placeholder="🔍 Найти пользователя...">

</form>
    <div class="logo">👩‍🏫 OquSpace</div>

    <div class="nav-links">

        <a href="index.php">🏠 Лента</a>
        <a href="create.php" class="active">📢 Объявления</a>
        <a href="create_material.php">📚 Учебные материалы</a>
        <a href="create_video.php">🎥 Видео уроки</a>
        <a href="create_group.php">👥 Группы</a>
        <a href="profile.php">👤 Профиль</a>

    </div>

    <div class="nav-right">
        <a href="logout.php" class="logout-link">🚪 Выйти</a>
    </div>

</nav>

<div class="page-center">

    <div class="form-box">

        <h2>📚 Загрузить материал</h2>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="title" placeholder="Название материала">

            <textarea name="description" placeholder="Описание"></textarea>

            <input type="file" name="file">

            <button type="submit">Опубликовать</button>

        </form>

    </div>

</div>

</body>
</html>
