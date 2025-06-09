<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function validateForm() {
            let isValid = true;

            const username = document.getElementById('username').value;
            const usernameError = document.getElementById('username_error');
            if (!/^[A-Za-zА-Яа-я\s]+$/.test(username)) {
                usernameError.textContent = "ФИО должно содержать только буквы и пробелы.";
                usernameError.style.display = "block";
                isValid = false;
            } else {
                usernameError.style.display = "none";
            }

            const login = document.getElementById('login').value;
            const loginError = document.getElementById('login_error');
            if (!/^[A-Za-z0-9]+$/.test(login)) {
                loginError.textContent = "Логин должен содержать только английские буквы и цифры.";
                loginError.style.display = "block";
                isValid = false;
            } else {
                loginError.style.display = "none";
            }

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorDiv = document.getElementById('password_error');

            if (password.length < 8) {
                errorDiv.textContent = "Пароль должен содержать минимум 8 символов.";
                errorDiv.style.display = "block";
                return false;
            }

            if (password !== confirmPassword) {
                errorDiv.textContent = "Пароли не совпадают.";
                errorDiv.style.display = "block";
                return false;
            }
            errorDiv.textContent = "";
            errorDiv.style.display = "none";
            return true;
        }
    </script>
</head>
<body>
        <div class="header">
            <div class="logo-t">Fleys</div>
            <h3>Магазин периферии</h3>
        </div>
<div class="regist">
        <h2>Регистрация</h2>
        <form action="reg.php" method="POST" onsubmit="return validateForm()">     
            <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            <div id="email_error" class="error"><?php echo $emailError ?? ''; ?></div>
            <br><br> 
            <input type="text" id="username" name="username" placeholder="ФИО" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            <div id="username_error" class="error"><?php echo $usernameError ?? ''; ?></div>
            <br><br>
            <input type="text" id="login" name="login" placeholder="Логин" value="<?php echo htmlspecialchars($login ?? ''); ?>" required>
            <div id="login_error" class="error"><?php echo $loginError ?? ''; ?></div>
            <br><br>
            <input type="password" id="password" name="password" placeholder="Пароль" required>
            <div id="password_error" class="error"><?php echo $passwordError ?? ''; ?></div>
            <br><br>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Подтвердите пароль" required>
            <div id="confirm_password_error" class="error"><?php echo $confirmPasswordError ?? ''; ?></div>
            <br><br>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <a href="login.php">Есть аккаунт?</a>
    </div>
</body>
</html>