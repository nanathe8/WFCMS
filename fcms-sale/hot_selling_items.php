<?php
include 'db.php'; // Include your database connection file

try {
    // Drop the procedure if it exists
    $conn->query("DROP PROCEDURE IF EXISTS get_hot_selling_items");

    // SQL to create the stored procedure
    $sql = "
    CREATE PROCEDURE get_hot_selling_items(
        IN start_date DATE,
        IN end_date DATE,
        OUT hot_selling_product VARCHAR(255)
    )
    BEGIN
        SELECT p.ProductName
        INTO hot_selling_product
        FROM ORDER_PRODUCT op
        JOIN ORDERS o ON op.OrdersID = o.OrdersID
        JOIN PRODUCT p ON op.ProductID = p.ProductID
        WHERE o.OrderDate BETWEEN start_date AND end_date
        GROUP BY p.ProductID
        ORDER BY SUM(op.Quantity) DESC
        LIMIT 1;
    END";

    // Execute the SQL statement
    if ($conn->query($sql)) {
        echo "Stored procedure 'get_hot_selling_items' created successfully.";
    } else {
        throw new Exception("Error creating stored procedure: " . $conn->error);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db.php'; // Include your database connection file

    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $hot_selling_product = '';

    try {
        // Call the stored procedure
        $stmt = $conn->prepare("CALL get_hot_selling_items(?, ?, @hot_selling_product)");
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();

        // Fetch the output value
        $result = $conn->query("SELECT @hot_selling_product AS hot_selling_product");
        if ($result) {
            $row = $result->fetch_assoc();
            $hot_selling_product = $row['hot_selling_product'];
        } else {
            throw new Exception("Error fetching result: " . $conn->error);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hot Selling Items Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Hot Selling Items Report</h2>
        <form method="POST" action="hot_selling_items.php" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date:</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date:</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </div>
        </form>
        
        <?php if (isset($hot_selling_product) && $hot_selling_product != ''): ?>
        <div class="alert alert-info">
            <strong>Hot Selling Product:</strong> <?php echo htmlspecialchars($hot_selling_product); ?>
        </div>
        <?php elseif (isset($hot_selling_product)): ?>
        <div class="alert alert-warning">
            No hot selling products found for the selected period.
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
