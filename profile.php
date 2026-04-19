<?php
session_start();
include 'db.php';
$uid = $_GET['id'] ?? $_SESSION['user_id'];

$user = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();

$reviews = $conn->query("SELECT * FROM reviews WHERE user_id=$uid");

$total = 0;
$count = 0;

while ($r = $reviews->fetch_assoc()) {
    $total += $r['rating'];
    $count++;
}

$rating = $count > 0 ? round($total / $count, 1) : "Нет рейтинга";
if (isset($_POST['upload_avatar'])) {

    $user_id = $_SESSION['user_id'];

    $file = $_FILES['avatar'];
    $filename = time() . '_' . $file['name'];
    $path = "uploads/" . $filename;

    move_uploaded_file($file['tmp_name'], $path);

    $stmt = $conn->prepare("UPDATE users SET avatar=? WHERE id=?");
    $stmt->bind_param("si", $path, $user_id);
    $stmt->execute();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$announcements = $conn->query("SELECT * FROM announcements WHERE user_id = $user_id ORDER BY created_at DESC");
$user_id = $user['id'];

// ОБЪЯВЛЕНИЯ
$announcements = $conn->query("
    SELECT * FROM announcements 
    WHERE user_id = $user_id
    ORDER BY id DESC
");

// МАТЕРИАЛЫ
$materials = $conn->query("
    SELECT * FROM materials 
    WHERE user_id = $user_id
    ORDER BY id DESC
");

// ВИДЕО
$videos = $conn->query("
    SELECT * FROM video_lessons 
    WHERE user_id = $user_id
    ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль: <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h1>Мой профиль</h1>
    <a href="index.php">← На главную</a>
</nav>

        <div class="profile-page">

    <!-- АВАТАР + ИНФО -->
    <div class="profile-top">

        <div class="profile-avatar">
            <img src="<?= $user['avatar'] ?? 'default.png' ?>">
        </div>

        <div class="profile-info">
            <h2><?= htmlspecialchars($user['username']) ?></h2>
            <p><?= htmlspecialchars($user['email']) ?></p>
            <p>📚 <?= htmlspecialchars($user['skill_category']) ?></p>
        </div>

    </div>

    <!-- 👇 ПОДПИСКА (САМАЯ ВАЖНАЯ, ВЫШЕ ВСЕХ) -->
    <div class="follow-box">
        <a href="follow.php?id=<?= $user['id'] ?>" class="btn-primary">
            ➕ Подписаться
        </a>
    </div>

    <!-- 📢 ОБЪЯВЛЕНИЯ -->
    <div class="accordion">

        <button class="accordion-btn">📢 Мои объявления</button>

        <div class="accordion-content">
            <?php while($row = $announcements->fetch_assoc()): ?>
                <div class="card">
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

    <!-- 📚 ПУБЛИКАЦИИ -->
    <div class="section">

        <?php
$user_id = $_SESSION['user_id'];

$materials = $conn->query("
SELECT * FROM materials 
WHERE user_id = $user_id
ORDER BY created_at DESC
");
?>

<h3>📚 Публикации</h3>

<?php if ($materials && $materials->num_rows > 0): ?>
    <?php while ($m = $materials->fetch_assoc()): ?>

        <div class="post-card">
            <h4><?= htmlspecialchars($m['title']) ?></h4>
            <p><?= htmlspecialchars($m['description']) ?></p>

            <a href="<?= $m['file_path'] ?>" target="_blank">
                📥 Скачать файл
            </a>
        </div>

    <?php endwhile; ?>
<?php else: ?>
    <p>Нет материалов</p>
<?php endif; ?>
    </div>

    <!-- 🎥 ВИДЕО -->
    <div class="section">
        

        <?php
$user_id = $_SESSION['user_id'];

$videos = $conn->query("
SELECT * FROM video_lessons 
WHERE user_id = $user_id
ORDER BY id DESC
");
?>

<h3>🎥 Видео уроки</h3>

<?php if ($videos && $videos->num_rows > 0): ?>
    <?php while ($v = $videos->fetch_assoc()): ?>

        <div class="post-card">
            <h4><?= htmlspecialchars($v['title']) ?></h4>
            <p><?= htmlspecialchars($v['description']) ?></p>

            <a href="<?= htmlspecialchars($v['video_url']) ?>" target="_blank">
                ▶ Смотреть видео
            </a>
        </div>

    <?php endwhile; ?>
<?php else: ?>
    <p>Нет видеоуроков</p>
<?php endif; ?>

    </div>

</div>
</body>
</html>
<script>
document.querySelectorAll(".accordion-btn").forEach(btn => {
    btn.onclick = () => {
        btn.parentElement.classList.toggle("active");
    }
});
</script>
