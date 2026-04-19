<?php
session_start();
include 'db.php';

$group_id = $_GET['id'];
$user_id = $_SESSION['user_id'];


$group = $conn->query("
    SELECT * FROM groups_table WHERE id=$group_id
")->fetch_assoc();


$messages = $conn->query("
    SELECT gm.*, u.username 
    FROM group_messages gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id=$group_id
    ORDER BY gm.id ASC
");

$members = $conn->query("
    SELECT u.username 
    FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id=$group_id
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $group['name'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h2>👥 <?= htmlspecialchars($group['name']) ?></h2>
<p><?= htmlspecialchars($group['description']) ?></p>

<hr>

<h3>👤 Участники</h3>

<?php while($m = $members->fetch_assoc()): ?>
    <span class="badge"><?= $m['username'] ?></span>
<?php endwhile; ?>

<hr>

<h3>💬 Чат</h3>

<div class="card">

<?php while($msg = $messages->fetch_assoc()): ?>
    <p><b><?= $msg['username'] ?>:</b> <?= htmlspecialchars($msg['message']) ?></p>
<?php endwhile; ?>

</div>

<form method="post" action="send_group_message.php">

    <input type="hidden" name="group_id" value="<?= $group_id ?>">
    <input type="text" name="message" placeholder="Написать сообщение...">

    <button>Отправить</button>

</form>

</div>

</body>
</html>
