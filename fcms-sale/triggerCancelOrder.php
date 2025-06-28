<?php
include 'db.php'; // Include your database connection file

// Trigger name
$triggerName = "before_order_cancel";

// SQL to check if the trigger exists
$checkTriggerSQL = "SHOW TRIGGERS LIKE '$triggerName'";

// SQL to create the trigger
$dropTriggerSQL = "DROP TRIGGER IF EXISTS before_order_cancel;";
$createTriggerSQL = "
CREATE TRIGGER before_order_cancel
BEFORE UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.Status = 'Canceled' AND OLD.Status != 'Pending' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot cancel an order that is not in Pending status';
    END IF;
END;
";

try {
    // Check if the trigger already exists
    $result = $conn->query($checkTriggerSQL);

    if ($result->num_rows > 0) {
        echo "Trigger '$triggerName' already exists.";
    } else {
        // If trigger does not exist, drop and then create it
        if ($conn->query($dropTriggerSQL) === TRUE && $conn->query($createTriggerSQL) === TRUE) {
            echo "Trigger '$triggerName' created successfully.";
        } else {
            throw new Exception("Error creating trigger: " . $conn->error);
        }
    }
} catch (Exception $e) {
    echo "Failed: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>
