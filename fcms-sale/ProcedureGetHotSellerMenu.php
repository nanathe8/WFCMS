<?php
include 'db.php'; // Include your database connection file

// Check if the procedure already exists
$procedure_check = "SHOW PROCEDURE STATUS WHERE Db = DATABASE() AND Name = 'GetHotSellingProducts'";
$result = $conn->query($procedure_check);

if ($result->num_rows == 0) {
    // Procedure does not exist; create it
    $procedure = "
        CREATE PROCEDURE GetHotSellingProducts(IN start_date DATE, IN end_date DATE)
        BEGIN
            SELECT P.ProductID, P.ProductName, P.ProductImage, SUM(OP.Quantity) AS TotalQuantity, P.ProductPrice
            FROM PRODUCT P
            JOIN ORDER_PRODUCT OP ON P.ProductID = OP.ProductID
            JOIN ORDERS O ON OP.OrdersID = O.OrdersID
            WHERE O.OrderDate BETWEEN start_date AND end_date
            GROUP BY P.ProductID, P.ProductName, P.ProductImage, P.ProductPrice
            ORDER BY TotalQuantity DESC
            LIMIT 10;
        END;
    ";

    if ($conn->query($procedure) === TRUE) {
        echo "Stored procedure created successfully!";
    } else {
        echo "Error creating procedure: " . $conn->error;
    }
} else {
    echo "Stored procedure 'GetHotSellingProducts' already exists.";
}
?>
