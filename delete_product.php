<?php
require 'db.php';

session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;

if (!$isAdmin) {
    header("Location: categories.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $product_id]);

        header("Location: categories.php?category=all&message=Товар успешно удален");
        exit;
    } catch (PDOException $e) {
        die("Ошибка при удалении товара: " . $e->getMessage());
    }
}
?>