<?php
session_start();
if(!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true){
    header('Location: logim.php');
}

$mysqli = new mysqli('localhost', 'root', '', 'avoska');
if($mysqli->connect_error){
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$query = "SELECT orders.order_id, users.full_name, users.email, products.product_name, orders.quantity, orders.status 
FROM orders 
JOIN users ON orders.user_id = users.id 
JOIN products ON orders.product_id = products.product_id";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_panel.css">
    <title>Панель администратора</title>
</head>
<body>
    <div class="container">
        <h2>Панель администратора</h2>
        <table>
            <tr>
                <th>ФИО</th>
                <th>Email</th>
                <th>Товар</th>
                <th>Количество</th>
                <th>Статус</th> 
                <th>Действие</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <?php if ($row['status'] === 'новый'): ?>
                        <a href="update_order_status.php?order_id=<?php echo $row['order_id']; ?>&status=подтвержден">Подтвердить</a>
                        <a href="update_order_status.php?order_id=<?php echo $row['order_id']; ?>&status=отменен">Отменить</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <a href="logout.php" class="logout-link">Выйти</a>
    </div>
</body>
</html>


<?php $mysqli->close(); ?>