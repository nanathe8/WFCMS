<?php
session_start();
include 'db.php'; // Include the database configuration

// Retrieve filter inputs
$month = isset($_POST['month']) ? $_POST['month'] : '';
$year = isset($_POST['year']) ? $_POST['year'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

// Query to fetch orders along with details
$orderQuery = "
    SELECT O.OrdersID, O.OrderDate, C.Name AS CustomerName, S.Staff_FNAME AS StaffName, 
           P.ProductName, OP.Quantity, OP.PricePerUnit, OP.SubTotal, O.Status 
    FROM `ORDERS` O
    JOIN CUSTOMER C ON O.CustomerID = C.CustomerID
    JOIN Staff S ON O.StaffID = S.staffID
    JOIN ORDER_PRODUCT OP ON O.OrdersID = OP.OrdersID
    JOIN PRODUCT P ON OP.ProductID = P.ProductID
    WHERE (? = '' OR MONTH(O.OrderDate) = ?)
    AND (? = '' OR YEAR(O.OrderDate) = ?)
    AND (? = '' OR O.Status = ?)
    ORDER BY O.OrderDate DESC";

$stmt = $conn->prepare($orderQuery);

// Bind the parameters
$stmt->bind_param('ssssss', $month, $month, $year, $year, $status, $status);
$stmt->execute();
$orderResult = $stmt->get_result();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $orderID = $_POST['order_id'];

        // Delete related entries in ORDER_PRODUCT
        $stmt = $conn->prepare("DELETE FROM ORDER_PRODUCT WHERE OrdersID = ?");
        $stmt->bind_param("s", $orderID);
        $stmt->execute();
        $stmt->close();

        // Delete the order from ORDERS
        $stmt = $conn->prepare("DELETE FROM ORDERS WHERE OrdersID = ?");
        $stmt->bind_param("s", $orderID);
        $stmt->execute();
        $stmt->close();

        // Redirect with success message
        echo "<script>alert('Order successfully deleted!'); window.location='orderreport.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { 
            width: 80%; 
            margin: 20px auto; 
        }
        h1 { text-align: center; 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th, td { padding: 10px; 
            text-align: left; 
            border: 1px solid #ddd; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .btn {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn i {
            margin-right: 5px; /* Space between icon and text */
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
        }
        .form-group input, .form-group select { 
            width: 100%; 
            padding: 8px; 
            box-sizing: border-box;
        }
        .btn-edit {
            background-color: #4CAF50; /* Green for Edit */
            color: white;
        }
        .btn-edit:hover {
            background-color: #45a049;
        }
        .btn-container {
            display: flex;
            gap: 10px; /* Space between buttons */
            justify-content: flex-start; /* Align buttons to the left */
            align-items: center; /* Vertically align buttons */
        }
        .btn-container form { 
            margin-bottom: 5px; 
        }
        .btn-delete {
            background-color: red; /* Red for Delete */
            color: white;
        }
        .delete-btn { color: black; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Order Details</h1>
    </div>
</header>

<div class="container">
    <h3>Filter Orders</h3>
    <form method="POST" action="orderreport.php">
        <div class="form-group">
            <label for="month">Month:</label>
            <select id="month" name="month">
                <option value="">All</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($month == $i) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="text" id="year" name="year" placeholder="e.g. 2023" value="<?php echo htmlspecialchars($year); ?>">
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="">All</option>
                <option value="In Process" <?php echo ($status == 'In Process') ? 'selected' : ''; ?>>In Process</option>
                <option value="Completed" <?php echo ($status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="Canceled" <?php echo ($status == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
            </select>
        </div>
        <button type="submit" class="btn">Filter</button>
    </form>

    <h3>Customer Orders List</h3>
    <table>
        <thead>
            <tr>
                <th>OrderID</th>
                <th>Username</th>
                <!-- <th>Staff Name</th> -->
                <th>Product Name</th>
                <th>Quantity</th>
                <!-- <th>Price Per Unit</th> -->
                <th>Total Amount</th>
                <th>Status</th>
                <th>Order Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($orderResult->num_rows > 0): ?>
                <?php while ($order = $orderResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['OrdersID']); ?></td>
                        <td><?php echo htmlspecialchars($order['CustomerName']); ?></td>
                        <!-- <td><?php echo htmlspecialchars($order['StaffName']); ?></td> -->
                        <td><?php echo htmlspecialchars($order['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($order['Quantity']); ?></td>
                        <!-- <td>RM<?php echo number_format($order['PricePerUnit'], 2); ?></td> -->
                        <td>RM<?php echo number_format($order['SubTotal'], 2); ?></td>
                        <td><?php echo htmlspecialchars($order['Status']); ?></td>
                        <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                        <td>
                            <div class="btn-container">
                                <form method="post" action="editorder.php" style="display: inline-block; margin-right: 5px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['OrdersID']; ?>">
                                    <button type="submit" name="edit_product" class="btn btn-edit">
                                        <i class="fas fa-pencil-alt"></i> Edit Status
                                    </button>
                                </form>
                                <form method="post" action="orderreport.php" style="display: inline-block;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['OrdersID']; ?>">
                                    <button type="submit" name="delete_product" class="btn btn-delete">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10" style="text-align: center;">No orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
