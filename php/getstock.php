<?php
header('Content-Type: application/json');

try {
    // Validate input
    if (!isset($_GET['productId']) || !is_numeric($_GET['productId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid product ID']);
        exit;
    }

    $productId = (int)$_GET['productId'];

    // Database connection
    include 'db_connection.php';

    // Create PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Query for product quantity using prepared statement
    $query = "SELECT product_quantity FROM product WHERE product_id = :productId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->execute();

    if ($row = $stmt->fetch()) {
        echo json_encode(['quantity' => $row['product_quantity']]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred']);
} finally {
    // Close connection
    $conn = null;
}
?>