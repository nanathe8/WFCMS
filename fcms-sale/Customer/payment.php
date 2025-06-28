<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['CustomerID'])) {
    header("Location: login.php");
    exit();
}

$staffID = 1; // Replace with a valid StaffID from your database

// Ensure that the session variables 'CustomerID' and 'staffID' are set
if (!isset($_SESSION['CustomerID']) || !isset($_SESSION['staffID'])) {
    $_SESSION['message'] = "Customer or Staff information is missing. Please log in again.";
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['CustomerID'];
$staffID = $_SESSION['staffID'];

$paymentSuccessful = true; // Assume payment is successful (this should be set by actual payment logic)

if ($paymentSuccessful && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $totalAmount = 0;

    // Calculate total amount and order date
    foreach ($_SESSION['cart'] as $item) {
        $totalAmount += $item['Subtotal'];
    }
    $orderDate = date('Y-m-d'); // Get current date

    // Insert order into ORDERS table
    $stmt = $conn->prepare("INSERT INTO ORDERS (OrdersID, CustomerID, StaffID, OrderDate, TotalAmount) VALUES (?, ?, ?, ?, ?)");
    $ordersID = uniqid('O'); // Generate a unique order ID
    $stmt->bind_param("siisi", $ordersID, $customerID, $staffID, $orderDate, $totalAmount);
    $stmt->execute();
    $stmt->close();

    // Insert order items into ORDER_PRODUCT table
    $stmt = $conn->prepare("INSERT INTO ORDER_PRODUCT (ProductID, OrdersID, Quantity, PricePerUnit, SubTotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $stmt->bind_param("ssidd", $item['ProductID'], $ordersID, $item['Quantity'], $item['Price'], $item['Subtotal']);
        $stmt->execute();
    }
    $stmt->close();

    // Clear the cart
    unset($_SESSION['cart']);

    // Set success message and redirect
    $_SESSION['message'] = "Payment successful! Your order has been placed.";
    header("Location: order_summary.php"); // Redirect to order summary page
    exit();
} else {
    // Set error message and redirect
    $_SESSION['message'] = "Payment failed. Please try again.";
    header("Location: addtocart.php"); // Redirect to cart page
    exit();
}

$conn->close();
?>
