<?php
session_start();
if(!isset($_SESSION['id'])){
    header('Location: login.php');
    exit;
}

$mysqli = new mysqli('localhost', 'root', '', 'avoska');
if($mysqli->connect_error){
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$product_id = $_POST['product'];
$quantity = $_POST['quantity'];
$delivery_address = $_POST['delivery_address'];
$user_id = $_SESSION['id'];
$status = 'новый';

if(is_numeric($quantity) && $quantity > 0){
    $stmt = $mysqli->prepare("INSERT INTO orders (user_id, product_id, quantity, delivery_address, status) VALUES (?, ?, ?, ?, ?)");
    if($stmt === false){
        die("Ошибка подготовки запроса " . $stmt->error);
    }

    $stmt->bind_param('iiiss', $user_id, $product_id, $quantity, $delivery_address, $status);
    if($stmt->execute()){
        header("Location: orders.php");
        exit();
    } else {
        echo "Ошибка при создании заказа " . $stmt->error;
    }

    
    $stmt->close();
} else {
    echo "Количество должно быть положительным числом";
}

$mysqli->close();
?>