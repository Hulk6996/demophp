<?php
session_start();
if(!isset($_SESSION['id'])){
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', '', 'avoska');
if($mysqli->connect_error){
    die('Ошибка подключения: ' . $mysqli->connect_error);
}

$userId = $_SESSION['id'];
$stmt = $mysqli->prepare('SELECT O.quantity, O.delivery_address, O.status, P.product_name, P.price FROM orders O INNER JOIN products P ON O.product_id = P.product_id WHERE O.user_id = ?');
$stmt->bind_param('s', $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="orders.css">
    <title>Список заказов</title>
</head>
<body>
    <div class="container container-orders">
        <h2>Список заказов</h2>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()):
                    $totalPrice = $row['price'] * $row['quantity'];
                ?>
                <li>
                    <?php echo htmlspecialchars($row['product_name']) . ", количество: " . $row['quantity'] . ", общая стоимость: " . $totalPrice . " руб., адрес доставки: " . htmlspecialchars($row['delivery_address']) . ", статус: " . $row['status']; ?>
                </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Нет заказов</p>
        <?php endif ?>
        <a href="create_order_form.php">Создать новый заказ</a>
        <a href="logout.php">Выйти</a>
    </div>
</body>
</html>


<?php $mysqli->close(); ?>