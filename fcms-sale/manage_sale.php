<?php
session_start();
include 'db.php'; // Include the database configuration

// Ensure the staff ID is set in session
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

$staff_id = $_SESSION['staff_id'];

// Fetch the role of the logged-in staff
require 'db_connection.php';
$query = "SELECT role FROM staff WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role'] ?? ''; // 'admin' or 'staff'

if (!$role) {
    header("Location: login.php");
    exit;
}

// Show buttons based on role
if ($role === 'admin'): ?>
    <button class="btn btn-add">Add Product</button>
    <button class="btn btn-edit">Edit</button>
    <button class="btn btn-delete">Delete</button>
<?php endif;

if ($role === 'staff'): ?>
    <button class="btn btn-edit">Edit</button>
<?php endif;

// Restrict access if not admin or staff
if (!in_array($role, ['admin', 'staff'])) {
    echo "<p>Access Denied.</p>";
    exit;
}

// Fetch all products
$stmt = $conn->prepare("SELECT * FROM PRODUCT ORDER BY ProductName ASC");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $productID = $_POST['product_ID'];
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    $productStock = $_POST['product_stock'];
    $productImage = $_POST['product_image']; // Assume image URL is provided

    // Prepared statement to insert product into the database
    $stmt = $conn->prepare("INSERT INTO PRODUCT (ProductID, ProductName, ProductPrice, StockStatus, ProductImage) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $productID, $productName, $productPrice, $productStock, $productImage);
    $stmt->execute();
    $stmt->close();

    // Redirect with success message
    echo "<script>alert('Product successfully added!'); window.location='manage_sale.php';</script>";
    exit();
}

// Handle updating a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $productID = $_POST['product_id'];
    $productPrice = $_POST['product_price'];
    $productStock = $_POST['product_stock'];
    $productImage = $_POST['product_image'];

    $stmt = $conn->prepare("UPDATE PRODUCT SET ProductPrice = ?, StockStatus = ?, ProductImage = ? WHERE ProductID = ?");
    $stmt->bind_param("dsss", $productPrice, $productStock, $productImage, $productID); // Corrected type specifiers
    $stmt->execute();
    $stmt->close();

    // Redirect with success message
    echo "<script>alert('Product successfully updated!'); window.location='manage_sale.php';</script>";
    exit();
}

// Handle deleting a product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $productID = $_POST['product_id'];

    // Prompt confirmation using JavaScript
    echo "<script>
    if (confirm('Are you sure you want to delete this product?')) {
        window.location = 'manage_sale.php?delete_product_id=" . $productID . "';
    }
    </script>";
    exit();
}

// Handle delete product action from URL
if (isset($_GET['delete_product_id'])) {
    $productID = $_GET['delete_product_id'];
    $stmt = $conn->prepare("DELETE FROM PRODUCT WHERE ProductID = ?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $stmt->close();

    // Show success message after deletion
    echo "<script>alert('Product successfully deleted!'); window.location='manage_sale.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff - Sales Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0; padding: 0; 
        }
        .container { 
            width: 80%; 
            margin: 20px auto; 
        }
        h3 { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th, td { padding: 10px; 
            text-align: left; 
            border: 1px solid #ddd; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .btn {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn:hover { 
            background: #555; 
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
        }
        .form-group input, .form-group select { 
            width: 100%; 
            padding: 8px; 
            box-sizing: border-box;
        }
        .btn-container {
            display: flex;
            gap: 10px; /* Space between buttons */
            justify-content: flex-start; /* Align buttons to the left (adjust as needed) */
            align-items: center; /* Vertically align buttons */
        }
        .btn-container form { 
            display: inline-block; 
        }
        /* Style for icons and text */
        .action-btn {
            text-align: center;
        } 
        .btn i {
            margin-right: 5px; /* Space between icon and text */
        }
        .action-btn p {
            font-size: 12px;
        }
        .btn-edit {
            background-color: #4CAF50; /* Green for Edit */
            color: white;
        }
        .btn-delete {
            background-color: red; /* Red for Delete */
            color: white;
        }

        .btn-delete:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1>Product Information</h1>
    </div>
</header>

<div class="container">
    <h3>Manage Products</h3>

    <!-- Form to add a new product -->
    <form method="post" action="">
        <div class="form-group">
            <label for="product_ID">Product ID:</label>
            <input type="text" id="product_ID" name="product_ID" required>
        </div>
        <div class="form-group">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name" required>
        </div>
        <div class="form-group">
            <label for="product_price">Price:</label>
            <input type="number" step="0.01" id="product_price" name="product_price" required>
        </div>
        <div class="form-group">
            <label for="product_stock">Stock Status:</label>
            <select id="product_stock" name="product_stock" required>
                <option value="Available">Available</option>
                <option value="Not Available">Not Available</option>
            </select>
        </div>
        <div class="form-group">
            <label for="product_image">Image URL:</label>
            <input type="text" id="product_image" name="product_image" required>
        </div>

        <button type="submit" name="add_product" class="btn">Add Product</button>
    </form>

    <h3>Existing Products</h3>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['ProductID']); ?></td>
                <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                <td>RM<?php echo number_format($product['ProductPrice'], 2); ?></td>
                <td><?php echo htmlspecialchars($product['StockStatus']); ?></td>
                <td><img src="/Workshop2/image/<?php echo htmlspecialchars($product['ProductImage']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>" style="width: 50px;"></td>
                <td class="btn-container">
                    <div class="action-btn">
                        <form method="post" action="manage_sale.php#editForm">
                            <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['ProductName']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo $product['ProductPrice']; ?>">
                            <input type="hidden" name="product_stock" value="<?php echo htmlspecialchars($product['StockStatus']); ?>">
                            <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['ProductImage']); ?>">
                        <form method="post" action="manage_sale.php#editForm">
                            <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                            <button type="submit" name="edit_product" class="btn btn-edit">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </button>
                        </form>
                        <form method="post" action="manage_sale.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                            <button type="submit" name="delete_product" class="btn btn-delete">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                    </div>

                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Form to update an existing product -->
    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])): ?>
        <a name="editForm"></a>
        <h3>Edit Product</h3>
        <form method="post" action="">
            <input type="hidden" name="product_id" value="<?php echo $_POST['product_id']; ?>">
            <div class="form-group">
                <label for="edit_product_name">Product Name:</label>
                <input type="text" id="edit_product_name" name="product_name" value="<?php echo htmlspecialchars($_POST['product_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="edit_product_price">Price:</label>
                <input type="number" step="0.01" id="edit_product_price" name="product_price" value="<?php echo $_POST['product_price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="edit_product_stock">Stock Status:</label>
                <select id="edit_product_stock" name="product_stock" required>
                    <option value="Available" <?php echo ($_POST['product_stock'] == "Available") ? "selected" : ""; ?>>Available</option>
                    <option value="Not Available" <?php echo ($_POST['product_stock'] == "Not Available") ? "selected" : ""; ?>>Not Available</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_product_image">Image URL:</label>
                <input type="text" id="edit_product_image" name="product_image" value="<?php echo htmlspecialchars($_POST['product_image']); ?>" required>
            </div>
            <button type="submit" name="update_product" class="btn">Update Product</button>
            <a href="manage_sale.php" class="btn">Cancel</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
