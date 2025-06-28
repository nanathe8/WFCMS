<?php
include 'db.php';
include 'headercustomer.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $quantity = (int)$quantity; // Convert to integer to avoid unexpected behavior

            // Remove product if quantity is zero
            if ($quantity === 0) {
                foreach ($_SESSION['cart'] as $key => $item) {
                    if ($item['ProductID'] == $product_id) {
                        unset($_SESSION['cart'][$key]); // Remove the item from the cart
                        $_SESSION['message'] = "You successfully deleted the product."; // Set delete message
                        error_log("Debug: Product with ID $product_id deleted."); // Debug message
                    }
                }
                continue; // Skip further processing for this product
            }

            // Update quantity if the product already exists in the cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['ProductID'] == $product_id) {
                    $item['Quantity'] = $quantity; 
                    $item['Subtotal'] = $item['Price'] * $quantity; 
                    $_SESSION['message'] = "You successfully updated the quantity."; 
                    error_log("Debug: Quantity for product with ID $product_id updated to $quantity."); 
                    $found = true;
                    break;
                }
            }

            // Unset reference after foreach to prevent side effects
            unset($item);

            // Add the product if it doesn't exist in the cart
            if (!$found) {
                // Use prepared statements to prevent SQL injection
                $stmt = $conn->prepare("SELECT ProductName, ProductPrice, ProductImage FROM PRODUCT WHERE ProductID = ?");
                $stmt->bind_param("s", $product_id); // Bind parameter
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $row = $result->fetch_assoc()) {
                    $_SESSION['cart'][] = [
                        'ProductID' => $product_id,
                        'ProductName' => $row['ProductName'],
                        'Price' => $row['ProductPrice'],
                        'Quantity' => $quantity,
                        'Subtotal' => $row['ProductPrice'] * $quantity,
                        'ProductImage' => $row['ProductImage'] // Add image to the session
                    ];
                    $_SESSION['message'] = "You successfully added the product."; // Set add message
                    error_log("Debug: Product with ID $product_id added."); // Debug message
                } else {
                    $_SESSION['message'] = "Product not found in the database."; // Debug message
                    error_log("Debug: Product with ID $product_id not found in database."); // Debug message
                }
                $stmt->close();
            }
        }

        // Redirect to prevent form resubmission
        header("Location: addtocart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0; 
            padding: 0;
        }

        /* .container { 
            width: 80%; 
            margin: 0 auto; 
            overflow: hidden;
        } */
        header { 
            background:#6d5b9e; 
            color: #fff; 
            padding: 20px 0; 
            text-align: center;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color:rgb(0, 0, 0);
        }
        nav a { 
            color: #fff; 
            text-decoration: none; 
            padding: 10px; 
            display: inline-block; 
            margin: 0 5px;
        }
        .cart, .menu { 
            margin: 20px 0; 
            background: #fff; 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table { 
            width: 100%; 
            border-collapse: collapse;
        }
        th, td { 
            padding: 10px; 
            text-align: left; 
            border: 1px solid #ddd;
        }
        th { 
            background-color: #f2f2f2;
        }
        .btn { 
            padding: 10px 20px; 
            background: #333; 
            color: #fff; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px;
        }
        .btn:hover { 
            background: #555; 
        }
        .product-image { 
            width: 100px; 
            height: auto;
            border-radius: 5px;
        }
        .total { 
            font-size: 1.2em; 
            font-weight: bold; 
            margin-top: 20px; 
            text-align: right; 
            padding-right: 10px;
        }
        .buttons { 
            display: flex; 
            justify-content: flex-end; 
            margin-top: 20px;
        }
        .buttons form { 
            margin-left: 10px;
        }
        .message { 
            padding: 10px; 
            margin: 20px 0; 
            color: green; 
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
        </div>
    </header>

    <div class="container">
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='message'>" . $_SESSION['message'] . "</div>";
            unset($_SESSION['message']); // Clear the message after displaying
        }
        ?>
        <h3>Your Cart</h3>
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($_SESSION['cart'] as $index => $item) {
                        echo "<tr>";
                        echo "<td>";
                        if (isset($item['ProductImage'])) {
                            echo "<img src='/Workshop2/image/" . htmlspecialchars($item['ProductImage']) . "' class='product-image'>";
                        } else {
                            echo "No image available";
                        }
                        echo "</td>";
                        echo "<td>" . htmlspecialchars($item['ProductName']) . "</td>";
                        echo "<td>RM" . number_format($item['Price'], 2) . "</td>";
                        echo "<td>
                        <form method='post' action='cart_action.php' style='display:inline;'>
                            <input type='hidden' name='product_index' value='{$index}'>
                            <button type='submit' name='action' value='decrease'>-</button>
                            <span>{$item['Quantity']}</span>
                            <button type='submit' name='action' value='increase'>+</button>
                        </form>
                        </td>";
                        echo "<td>RM" . number_format($item['Subtotal'], 2) . "</td>";
                        echo "<td>
                        <form method='post' action='cart_action.php' style='display:inline;'>
                            <input type='hidden' name='product_index' value='{$index}'>
                            <button type='submit' name='action' value='delete'>Delete</button>
                        </form>
                        </td>";
                        echo "</tr>";
                        $total += $item['Subtotal']; // Accumulate total
                    }
                    ?>
                </tbody>
            </table>

            <div class="total">
                Total: RM<?php echo number_format($total, 2); ?>
            </div>

            <div class="buttons">
                <button onclick="window.location.href='menu.php'" class="btn">Go Back</button>
                <form method="post" action="checkout.php">
                    <button type="submit" name="checkout" class="btn">Checkout</button>
                </form>
            </div>

        <?php else: ?>
            <p>Your cart is empty.</p>
            <button onclick="window.location.href='menu.php'" class="btn">Back to Menu</button>
        <?php endif; ?>
    </div>
            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
