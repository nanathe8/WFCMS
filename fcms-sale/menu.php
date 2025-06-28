<?php
include 'db.php';
include 'headercustomer.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch the product list
$productQuery = "SELECT * FROM PRODUCT WHERE StockStatus = 'Available'";
$result = $conn->query($productQuery);
$num_products = $result->num_rows;

// Initialize arrays to hold each category
$biasaProducts = [];
$happyBoxProducts = [];

// Separate products into Biasa and Happy Box based on the product name
while ($row = $result->fetch_assoc()) {
    if (strpos(strtolower($row['ProductName']), 'happy box') !== false) {
        $happyBoxProducts[] = $row;
    } else {
        $biasaProducts[] = $row; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Ordering System</title>
    <!-- Bootstrap JS for icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        header { 
            background: #6d5b9e; 
            color: #fff; 
            padding: 20px 0; 
            text-align: center; 
        }
        nav a { 
            color: #fff; 
            text-decoration: none; 
            padding: 10px; 
            display: inline-block; 
            margin: 0 5px;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color:rgb(0, 0, 0);
        }
        /* Ensuring the images inside cards are properly aligned */
        .card-img-top {
            object-fit: cover;
            height: 200px; /* Set a fixed height */
            width: 100%;
        }
        /* Ensuring consistent card heights for alignment */
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card-body {
            flex-grow: 1;
        }
        .price {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .btn-add-cart {
            background-color: #6d5b9e !important;
            color: white !important;
            border: none!important;
            padding: 10px;
            font-size: 1rem;
        }
        .btn-add-cart:hover {
            background-color: #6d5b9e !important;
        }

        /* Aligning cards in rows with equal width */
        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-4 {
            flex: 1 1 30%; /* Ensure 3 products per row */
            max-width: 30%; /* Adjusting the width of the columns */
            padding: 15px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
        </div>
    </header>

    <!-- Display Success Message -->
    <?php
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . htmlspecialchars($_SESSION['message']) . "');</script>";
            unset($_SESSION['message']); // Clear the message after displaying
        }
    ?>

    <!-- Best Seller In 30 days-->
    <?php 
    include 'best_sellers_customer.php'; ?> 

    <!-- Biasa Products Section -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Combo Menu</h2>
        <h5 class="text-center mb-4">- 2 Pieces -</h5>
        <div class="row">
        <?php foreach ($biasaProducts as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="/Workshop2/image/<?php echo htmlspecialchars($product['ProductImage']); ?>" alt="Product Image" class="card-img-top img-fluid">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['ProductName']); ?></h5>
                        <p class="card-text price">RM<?php echo number_format($product['ProductPrice'], 2); ?></p>
                        <form method="POST" action="addtocart.php">
                            <input type="number" name="quantity[<?php echo $product['ProductID']; ?>]" min="1" max="10" value="1" class="form-control mb-3 text-center" placeholder="Qty">
                            <input type="hidden" name="product_id[]" value="<?php echo $product['ProductID']; ?>">
                            <button type="submit" class="btn btn-add-cart w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>

    <!-- Happy Box Products Section -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Happy Box Menu</h2>
        <h5 class="text-center mb-4">- 5 Pieces -</h5>
        <div class="row">
        <?php foreach ($happyBoxProducts as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="/Workshop2/image/<?php echo htmlspecialchars($product['ProductImage']); ?>" alt="Product Image" class="card-img-top img-fluid">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['ProductName']); ?></h5>
                        <p class="card-text price">RM<?php echo number_format($product['ProductPrice'], 2); ?></p>
                        <form method="POST" action="addtocart.php">
                            <input type="number" name="quantity[<?php echo $product['ProductID']; ?>]" min="1" max="10" value="1" class="form-control mb-3 text-center" placeholder="Qty">
                            <input type="hidden" name="product_id[]" value="<?php echo $product['ProductID']; ?>">
                            <button type="submit" class="btn btn-add-cart w-100">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>

    <!-- Bootstrap JS for dropdown button-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
