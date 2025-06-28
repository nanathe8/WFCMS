<?php
include 'db.php'; // Include your database connection file
include 'ProcedureGetHotSellerMenu.php';

// Define the date range
$start_date = date('Y-m-d', strtotime('-30 days'));
$end_date = date('Y-m-d');

try {
    // Prepare and call the stored procedure
    $stmt = $conn->prepare("CALL GetHotSellingProducts(?, ?)");
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    
    // Fetch results
    $result = $stmt->get_result();
    $hot_selling_products = [];
    while ($row = $result->fetch_assoc()) {
        $hot_selling_products[] = $row;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Best Selling Menu!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            position: relative;
        }
        .best-seller-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2>Best Seller Menu!</h2>
        
        <?php if (count($hot_selling_products) > 0): ?>
            <div class="row">
                <?php foreach ($hot_selling_products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                        <img src="/Workshop2/image/<?php echo htmlspecialchars($product['ProductImage']); ?>" 
                            alt="Product Image" 
                            class="card-img-top img-fluid"
                            onerror="this.src='/Workshop2/image/default-placeholder.png';">

                            <div class="best-seller-badge">Best Seller</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['ProductName']); ?></h5>
                                <p class="card-text">Price: RM<?php echo number_format($product['ProductPrice'], 2); ?></p>
                                <p class="card-text">Total Sold: <?php echo htmlspecialchars($product['TotalQuantity']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                No hot selling products found for the selected period.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
