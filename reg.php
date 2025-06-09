<?php
require 'db.php';

$emailError = '';
$usernameError = '';
$loginError = '';
$passwordError = '';
$confirmPasswordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    if (empty($email) || empty($username) || empty($login) || empty($password) || empty($confirmPassword)) {
        $emailError = "Все поля должны быть заполнены!";
    } else {
        if ($password !== $confirmPassword) {
            $confirmPasswordError = "Пароли не совпадают!";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $existingEmail = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingEmail) {
                    $emailError = "Пользователь с таким email уже зарегистрирован!";
                }
                $stmt = $pdo->prepare("SELECT id FROM users WHERE login = :login");
                $stmt->execute(['login' => $login]);
                $existingLogin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingLogin) {
                    $loginError = "Данный логин уже занят!";
                }

                if (empty($emailError) && empty($loginError)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (email, username, login, pass) VALUES (:email, :username, :login, :pass)");
                    $stmt->execute([
                        'email' => $email,
                        'username' => $username,
                        'login' => $login,
                        'pass' => $hashedPassword
                    ]);
                    $user_id = $pdo->lastInsertId();
                    session_start();
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    header("Location: profile.php");
                    exit();
                }
            } catch (PDOException $e) {
                die("Ошибка при регистрации: " . $e->getMessage());
            }
        }
    }
}
include 'register.php';
?>