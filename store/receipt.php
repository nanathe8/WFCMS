<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Check if order data exists in the session
if (!isset($_SESSION['order_data'])) {
    echo "Order data not found.";
    exit;
}

$orderData = $_SESSION['order_data'];

// Insert order into database
$conn = new mysqli('host', 'user', 'password', 'database'); // Update with your DB credentials

$query = "INSERT INTO orders (customer_id, staff_id, total_price, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($query);
$stmt->bind_param("iid", $orderData['customer_id'], $orderData['staff_id'], $orderData['total_price']);
$stmt->execute();

// Get the generated order ID
$orderId = $stmt->insert_id;

// Insert order items into order_items table
foreach ($orderData['cart'] as $item) {
    $query = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiidd", $orderId, $item['ProductID'], $item['Quantity'], $item['Price'], $item['Subtotal']);
    $stmt->execute();
}

// Clear session data for cart and order
unset($_SESSION['order_data']); //based on the payment module need to change it
unset($_SESSION['cart']);

echo "Order finalized successfully!";
?>
