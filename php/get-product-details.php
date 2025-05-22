<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
include 'db_connection.php';

try {
    $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($productId <= 0) {
        echo json_encode(['error' => 'Invalid product ID']);
        exit;
    }

    $sql = "SELECT * FROM product WHERE product_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    echo json_encode($product);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>