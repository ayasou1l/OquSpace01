<?php
include 'db.php';
session_start();

$search = $_GET['search'] ?? '';

// ВРЕМЕННО УПРОЩАЕМ, чтобы не ломалось
if ($search) {
    $groups = $conn->query("SELECT * FROM groups_table WHERE name LIKE '%$search%'");
} else {
    $groups = $conn->query("SELECT * FROM groups_table ORDER BY id DESC");
}

if (!$groups) {
    die("DB ERROR: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Группы</title>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h2>👥 Группы</h2>

<!-- 🔍 ПОИСК -->
<form method="GET">
    <input type="text" name="search" placeholder="Поиск групп...">
    <button>🔍 Найти</button>
</form>

<!-- ➕ СОЗДАНИЕ -->
<div class="card">
    <h3>➕ Создать группу</h3>

    <form method="POST" action="create_group.php">
        <input type="text" name="name" placeholder="Название группы" required>
        <textarea name="description" placeholder="Описание"></textarea>
        <button type="submit">Создать</button>
    </form>
</div>

<!-- 📋 СПИСОК -->
<h3>📋 Доступные группы</h3>

<?php while($g = $groups->fetch_assoc()): ?>

<div class="announcement-card">

    <h4><?= htmlspecialchars($g['name']) ?></h4>
    <p><?= htmlspecialchars($g['description']) ?></p>

    <a href="join_group.php?id=<?= $g['id'] ?>">➕ Вступить</a>

</div>

<?php endwhile; ?>

</div>

</body>
</html>
