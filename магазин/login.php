<?php
session_start();
$messageError = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $mysqli = new mysqli('localhost', 'root', '', 'avoska');
    if($mysqli->connect_error){
        die("Ошибка подключения: " . $mysqli->connect_error);
    }

    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $adminUsername = 'sklad';
    $adminPassword = '123qwe';

    if($adminUsername == $username && $adminPassword == $password){
        $_SESSION['isAdmin'] = true;
        $_SESSION['username'] = $adminUsername;
        header('Location: admin_panel.php');
        exit;
    } else {
        $stmt = $mysqli->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            if(password_verify($password, $row['password'])){
                $_SESSION['username'] = $row['username'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['phone'] = $row['phone'];
                $_SESSION['email'] = $row['email'];
                header('Location: orders.php');
                exit;
            } else {
                $messageError = 'Пароль не соответствует';
            }
        } else {
            $messageError = 'Пользователь не найден';
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
    <link rel="stylesheet" href="login.css">
    <title>Авторизация</title>
</head>
<body>
    <div class="container">
        <h2>Авторизация</h2>
        <?php if (!empty($messageError)) echo "<p class='error-message'>$messageError</p>"; ?>
        <form action="login.php" method="post">

            <label for="username">Логин:</label><br>
            <input type="text" id="username" name="username" placeholder="login" required><br>
            
            <label for="password">Пароль:</label><br>
            <input type="password" id="password" name="password" placeholder="*****" required><br>

            <input type="submit" value="Войти">
            <a href="register.php">У меня еще нет аккаунта</a><br>
        </form>
    </div>
</body>
</html>
