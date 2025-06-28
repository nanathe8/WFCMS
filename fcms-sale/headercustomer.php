<?php
// Start the session
session_start();

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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        /* Navbar Styling */
        .navbar {
            background-color: #fff;
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-size: 2rem;
            font-weight: bold;
            color: #6d5b9e;
        }
        .nav-link { 
            color: #333; 
        } 
        .nav-link:hover { 
            color: #6d5b9e; 
        }
        /* Product Card Styling */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card img {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            height: 180px;
            object-fit: cover;
        }
        .btn-add-cart {
            background-color: #6d5b9e;
            color: #fff;
            border: none;
        }
        .btn-add-cart:hover {
            background-color: #5a4a85;
        }
        .price {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
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
                        <a class="nav-link" href="addtocart.php">🛒 View Cart</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</body>
</html>
