<?php include 'autoFill-id.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Input Form</title>
    <link rel="stylesheet" href="../css/addProduct.css">
    <style>
        .error-message {
            color: red;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .error-field {
            border-color: red !important;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Product Input Form</h2>
        <form id="productForm" action="product-insert.php" method="POST" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="product_id">Product ID</label>
                <input type="text" id="product_id" name="product_id" maxlength="15" value="<?php echo htmlspecialchars($newId); ?>" required readonly>
                <div id="product_id_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="product_category">Product Category</label>
                <select id="product_category" name="product_category" required>
                    <option value="">-- Select Category --</option>
                    <option value="Vintage Dresses">Vintage Dresses</option>
                    <option value="Vintage Jackets">Vintage Jackets</option>
                    <option value="Vintage Tops">Vintage Tops</option>
                    <option value="Vintage Tops">Vintage Jeans</option>
                    <option value="Vintage Tops">Vintage Shirts</option>
                    <option value="Vintage Tops">Vintage Skirts</option>
                </select>
                <div id="product_category_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" maxlength="100" required>
                <div id="product_name_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="product_price">Product Price</label>
                <input type="number" step="0.01" min="0.01" id="product_price" name="product_price" required>
                <div id="product_price_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="product_quantity">Product Quantity</label>
                <input type="number" min="1" id="product_quantity" name="product_quantity" required>
                <div id="product_quantity_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="product_image">Product Image</label>
                <input type="file" id="product_image" name="product_image" accept="image/*" required>
                <div id="product_image_error" class="error-message"></div>
                <small>Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
    <script src="../js/validation.js"></script>
</body>
</html>