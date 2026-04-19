<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ЛЕНТА ОБЪЯВЛЕНИЙ
$sql = "SELECT a.id, a.title, a.description, a.created_at, u.username, u.skill_category
        FROM announcements a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("SQL ERROR: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>OquSpace — Лента</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="page">

    <!-- NAVBAR -->
    <nav class="navbar">
    <form class="global-search" method="GET" action="search_users.php">

    <input type="text" name="q" placeholder="🔍 Найти пользователя...">

</form>
        <div class="logo">👩‍🏫 OquSpace</div>

        <div class="nav-links">
            <a href="index.php">🏠 Лента</a>
            <a href="create.php">📢 Объявления</a>
            <a href="create_material.php">📚 Учебные материалы</a>
            <a href="create_video.php">🎥 Видео уроки</a>
            <a href="groups.php">👥 Группы</a>
            <a href="profile.php">👤 Профиль</a>
        </div>

        <div class="nav-right">
            <a href="logout.php" class="logout-link">🚪 Выйти</a>
        </div>

    </nav>

    <h2>📢 Лента объявлений</h2>

    <?php while ($row = $result->fetch_assoc()): ?>

        <div class="announcement-card">

            <div class="card-header">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <span class="badge"><?= htmlspecialchars($row['skill_category']) ?></span>
            </div>

            <p class="author">👤 <?= htmlspecialchars($row['username']) ?></p>

            <p class="description"><?= nl2br(htmlspecialchars($row['description'])) ?></p>

            <div class="card-footer">
                <small>📅 <?= $row['created_at'] ?></small>
            </div>

            <!-- ОТКЛИК -->
            <form method="POST" action="respond.php">
                <input type="hidden" name="announcement_id" value="<?= $row['id'] ?>">
                <input type="text" name="message" placeholder="Написать отклик...">
                <button type="submit">Отправить</button>
            </form>

            <!-- ОТВЕТЫ -->
            <?php
            $responses = $conn->query("
                SELECT ar.*, u.username
                FROM announcement_responses ar
                JOIN users u ON ar.user_id = u.id
                WHERE ar.announcement_id = {$row['id']}
            ");

            if ($responses) {
                while ($r = $responses->fetch_assoc()):
            ?>
                <div class="card">
                    <b><?= htmlspecialchars($r['username']) ?>:</b>
                    <?= htmlspecialchars($r['message']) ?>
                </div>
            <?php
                endwhile;
            }
            ?>

        </div>

    <?php endwhile; ?>

</div>

</body>
</html>
