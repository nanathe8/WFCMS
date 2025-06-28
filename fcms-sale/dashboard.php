<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in user's username
$userID = $_SESSION['userid'];
$query = $conn->prepare("SELECT Username FROM CUSTOMER WHERE CustomerID = ?");
$query->bind_param("i", $userID);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['Username'];
} else {
    $username = "Guest";
}
$query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering System</title>
    <!-- Add Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        nav a { 
            color: #fff; 
            text-decoration: none; 
            padding: 10px; 
            display: inline-block; 
            margin: 0 5px;
        }
        /* Header Navigation */
        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color:rgb(0, 0, 0);
        }
        .nav-link {
            color: #333;
        }
        .nav-link:hover {
            color:rgb(207, 104, 0);
        }

        /* Hero Section */
        .hero {
            background: url('../image/friedchicken3.jpg') no-repeat center center/cover;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
            position: relative;
            padding-top: 250px;
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .btn-custom {
            background-color: #6d5b9e;
            color: #fff;
            padding: 20px 30px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 10px;
        }
        .btn-custom:hover {
            background-color:rgb(0, 0, 0);
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-drumstick-bite"></i> Fried Chicken</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="customerorders.php">Orders</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addtocart.php"><i class="bi bi-cart"></i> 🛒 View Cart</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>HUNGRY?!</h1>
        <p>Good, we are here to serve you</p>
        <div>
            <a href="menu.php" class="btn-custom">Order Now</a>
            <!-- <a href="#" class="btn-custom">View Menu</a> -->
        </div>
    </section>

    <!-- Add Bootstrap JS for dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
