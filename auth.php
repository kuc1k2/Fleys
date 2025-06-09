<?php
session_start();
require 'db.php';

$login = $_POST['login'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
$stmt->execute([$login]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['pass'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = ($user['role'] === 'admin' || $user['is_admin'] == 1);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_admin'] = true;
    $_SESSION['role'] = 'admin'; 
    
    header("Location: categories.php");
    exit;
} else {
    header("Location: login.php?error=invalid_credentials");
    exit;
}