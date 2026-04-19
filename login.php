<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $skill_category = $_POST['skill_category'];

    $file = $_FILES['skill_proof'];
    $filename = time() . '_' . basename($file['name']);
    $destination = 'uploads/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Пользователь с таким именем или email уже существует.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, skill_category, skill_proof) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $password, $skill_category, $destination);
            if ($stmt->execute()) {
                $success = "Регистрация прошла успешно! Теперь войдите.";
            } else {
                $error = "Ошибка регистрации: " . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $error = "Ошибка загрузки файла.";
    }
}

if (isset($_POST['login'])) {
    $login = $_POST['login_username'];
    $password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = "Неверный пароль.";
        }
    } else {
        $error = "Пользователь не найден.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>OquSpace — Вход и Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📚 OquSpace</h1>
        <p class="slogan">Меняйся знаниями, учись бесплатно!</p>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <div class="forms-container">
            <div class="form-box">
                <h2>Регистрация</h2>
                <form action="login.php" method="post" enctype="multipart/form-data">
                    <input type="text" name="username" placeholder="Имя пользователя" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Пароль" required>

                    <label for="skill_category">Чему могу учить:</label>
                    <select name="skill_category" required>
                        <option value="">-- Выбери предмет --</option>
                        <option value="Математика">Математика</option>
                        <option value="Биология">Биология</option>
                        <option value="Физика">Физика</option>
                        <option value="Английский">Английский</option>
                        <option value="Программирование">Программирование</option>
                        <option value="Музыка">Музыка</option>
                    </select>

                    <label for="skill_proof">Доказательство (сертификат, диплом):</label>
                    <input type="file" name="skill_proof" accept=".pdf,.jpg,.jpeg,.png" required>

                    <button type="submit" name="register">Зарегистрироваться</button>
                </form>
            </div>

            <div class="form-box">
                <h2>Вход</h2>
                <form action="login.php" method="post">
                    <input type="text" name="login_username" placeholder="Имя или Email" required>
                    <input type="password" name="login_password" placeholder="Пароль" required>
                    <button type="submit" name="login">Войти</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
