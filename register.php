<?php
// Установка соединения с базой данных
$host = 'localhost'; // или ваш хост
$dbname = 'your_database_name'; // ваше имя базы данных
$username = 'your_username'; // ваше имя пользователя базы данных
$password = 'your_password'; // ваш пароль базы данных

$mysqli = new mysqli($host, $username, $password, $dbname);

// Проверка соединения
if ($mysqli->connect_error) {
    die("Не удалось подключиться: " . $mysqli->connect_error);
}

// Обработка отправки формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $mysqli->real_escape_string($_POST['full_name']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $raw_password = $_POST['password'];
    $driver_license_number = $mysqli->real_escape_string($_POST['driver_license_number']);

    // Базовая валидация
    if (!empty($full_name) && !empty($phone) && !empty($email) && !empty($raw_password) && strlen($raw_password) >= 3 && !empty($driver_license_number)) {
        // Хеширование пароля
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

        // Подготовка и привязка
        $stmt = $mysqli->prepare("INSERT INTO users (full_name, phone, email, password, driver_license_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $phone, $email, $password, $driver_license_number);

        // Выполнение и проверка на ошибки
        if ($stmt->execute()) {
            echo "Пользователь успешно зарегистрирован.";
        } else {
            echo "Ошибка: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Все поля обязательны к заполнению, и пароль должен быть не менее 3 символов.";
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
</head>
<body>
    <h2>Регистрация</h2>
    <form action="register.php" method="post">
        <label for="full_name">Полное имя:</label><br>
        <input type="text" id="full_name" name="full_name" required><br>
        
        <label for="phone">Телефон (8(XXX)-XXX-XX-XX):</label><br>
        <input type="text" id="phone" name="phone" required pattern="8\(\d{3}\)-\d{3}-\d{2}-\d{2}"><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        
        <label for="password">Пароль (минимум 3 символа):</label><br>
        <input type="password" id="password" name="password" required minlength="3"><br>
        
        <label for="driver_license_number">Номер водительского удостоверения:</label><br>
        <input type="text" id="driver_license_number" name="driver_license_number" required><br>
        
        <input type="submit" value="Зарегистрироваться">
    </form>
</body>
</html>
