<?php
header('Content-Type: application/json');
// Database connection
include 'db_connection.php';
try {
    // Get category filter if provided
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    
    // Base SQL query
    $sql = "SELECT * FROM product";
    $params = [];
    
    // Add category filter if specified
    if ($category) {
        $sql .= " WHERE product_category LIKE :category";
        $params[':category'] = '%' . $category . '%';
    }
    
    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    }
    $stmt->execute();
   
    // Fetch all rows as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
    // Write products to a JSON file
    $jsonFilePath = '../json/products.json';
    if (file_put_contents($jsonFilePath, json_encode($products, JSON_PRETTY_PRINT)) === false) {
        throw new Exception('Failed to create JSON file');
    }
   
    // Pagination setup
    $productsPerPage = 8; // Number of products per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $productsPerPage;
   
    // Slice the products array for the current page
    $paginatedProducts = array_slice($products, $start, $productsPerPage);
   
    // Calculate total pages
    $totalProducts = count($products);
    $totalPages = ceil($totalProducts / $productsPerPage);
   
    // Return the paginated products and total pages as JSON
    echo json_encode([
        'products' => $paginatedProducts,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalProducts' => $totalProducts
    ]);
   
} catch (PDOException $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Handle other errors
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>