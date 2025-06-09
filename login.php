<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$error = $_GET['error'] ?? '';
$errorMessage = '';
switch ($error) {
    case 'invalid_password':
        $errorMessage = 'Неверный пароль';
        break;
    case 'user_not_found':
        $errorMessage = 'Пользователь не найден';
        break;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница авторизации</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.google.com/share?selection.family=Grechen+Fuemen" rel="stylesheet">
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-t">Fleys</div>
        <h3>Магазин периферии</h3>
    </div>
    <div class="login">
        <h2>Авторизация</h2>
        <?php if ($errorMessage): ?>
            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        <form action="auth.php" method="POST">    
            <input type="text" id="login" name="login" placeholder="Логин" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
            <br><br>
            <input type="password" id="password" name="password" placeholder="Пароль" required>
            <br><br>
            <button type="submit">Войти</button>
        </form>
        <a href="register.php">Регистрация</a>
    </div>
</body>
</html>