<?php
session_start();
$host = 'localhost';
$db = 'kursovaya';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = '';
$email = '';
$phone = '';

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
    $username = $user_data['username'];
    $email = $user_data['email'];
    $phone = $user_data['phone'] ?? '';
} else {
    $username = "Неизвестный пользователь";
    $email = "Неизвестный email";
    $phone = '';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone'])) {
    $phone = trim($_POST['phone']);
    $update_sql = "UPDATE users SET phone = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $phone, $user_id);

    if ($update_stmt->execute()) {
        $success_message = "Номер телефона успешно сохранен!";
        $stmt->close();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $username = $user_data['username'];
            $email = $user_data['email'];
            $phone = $user_data['phone'] ?? ''; 
        }
    } else {
        $error_message = "Ошибка при сохранении номера телефона: " . $conn->error;
    }

    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-logo">
        <div class="logo-text"><a href="index.html">Fleys</a></div>
        </div>
        <ul class="navbar-links">
            <li><a href="profile.php">Профиль</a></li>
            <li><a href="cart.html">Корзина</a></li>
            <li><a href="categories.php">Категории</a></li>
        </ul>
        <div class="navbar-logout">
            <a href="logout.php">Выйти</a>
        </div>
    </nav>
    <main class="main-content">
        <h1>Ваш профиль</h1>
        <div class="profile-info">
            <?php if (isset($success_message)): ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <p><strong>Имя:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Телефон:</strong> <?php echo htmlspecialchars($phone); ?></p>
            <form method="POST" action="">
                <label for="phone"><strong>Изменить телефон:</strong></label>
                <input type="text" id="phone" name="phone" placeholder="Введите ваш номер телефона" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                <button type="submit" class="save-button">Сохранить</button>
            </form>
        </div>
    </main>
</body>
</html>