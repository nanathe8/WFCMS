<?php
session_start();

if (!isset($_SESSION['cart'])) {
    header("Location: menu.php"); // Redirect if the cart is empty
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_index = $_POST['product_index'];
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$product_index])) {
        switch ($action) {
            case 'increase':
                // Increase the quantity
                $_SESSION['cart'][$product_index]['Quantity']++;
                $_SESSION['cart'][$product_index]['Subtotal'] = 
                    $_SESSION['cart'][$product_index]['Price'] * $_SESSION['cart'][$product_index]['Quantity'];
                break;

            case 'decrease':
                // Decrease the quantity (but not less than 1)
                if ($_SESSION['cart'][$product_index]['Quantity'] > 1) {
                    $_SESSION['cart'][$product_index]['Quantity']--;
                    $_SESSION['cart'][$product_index]['Subtotal'] = 
                        $_SESSION['cart'][$product_index]['Price'] * $_SESSION['cart'][$product_index]['Quantity'];
                } else {
                    // Optional: Remove the item if quantity reaches 0
                    unset($_SESSION['cart'][$product_index]);
                }
                break;

            case 'delete':
                // Remove the product from the cart
                unset($_SESSION['cart'][$product_index]);
                $_SESSION['message'] = "Product deleted successfully!";
                break;

            default:
                // Handle unexpected actions
                break;
        }
    }
}

// Redirect back to the cart page after performing the action
header("Location: addtocart.php"); // Replace `cart.php` with your cart page URL
exit;
?>
