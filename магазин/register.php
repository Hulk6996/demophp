<?php
session_start();
$messageError = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $mysqli = new mysqli('localhost', 'root', '', 'avoska');
    if($mysqli->connect_error){
        die('Ошибка подключения: ' . $mysqli->connect_error);
    }

    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $full_name = $mysqli->real_escape_string($_POST['full_name']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $email = $mysqli->real_escape_string($_POST['email']);

    $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $messageError = 'Такой логин уже занят';
    } else {
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $messageError = 'Такой email уже занят';
        } elseif(strlen($password) < 4){
            $messageError = 'Пароль должен содержать минимум 4 сивола';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $messageError = 'Введите корректный email';
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $mysqli->prepare('INSERT INTO users(username, password, full_name, phone, email) VALUES (?,?,?,?,?)');
            $stmt->bind_param('sssss', $username, $password_hashed, $full_name, $phone, $email);
            if($stmt->execute()){
                header('Location: login.php');
            } else {
                $messageError = 'Ошибка регистрации ' . $stmt->error;
            }
        }
    }
    $mysqli->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Регистрация</title>
</head>
<body>
    <div class="container">
        <h2>Регистрация</h2>
        <?php if (!empty($errorMessage)) echo "<p class='error-message'>$errorMessage</p>"; ?>
        <form action="register.php" method="post">
            <label for="username">Логин:</label><br>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Пароль:</label><br>
            <input type="password" id="password" name="password" required><br>

            <label for="full_name">ФИО:</label><br>
            <input type="text" id="full_name" name="full_name" required pattern="[а-яА-Я\s]+" title="ФИО должно содержать только символы кириллицы и пробелы"><br>

            <label for="phone">Телефон:</label><br>
            <input type="text" id="phone" name="phone" pattern="\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}" required title="Телефонный номер должен быть в формате +7(XXX)-XXX-XX-XX"><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br>

            <input type="submit" value="Зарегистрироваться">
            <a href='login.php'>Уже есть аккаунт</a><br>
        </form>
    </div>
</body>
</html>