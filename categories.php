<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$isadmin = ($_SESSION['is_admin'] ?? false) && ($_SESSION['username'] ?? '') === 'Admin';

$category = $_GET['category'] ?? 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product']) && $isadmin) {
    try {
        $product_id = (int)$_POST['product_id'];
        
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);

        if ($stmt->rowCount() > 0 && !empty($product['image']) && file_exists($product['image'])) {
            unlink($product['image']);
        }
        
        $_SESSION['message'] = "Товар успешно удалён";
        header("Location: categories.php?category=$category");
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['message'] = "Ошибка при удалении: " . $e->getMessage();
        header("Location: categories.php?category=$category");
        exit;
    }
}

try {
    if ($category === 'all') {
        $stmt = $pdo->query("SELECT * FROM products");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->execute([$category]);
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка при загрузке товаров: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Категории товаров</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .add-to-cart {
            width: 100%;
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        .add-to-cart:hover {
            background-color: #218838;
        }
    </style>
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
            <?php if ($isadmin): ?>
                <li><a href="admin_panel.php">Админ-панель</a></li>
            <?php endif; ?>
        </ul>
        <div class="navbar-logout">
            <a href="logout.php">Выйти</a>
        </div>
    </nav>

    <main class="main-content">
        <h1>Категории товаров</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="category-select">
            <select onchange="location = this.value;">
                <option value="categories.php?category=all" <?= $category === 'all' ? 'selected' : '' ?>>Все товары</option>
                <option value="categories.php?category=keyboard" <?= $category === 'keyboard' ? 'selected' : '' ?>>Клавиатуры</option>
                <option value="categories.php?category=mouse" <?= $category === 'mouse' ? 'selected' : '' ?>>Мыши</option>
                <option value="categories.php?category=headphones" <?= $category === 'headphones' ? 'selected' : '' ?>>Наушники</option>
            </select>
        </div>

        <div class="products-list">
            <?php if (empty($products)): ?>
                <p>Товары не найдены.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php endif; ?>
                        
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                        <p class="price"><?= htmlspecialchars($product['price']) ?> руб.</p>
                        
                        <?php if ($isadmin): ?>
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" name="delete_product" class="btn-delete"
                                        onclick="return confirm('Удалить этот товар?')">
                                    Удалить
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="add-to-cart" 
                                    data-product-id="<?= $product['id'] ?>"
                                    data-product-name="<?= htmlspecialchars($product['name']) ?>"
                                    data-product-price="<?= htmlspecialchars($product['price']) ?>">
                                Добавить в корзину
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');
                    const productPrice = this.getAttribute('data-product-price');
                    let cart = JSON.parse(localStorage.getItem('cart')) || [];
                    
                    const existingItem = cart.find(item => item.id === productId);
                    
                    if (existingItem) {
                        existingItem.quantity += 1;
                    } else {
                        cart.push({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            quantity: 1
                        });
                    }

                    localStorage.setItem('cart', JSON.stringify(cart));
                    
                    alert('Товар "' + productName + '" добавлен в корзину!');
                });
            });
        });
    </script>
</body>
</html>