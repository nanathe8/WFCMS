<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in user's ID
$userID = $_SESSION['userid'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required fields are set
    if (isset($_POST['paymentType'], $_POST['creditCardNumber'], $_POST['expiryMonth'], 
              $_POST['expiryDate'], $_POST['ccv'], $_POST['nameOnCard'], $_POST['orderID'])) {

        // Get the posted form values
        $paymentType = $_POST['paymentType'];
        $creditCardNumber = $_POST['creditCardNumber'];
        $expiryMonth = $_POST['expiryMonth'];
        $expiryDate = $_POST['expiryDate'];
        $ccv = $_POST['ccv'];
        $nameOnCard = $_POST['nameOnCard'];
        $orderID = $_POST['orderID']; // Get the orderID that the user wants to pay for

        // Fetch the amount associated with the orderID
        $sql = "SELECT Amount FROM orders WHERE OrderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $orderID);
        $stmt->execute();
        $stmt->bind_result($amount);
        $stmt->fetch();
        $stmt->close();

        if (!$amount) {
            echo "<script>alert('Order not found or amount not available.');</script>";
            exit();
        }

        // Insert into PaymentMethod table
        try {
            // Insert into PaymentMethod table
            $stmt = $conn->prepare("INSERT INTO paymentmethod (Type, CreditCardNumber, ExpiryMonth, ExpiryDate, CCV, NameOnCard) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisis", $paymentType, $creditCardNumber, $expiryMonth, $expiryDate, $ccv, $nameOnCard);
            $stmt->execute();
            $paymentMethodID = $stmt->insert_id; // Get the generated PaymentMethodID
            $stmt->close();

            // Insert into Payment table
            $paymentDate = date('Y-m-d');
            $status = 'Completed'; // Set the payment status
            $stmt = $conn->prepare("INSERT INTO payment (PaymentDate, Amount, Status, PaymentMethodID, CustomerID, OrdersID) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsiis", $paymentDate, $amount, $status, $paymentMethodID, $userID, $orderID);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Payment successful!'); window.location.href='dashboard.php';</script>";

        } catch (Exception $e) {
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }

    } else {
        echo "<script>alert('Please fill all the fields.');</script>";
    }
}

// Function to generate the next OrderID (e.g., O001, O002, etc.)
function generateOrderID($conn) {
    $query = "SELECT MAX(OrderID) AS last_order FROM orders WHERE OrderID LIKE 'O%'";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        $lastOrderID = $row['last_order'];

        if ($lastOrderID) {
            // Extract the number part, increment it, and generate the new OrderID
            $number = (int)substr($lastOrderID, 1); // Remove the 'O' and get the number
            $newOrderID = 'O' . str_pad($number + 1, 3, '0', STR_PAD_LEFT); // Increment and pad with zeros
        } else {
            // If no orders exist yet, start with O001
            $newOrderID = 'O001';
        }
    } else {
        // Error fetching last order, start from O001
        $newOrderID = 'O001';
    }

    return $newOrderID;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Payment</h1>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="amount" class="form-label">Amount (RM)</label>
                <input type="number" class="form-control" id="amount" name="amount" value="<?php echo isset($amount) ? $amount : ''; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="paymentType" class="form-label">Payment Type</label>
                <select class="form-select" id="paymentType" name="paymentType" required>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="creditCardNumber" class="form-label">Card Number</label>
                <input type="text" class="form-control" id="creditCardNumber" name="creditCardNumber" required>
            </div>
            <div class="mb-3">
                <label for="expiryMonth" class="form-label">Expiry Month</label>
                <input type="number" class="form-control" id="expiryMonth" name="expiryMonth" required>
            </div>
            <div class="mb-3">
                <label for="expiryDate" class="form-label">Expiry Date</label>
                <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
            </div>
            <div class="mb-3">
                <label for="ccv" class="form-label">CCV</label>
                <input type="number" class="form-control" id="ccv" name="ccv" required>
            </div>
            <div class="mb-3">
                <label for="nameOnCard" class="form-label">Name on Card</label>
                <input type="text" class="form-control" id="nameOnCard" name="nameOnCard" required>
            </div>
            <!-- Hidden field for OrderID -->
            <input type="hidden" name="orderID" value="<?php echo $_GET['orderID']; ?>">
            <button type="submit" class="btn btn-primary">Pay Now</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
