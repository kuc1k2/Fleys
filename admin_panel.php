<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = str_replace([' ', '.'], '', trim($_POST['price'] ?? ''));
    $category = trim($_POST['category'] ?? '');
    $image = $_FILES['image'] ?? null;

    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Название товара обязательно";
    } elseif (strlen($name) > 100) {
        $errors[] = "Название слишком длинное (макс. 100 символов)";
    }
    
    if (empty($description)) {
        $errors[] = "Описание обязательно";
    }
    
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Укажите корректную цену";
    } else {
        $price = floatval($price);
    }
    
    if (!in_array($category, ['keyboard', 'mouse', 'headphones'])) {
        $errors[] = "Выберите корректную категорию";
    }
    
    if (!$image || $image['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Ошибка загрузки изображения";
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024;
        
        if (!in_array($image['type'], $allowed_types)) {
            $errors[] = "Допустимы только JPG, PNG и WebP изображения";
        }
        
        if ($image['size'] > $max_size) {
            $errors[] = "Размер изображения не должен превышать 5MB";
        }
    }
    
    if (empty($errors)) {
        try {
            $upload_dir = 'uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('product_') . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($image['tmp_name'], $file_path)) {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image, created_at) 
                                      VALUES (:name, :description, :price, :category, :image, NOW())");
                
                $stmt->execute([
                    'name' => htmlspecialchars($name),
                    'description' => htmlspecialchars($description),
                    'price' => $price,
                    'category' => htmlspecialchars($category),
                    'image' => $file_path
                ]);
                
                $success_message = "Товар успешно добавлен!";
            } else {
                $errors[] = "Ошибка при сохранении изображения";
            }
        } catch (PDOException $e) {
            if (isset($file_path) && file_exists($file_path)) {
                unlink($file_path);
            }
            $errors[] = "Ошибка базы данных: " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $error_message = implode("<br>", $errors);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];
    
    if (in_array($status, ['accepted', 'rejected'])) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $orderId]);
            
            $success_message = "Статус заказа #$orderId успешно обновлен";
        } catch (PDOException $e) {
            $error_message = "Ошибка при обновлении статуса заказа: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | Fleys</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: var(--dark);
        }
        
        .header {
            background-color: var(--primary);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--secondary);
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            margin-top: 1rem;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--secondary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .product-form, .orders-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .price-input {
            position: relative;
        }
        
        .price-input::after {
            content: ' руб.';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        .order-item {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .order-actions .btn {
            margin-top: 0;
        }
        
        .btn-accept {
            background-color: #28a745;
            color: white;
        }
        
        .btn-reject {
            background-color: #dc3545;
            color: white;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            
            .nav-links {
                margin-top: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .order-header {
                flex-direction: column;
            }
            
            .order-status {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.html" class="logo">Fleys</a>
            <nav class="nav-links">
                <a href="categories.php" class="nav-link">Категории</a>
                <a href="profile.php" class="nav-link">Профиль</a>
                <a href="logout.php" class="nav-link">Выйти</a>
            </nav>
        </div>
    </header>

    <main class="admin-container">
        <h1>Административная панель</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php endif; ?>

        <section class="product-form">
            <h2>Добавить новый товар</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Название товара</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                </div>
                
                <div class="form-group price-input">
                    <label for="price">Цена</label>
                    <input type="text" id="price" name="price" class="form-control" 
                           pattern="\d{1,3}(?:\.\d{3})*(?:,\d{2})?" 
                           title="Формат: 15.000 или 15.000,00" required>
                    <small>Формат: 15.000 или 15.000,00</small>
                </div>
                
                <div class="form-group">
                    <label for="category">Категория</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="keyboard">Клавиатуры</option>
                        <option value="mouse">Мыши</option>
                        <option value="headphones">Наушники</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Изображение</label>
                    <input type="file" id="image" name="image" accept="image/*" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Добавить товар</button>
            </form>
        </section>

                <section class="orders-section">
            <h2>Управление заказами</h2>
            
            <?php
            $orders = $pdo->query("
                SELECT o.*, u.email as user_email 
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($orders)): ?>
                <p>Нет заказов</p>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): 
                        $items = json_decode($order['order_data'], true);
                        $statusClass = 'status-' . $order['status'];
                    ?>
                        <div class="order-item">
                            <div class="order-header">
                                <h3>Заказ #<?= $order['id'] ?></h3>
                                <span class="order-status <?= $statusClass ?>">
                                    <?= $order['status'] == 'accepted' ? 'Принят' : ($order['status'] == 'rejected' ? 'Отклонен' : 'Ожидает') ?>
                                </span>
                            </div>
                            
                            <p><strong>Клиент:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                            <p><strong>Адрес:</strong> <?= htmlspecialchars($order['address']) ?></p>
                            <?php if (!empty($order['comments'])): ?>
                                <p><strong>Комментарий:</strong> <?= htmlspecialchars($order['comments']) ?></p>
                            <?php endif; ?>
                            <p><strong>Пользователь:</strong> <?= $order['user_email'] ? htmlspecialchars($order['user_email']) : 'Гость' ?></p>
                            <p><strong>Дата:</strong> <?= $order['created_at'] ?></p>
                            <p><strong>Сумма:</strong> <?= number_format($order['total'], 2, '.', ' ') ?> руб.</p>
                            
                            <div class="order-items">
                                <h4>Товары:</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Товар</th>
                                            <th>Цена</th>
                                            <th>Кол-во</th>
                                            <th>Сумма</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $orderTotal = 0;
                                        foreach ($items as $item): 
                                            $price = (float)str_replace([',', ' '], '.', $item['price']);
                                            $sum = $price * $item['quantity'];
                                            $orderTotal += $sum;
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['name']) ?></td>
                                                <td><?= number_format($price, 2, '.', ' ') ?> руб.</td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td><?= number_format($sum, 2, '.', ' ') ?> руб.</td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="3" style="text-align: right;"><strong>Итого:</strong></td>
                                            <td><strong><?= number_format($orderTotal, 2, '.', ' ') ?> руб.</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if ($order['status'] == 'pending'): ?>
                                <div class="order-actions">
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-accept">Принять заказ</button>
                                    </form>
                                    
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-reject">Отклонить заказ</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        document.getElementById('price').addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            if (value === '') {
                this.value = '';
                return;
            }
            let formattedValue = '';
            for (let i = value.length - 1, j = 0; i >= 0; i--, j++) {
                if (j > 0 && j % 3 === 0) {
                    formattedValue = '.' + formattedValue;
                }
                formattedValue = value[i] + formattedValue;
            }
            
            this.value = formattedValue;
        });
    </script>
</body>
</html>