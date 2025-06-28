<?php
include 'db.php'; // Include the database configuration
include 'headercustomer.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Validate cart and calculate total price
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    echo "<p>Your cart is empty. Please add items to your cart before checking out.</p>";
    echo "<br><a href='menu.php'>Go Back to Menu</a>"; // Adjust the URL as needed
    exit();
}

// Fetch CustomerID and StaffID
$customerID = $_SESSION['CustomerID'] ?? null;
$staffID = $_SESSION['StaffID'] ?? 1; // Default StaffID if not set in session


// Calculate the total price
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['Subtotal'];
}

// Generate the next OrderID
$stmt = $conn->prepare("SELECT OrdersID FROM orders ORDER BY OrdersID DESC LIMIT 1");
$stmt->execute();
$stmt->bind_result($lastOrderID);
$stmt->fetch();
$stmt->close();

// Extract numeric part of the last OrderID and increment it
if ($lastOrderID) {
    $number = intval(substr($lastOrderID, 1)) + 1;
    $newOrderID = 'O' . str_pad($number, 3, '0', STR_PAD_LEFT);
} else {
    $newOrderID = 'O001'; // If no previous OrderID exists, start with O001
}

// Process the order if the Payment button is clicked
if (isset($_POST['Payment'])) {
    $conn->begin_transaction();

    try {
        // Insert order into Orders table
        $stmt = $conn->prepare("INSERT INTO orders (OrdersID, CustomerID, StaffID, TotalAmount, Status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("siid", $newOrderID, $customerID, $staffID, $total);
        $stmt->execute();

        // Insert order items into order_product table
        $stmt = $conn->prepare("INSERT INTO order_product (OrdersID, ProductID, Quantity, PricePerUnit, SubTotal) VALUES (?, ?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt->bind_param("siidd", $newOrderID, $item['ProductID'], $item['Quantity'], $item['Price'], $item['Subtotal']);
            $stmt->execute();
        }

        $conn->commit();
        unset($_SESSION['cart']); // Clear the cart after successful order
        header("Location: payment.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p>Error placing order: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        header { background: #6d5b9e; color: #fff; padding: 20px 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .total { font-size: 1.2em; font-weight: bold; margin-top: 20px; text-align: right; }
        .buttons { display: flex; justify-content: space-between; margin-top: 20px; }
        .btn { padding: 10px; background: #6d5b9e; color: #fff; border: none; cursor: pointer; }
        .btn:hover { background: #555; }
    </style>
</head>
<body>
    <header>
        <h1></h1>
    </header>

    <div class="container">
        <h3>Order Summary</h3>
        <?php
        echo "<p>Order ID: " . htmlspecialchars($newOrderID) . "</p>";
        ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($_SESSION['cart'] as $item) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['ProductName']) . "</td>";
                    echo "<td>RM" . number_format($item['Price'], 2) . "</td>";
                    echo "<td>" . $item['Quantity'] . "</td>";
                    echo "<td>RM" . number_format($item['Subtotal'], 2) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="total">
            <strong>Total: RM<?php echo number_format($total, 2); ?></strong>
        </div>

        <p>Proceed To Payment?</p>

        <div class="buttons">
            <form method="post">
                <button type="submit" name="Payment" class="btn">Yes</button>
            </form>
            <form method="post" action="menu.php">
                <button type="submit" class="btn">No</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
