
<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Ensure no output before headers
ob_start();

include 'db_connection.php';

try {
    $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($productId <= 0) {
        echo json_encode(['error' => 'Invalid product ID']);
        exit;
    }

    // Get the price of the current product
    $sql = "SELECT product_price FROM product WHERE product_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentProduct) {
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    // Fetch up to 4 related products within ±20% of the current product's price
    $price = $currentProduct['product_price'];
    $minPrice = $price * 0.8; // 80% of the price
    $maxPrice = $price * 1.2; // 120% of the price
    $sql = "SELECT product_id, product_name, product_price, product_location 
            FROM product 
            WHERE product_id != :id 
            AND product_price BETWEEN :minPrice AND :maxPrice 
            LIMIT 4";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':minPrice', $minPrice, PDO::PARAM_STR);
    $stmt->bindValue(':maxPrice', $maxPrice, PDO::PARAM_STR);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output JSON and clean buffer
    echo json_encode($products);
    ob_end_flush();
    exit;

} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>
