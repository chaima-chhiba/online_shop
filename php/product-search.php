<?php
include 'db_connection.php';

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $searchQuery = isset($_GET['searchQuery']) ? $_GET['searchQuery'] : '';
    $searchTerm = "%" . $searchQuery . "%";

    $sql = "SELECT * FROM product WHERE product_name LIKE :searchTerm";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['products' => $products]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// Close connection
$conn = null;
?>