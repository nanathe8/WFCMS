<?php
session_start();
include 'db.php'; // Include the database configuration

// Check if the order_id is provided
if (!isset($_POST['order_id'])) {
    echo "<script>alert('No order selected.'); window.location='orderreport.php';</script>";
    exit();
}

$orderID = $_POST['order_id'];
$errorMessage = "";

// Fetch the current order details along with the products
$stmt = $conn->prepare("
    SELECT O.OrdersID, O.OrderDate, O.Status, 
           C.Name AS CustomerName, C.Email AS CustomerEmail, C.Phone AS CustomerPhone,
           OP.ProductID, P.ProductName, OP.Quantity, OP.PricePerUnit, OP.SubTotal
    FROM ORDERS O
    JOIN CUSTOMER C ON O.CustomerID = C.CustomerID
    JOIN ORDER_PRODUCT OP ON O.OrdersID = OP.OrdersID
    JOIN PRODUCT P ON OP.ProductID = P.ProductID
    WHERE O.OrdersID = ?");
$stmt->bind_param("s", $orderID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Order not found.'); window.location='orderreport.php';</script>";
    exit();
}

$orderDetails = [];
while ($row = $result->fetch_assoc()) {
    $orderDetails[] = $row;
}

$order = $orderDetails[0]; // Assign the first row to the $order variable for status selection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $newStatus = $_POST['status'];

    // Update the order status
    $updateStmt = $conn->prepare("UPDATE ORDERS SET Status = ? WHERE OrdersID = ?");
    $updateStmt->bind_param("ss", $newStatus, $orderID);

    if ($updateStmt->execute()) {
        echo "<script>alert('Order status updated successfully!'); window.location='orderreport.php';</script>";
    } else {
        $errorMessage = "Failed to update order status. Please try again.";
    }

    $updateStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order Status</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0; padding: 0; 
        }
        .container { 
            width: 50%; 
            margin: 50px auto; 
            background: #fff; 
            padding: 20px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            border-radius: 8px; 
        }
        h1 { 
            text-align: center; 
        }
        form { 
            margin-top: 20px; 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
        }
        .form-group select, .form-group input { 
            width: 100%; 
            padding: 10px; 
            box-sizing: border-box; 
        }
        .btn { 
            display: block; 
            width: 100%; 
            padding: 10px; 
            background: #6d5b9e; 
            color: #fff; 
            text-align: center; 
            border: none; 
            cursor: pointer; 
            font-size: 16px; 
            margin-top: 10px; 
        }
        .btn:hover { 
            background: #3a2a77; 
        }
        .error { 
            color: red; 
            -bottom: 10px; 
        }
        .order-details { 
            margin-bottom: 20px;
        }
        .order-details p { 
            margin: 5px 0; 
        }
        .order-details span { 
            display: inline-block; 
            min-width: 150px; 
            font-weight: bold; 
        }
        .product-details { 
            margin-bottom: 20px; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            background-color: #f9f9f9; 
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Order Status</h1>

    <?php if ($errorMessage): ?>
        <p class="error"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <div class="order-details">
        <?php foreach ($orderDetails as $item): ?>
            <div class="product-details">
                <p><span>Customer Name:</span> <?php echo htmlspecialchars($item['CustomerName']); ?></p>
                <p><span>Food Item:</span> <?php echo htmlspecialchars($item['ProductName']); ?></p>
                <p><span>Quantity:</span> <?php echo htmlspecialchars($item['Quantity']); ?></p>
                <p><span>Price:</span> RM<?php echo number_format($item['SubTotal'], 2); ?></p>
                <p><span>Order Date:</span> <?php echo htmlspecialchars($item['OrderDate']); ?></p>
                <p><span>Order Status:</span> <?php echo htmlspecialchars($item['Status']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="editorder.php">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderID); ?>">
        
        <div class="form-group">
            <label for="status">Select Order Status:</label>
            <select id="status" name="status">
                <option value="Pending" <?php echo ($order['Status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="In Process" <?php echo ($order['Status'] === 'In Process') ? 'selected' : ''; ?>>In Process</option>
                <option value="Completed" <?php echo ($order['Status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="Canceled" <?php echo ($order['Status'] === 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
            </select>
        </div>

        <button type="submit" name="update_status" class="btn">Update Status</button>
    </form>
</div>

</body>
</html>
