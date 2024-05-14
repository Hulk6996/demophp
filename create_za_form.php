<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Параметры подключения к базе данных
$host = 'localhost';  // Или ваш хост
$dbname = 'your_database_name';  // Имя вашей базы данных
$username = 'your_username';  // Имя пользователя базы данных
$password = 'your_password';  // Пароль базы данных

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Не удалось подключиться к базе данных: " . $mysqli->connect_error);
}

// Получение списка автомобилей
$query = "SELECT car_id, car_name FROM cars";
$result = $mysqli->query($query);

$cars = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

// Обработка отправки формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $car_id = $mysqli->real_escape_string($_POST['car_id']);
    $reservation_date = $mysqli->real_escape_string($_POST['reservation_date']);

    // Проверка наличия заявок на эту дату
    $stmt = $mysqli->prepare("SELECT * FROM reservations WHERE car_id = ? AND reservation_date = ? AND status IN ('новое', 'подтверждено')");
    $stmt->bind_param("is", $car_id, $reservation_date);
    $stmt->execute();
    $existing_reservations = $stmt->get_result();

    if ($existing_reservations->num_rows > 0) {
        $error_message = "На эту дату автомобиль уже забронирован.";
    } else {
        // Вставка новой заявки
        $insert_stmt = $mysqli->prepare("INSERT INTO reservations (user_id, car_id, reservation_date, status) VALUES (?, ?, ?, 'новое')");
        $insert_stmt->bind_param("iis", $user_id, $car_id, $reservation_date);
        if ($insert_stmt->execute()) {
            $success_message = "Заявка успешно создана.";
        } else {
            $error_message = "Ошибка при создании заявки: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }
    $stmt->close();
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Формирование заявки</title>
</head>
<body>
    <h2>Формирование заявки на бронирование автомобиля</h2>
    <?php if (!empty($success_message)) echo "<p style='color: green;'>$success_message</p>"; ?>
    <?php if (!empty($error_message)) echo "<p style='color: red;'>$error_message</p>"; ?>

    <form action="make_reservation.php" method="post">
        <label for="car_id">Выберите автомобиль:</label><br>
        <select id="car_id" name="car_id" required>
            <?php foreach ($cars as $car): ?>
                <option value="<?php echo $car['car_id']; ?>"><?php echo htmlspecialchars($car['car_name']); ?></option>
            <?php endforeach; ?>
        </select><br>
        
        <label for="reservation_date">Дата бронирования:</label><br>
        <input type="date" id="reservation_date" name="reservation_date" required><br>
        
        <input type="submit" value="Сформировать заявку">
    </form>
</body>
</html>
