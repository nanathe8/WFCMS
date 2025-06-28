<?php
session_start();
include 'db.php'; // Ensure this includes your database connection setup

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to update order status
function updateOrderStatus($orderId, $newStatus) {
    updateStatusInDatabase($orderId, $newStatus);

    if ($newStatus === 'Ready') {
        sendCustomerNotification($orderId);
        sendInternalAlert($orderId);
    }
}

// Function to update status in the database
function updateStatusInDatabase($orderId, $newStatus) {
    global $db; // Ensure the $db variable is accessible

    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $db->error);
    }
    $stmt->bind_param('si', $newStatus, $orderId);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}

// Function to fetch order details from the database
function fetchOrderDetails($orderId) {
    global $db; // Ensure the $db variable is accessible

    $stmt = $db->prepare("SELECT customer_email, customer_name FROM orders WHERE id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $db->error);
    }
    $stmt->bind_param('i', $orderId);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $orderDetails = $result->fetch_assoc();
    $stmt->close();

    return $orderDetails;
}

// Function to send notification to the customer
function sendCustomerNotification($orderId) {
    $orderDetails = fetchOrderDetails($orderId);
    $customerEmail = $orderDetails['customer_email'];
    $customerName = $orderDetails['customer_name'];

    $subject = "Your Order is Ready!";
    $message = "Hello $customerName,\n\nYour order #$orderId is now ready for pickup.\n\nThank you for choosing us!";
    $headers = "From: no-reply@yourrestaurant.com";

    mail($customerEmail, $subject, $message, $headers);

    logTrigger("Customer notification sent for order #$orderId");
}

// Function to send internal alert
function sendInternalAlert($orderId) {
    $staffEmail = 'staff@yourrestaurant.com';

    $subject = "Attention Required: Order #$orderId";
    $message = "Order #$orderId is now ready and requires your attention.\n\nPlease proceed with the next steps.";
    $headers = "From: no-reply@yourrestaurant.com";

    mail($staffEmail, $subject, $message, $headers);

    logTrigger("Internal alert sent for order #$orderId");
}

// Function to log triggers (for debugging and record-keeping)
function logTrigger($message) {
    file_put_contents('trigger_log.txt', $message . "\n", FILE_APPEND);
}

// Example usage (this should be replaced with actual status change logic)
if (isset($_POST['order_id']) && isset($_POST['new_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];
    updateOrderStatus($orderId, $newStatus);
}

// Close the database connection if it exists
if (isset($db)) {
    $db->close();
}
?>
