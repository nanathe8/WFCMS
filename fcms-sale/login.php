<?php
session_start();
include 'db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check the credentials and get the CustomerID and staffID
    $stmt = $conn->prepare("SELECT CustomerID, staffID FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Fetch customer and staff IDs
        $stmt->bind_result($customerID, $staffID);
        $stmt->fetch();
        
        // Set session variables
        $_SESSION['userid'] = $username;
        $_SESSION['CustomerID'] = $customerID;
        $_SESSION['staffID'] = $staffID;

        // Redirect to payment page or dashboard
        header("Location: payment.php");
        exit();
    } else {
        // Handle invalid login
        $_SESSION['message'] = "Invalid username or password.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>

<?php
// login.php
/*
$valid_username = "user";
$valid_password = "password";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $custusername = $_POST['username'];
    $custpassword = $_POST['password'];
    
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "fcms"; // Replace with your database name
    
    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $login = "SELECT * FROM customer WHERE Username = '$custusername'";
    $result = $conn->query($login);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($custpassword, $row['Password_hash'])) {
            $_SESSION['Username'] = $custusername;
            echo "Login successful! Welcome, " . htmlspecialchars($custusername) . ".";
            // Redirect to a protected page
            header("Location: menu.php");
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Invalid username.";
    }
}
$conn->close(); */
?> 