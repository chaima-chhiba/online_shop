<?php
// Database configuration
include 'db_connection.php';

// Initialize variables for messages and product data
$message = "";
$alertType = "";
$editProductData = null;

try {
    // Handle form submission for updating and deleting products
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['update_product'])) {
            // Update product
            $product_id = $_POST['product_id'];
            $product_category = $_POST['product_category'];
            $product_name = $_POST['product_name'];
            $product_price = $_POST['product_price'];
            $product_quantity = $_POST['product_quantity'];

            // SQL UPDATE query with prepared statement
            $stmt = $conn->prepare("UPDATE product 
                    SET product_category = :category, 
                        product_name = :name, 
                        product_price = :price, 
                        product_quantity = :quantity 
                    WHERE product_id = :id");
            
            // Bind parameters
            $stmt->bindParam(':category', $product_category);
            $stmt->bindParam(':name', $product_name);
            $stmt->bindParam(':price', $product_price);
            $stmt->bindParam(':quantity', $product_quantity);
            $stmt->bindParam(':id', $product_id);
            
            // Execute the statement
            if ($stmt->execute()) {
                $message = "Product updated successfully!";
                $alertType = "success";
            } else {
                $message = "Error updating product.";
                $alertType = "error";
            }
        }

        if (isset($_POST['delete_product'])) {
            // Delete product
            $product_id = $_POST['product_id'];

            // SQL DELETE query with prepared statement
            $stmt = $conn->prepare("DELETE FROM product WHERE product_id = :id");
            $stmt->bindParam(':id', $product_id);
            
            if ($stmt->execute()) {
                $message = "Product deleted successfully!";
                $alertType = "success";
            } else {
                $message = "Error deleting product.";
                $alertType = "error";
            }
        }
    }

    // Fetch product data
    $stmt = $conn->prepare("SELECT * FROM product");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch product for edit
    if (isset($_GET['edit_product_id'])) {
        $editProductId = $_GET['edit_product_id'];
        $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = :id");
        $stmt->bindParam(':id', $editProductId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $editProductData = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = "Product not found!";
            $alertType = "error";
        }
    }
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
    $alertType = "error";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List and Update Form</title>
    <link rel="stylesheet" href="../css/loadProductList.css">
    <script src="../js/search_table.js" defer></script>
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?= $alertType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <!-- Product List -->
        <div class="product-list">
            <h2>Product List</h2>

            <!-- Search Box -->
            <input type="text" id="productSearchInput" onkeyup="productSearchTable()" placeholder="Search for products...">

            <?php if (!empty($result)): ?>
                <table id="productTable">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_id']) ?></td>
                                <td><?= htmlspecialchars($row['product_category']) ?></td>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['product_price']) ?></td>
                                <td><?= htmlspecialchars($row['product_quantity']) ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($row['product_location']) ?>"
                                        alt="<?= htmlspecialchars($row['product_name']) ?>" style="max-width: 60px;">
                                </td>
                                <td>
                                    <!-- Action Buttons (Edit and Delete) -->
                                    <div style="display: flex; gap: 3px; align-items: left;">
                                        <!-- Edit Button -->
                                        <form method="GET" action="" style="display: inline;">
                                            <input type="hidden" name="edit_product_id" value="<?= $row['product_id'] ?>">
                                            <button type="submit" name="edit_product"
                                                title="Edit">✏️</button>
                                        </form>

                                        <!-- Delete Button -->
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                            <button type="submit" name="delete_product"
                                                onclick="return confirm('Are you sure you want to delete this product?')"
                                                title="Delete">❌</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No product data found.</p>
            <?php endif; ?>
        </div>

        <!-- Update Form -->
        <div class="form-container">
            <h2>Update Product</h2>
            <form method="POST" action="">
                <label for="product_id">Product ID</label>
                <input type="text" id="product_id" name="product_id"
                    value="<?= $editProductData ? htmlspecialchars($editProductData['product_id']) : '' ?>" required
                    readonly>

                <label for="product_category">Category</label>
                <input type="text" id="product_category" name="product_category"
                    value="<?= $editProductData ? htmlspecialchars($editProductData['product_category']) : '' ?>" required>

                <label for="product_name">Name</label>
                <input type="text" id="product_name" name="product_name"
                    value="<?= $editProductData ? htmlspecialchars($editProductData['product_name']) : '' ?>" required>

                <label for="product_price">Price</label>
                <input type="number" id="product_price" name="product_price"
                    value="<?= $editProductData ? htmlspecialchars($editProductData['product_price']) : '' ?>" step="0.01"
                    required>

                <label for="product_quantity">Quantity</label>
                <input type="number" id="product_quantity" name="product_quantity"
                    value="<?= $editProductData ? htmlspecialchars($editProductData['product_quantity']) : '' ?>" required>

                <button type="submit" name="update_product">Update Product</button>
            </form>
        </div>
    </div>

</body>

</html>