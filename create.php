<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (isset($_POST['create'])) {

    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO announcements (user_id, title, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $description);
    $stmt->execute();

    header('Location: index.php');
    exit;
}

$message = '';

if (isset($_POST['create'])) {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO announcements (user_id, title, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $description);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        $message = "Ошибка: " . $conn->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать объявление</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page">
        <nav class="navbar">
    <form class="global-search" method="GET" action="search_users.php">

       <input type="text" name="q" placeholder="🔍 Найти пользователя...">

    </form>
    <div class="logo">📚 OquSpace</div>

    <div class="nav-links">
        <a href="index.php">🏠 Лента</a>
        <a href="create.php" class="active">📢 Объявления</a>
        <a href="create_material.php">📚 Учебные материалы</a>
        <a href="create_video.php">🎥 Видео уроки</a>
        <a href="groups.php">👥 Группы</a>
        <a href="profile.php">👤 Профиль</a>
    </div>

    <div class="nav-right">
        <a href="logout.php" class="logout-link">🚪 Выйти</a>
    </div>

</nav>
            <h2 class="page-title">📢 Создание объявления</h2>

        <?php if ($message): ?>
            <div class="alert error"><?= $message ?></div>
        <?php endif; ?>

        <div class="card">    
        <form method="post" class="create-form">
            <label for="title">Заголовок (например: "Меняю 3 часа биологии на 2 часа математики")</label>
            <input type="text" name="title" id="title" required placeholder="Я хочу научиться...">

            <label for="description">Описание и детали трейда</label>
            <textarea name="description" id="description" rows="6" required placeholder="Опиши, что ты можешь дать и что хочешь получить. Можно прикрепить ссылки на материалы в описании."></textarea>

            <button type="submit" name="create" class="btn-primary">Опубликовать предложение</button>
        </form>
    </div>
   </div>
</body>
</html>
