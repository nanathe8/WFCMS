<?php
include 'db.php'; 
include 'headercustomer.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch the customer's orders
$stmt = $conn->prepare("
    SELECT O.OrdersID, O.OrderDate, O.Status, 
        C.Name AS CustomerName, OP.ProductID, P.ProductName, OP.Quantity, OP.PricePerUnit, OP.SubTotal
    FROM ORDERS O
    JOIN CUSTOMER C ON O.CustomerID = C.CustomerID
    JOIN ORDER_PRODUCT OP ON O.OrdersID = OP.OrdersID
    JOIN PRODUCT P ON OP.ProductID = P.ProductID
    WHERE O.CustomerID = ? AND O.Status IN ('Pending', 'In Process', 'Completed', 'Canceled')
    ORDER BY O.OrderDate DESC");
$stmt->bind_param("s", $customerID);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Separate orders into processing and history categories 
$processingOrders = array_filter($orders, function($item) { 
    return in_array($item['Status'], ['Pending', 'In Process']); 
}); 

$historyOrders = array_filter($orders, function($item) { 
    return in_array($item['Status'], ['Completed', 'Canceled']);
});

// Cancel order (only if the status is 'Pending')
if (isset($_POST['cancel_order_id'])) {
    $orderIDToCancel = $_POST['cancel_order_id'];

    // Validate input (no longer check for numeric)
    if (empty($orderIDToCancel)) {
        echo "<script>alert('Invalid order ID. Received: $orderIDToCancel');</script>";
    } else {

        // Check if the order exists and belongs to the customer
        $stmt = $conn->prepare("
            SELECT Status FROM ORDERS 
            WHERE OrdersID = ? AND CustomerID = ?
        ");
        $stmt->bind_param("ss", $orderIDToCancel, $customerID); // Bind as string
        $stmt->execute();
        $stmt->bind_result($status);
        $orderExists = $stmt->fetch();
        $stmt->close();

        if (!$orderExists) {
            echo "<script>alert('Order not found or does not belong to you.');</script>";
        } else {
            if ($status === 'Pending') {
                $updateStmt = $conn->prepare("UPDATE ORDERS SET Status = 'Canceled' WHERE OrdersID = ?");
                $updateStmt->bind_param("s", $orderIDToCancel); // Bind as string
                try {
                    if ($updateStmt->execute()) {
                        echo "<script>alert('Your order has been successfully canceled.');</script>";
                    } else {
                        throw new Exception('Failed to cancel the order.');
                    }
                } catch (Exception $e) {
                    echo "<script>alert('" . $e->getMessage() . "');</script>";
                }
                $updateStmt->close();
            } else {
                echo "<script>alert('Only orders with status \"Pending\" can be canceled.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        header { 
            background: #6d5b9e; 
            color: #fff; 
            padding: 20px 0; 
            text-align: center; 
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color:rgb(0, 0, 0);
        }
        nav a { 
            color: #fff; 
            text-decoration: none; 
            padding: 10px; 
            display: inline-block; 
            margin: 0 5px;
        }
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f8f9fa; 
            margin: 0; padding: 0; 
        }
        h1, h2 { 
            text-align: center; 
            margin-bottom: 20px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
        }
        th, td { 
            padding: 10px; 
            text-align: left; 
            border: 1px solid #ddd; 
        }
        th { 
            background-color: #f2f2f2;
        }
        .order-details p {
            text-align: center;
            color: #777;
        }
        .order-details {
            margin-bottom: 20px;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
            th, td {
                padding: 6px;
            }
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .no-orders {
            text-align: center;
            padding: 20px;
            color: #888;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons button {
            font-size: 0.9rem;
            padding: 5px 10px;
        }        
    </style>
</head>
<body>
    <header>
        <div class="container">
        </div>
    </header>

    <div class="container">
        <h2>Your Orders</h2>

        <div class="order-details">
            <h4>Processing Orders</h4>
            <?php if (empty($processingOrders)): ?>
                <p>No orders are currently being processed.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Food Item</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 15%;">Price</th>
                            <th style="width: 20%;">Order Date</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($processingOrders as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                                <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                                <td>RM<?php echo number_format($item['SubTotal'], 2); ?></td>
                                <td><?php echo htmlspecialchars($item['OrderDate']); ?></td>
                                <td><?php echo htmlspecialchars($item['Status']); ?></td>
                                <td>
                                    <?php if ($item['Status'] === 'Pending'): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="cancel_order_id" value="<?php echo htmlspecialchars($item['OrdersID']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Cancel</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="order-details">
            <h4>Order History</h4>
            <?php if (empty($historyOrders)): ?>
                <p>No order history available.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Food Item</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 15%;">Price</th>
                            <th style="width: 20%;">Order Date</th>
                            <th style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historyOrders as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                                <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                                <td>RM<?php echo number_format($item['SubTotal'], 2); ?></td>
                                <td><?php echo htmlspecialchars($item['OrderDate']); ?></td>
                                <td><?php echo htmlspecialchars($item['Status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS for dropdown -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
