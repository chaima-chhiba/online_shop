<?php
include "db_connection.php"; // This file should now contain PDO credentials

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare and execute the statement
    $stmt = $conn->prepare("SELECT product_id, product_name, product_price, product_location FROM product");
    $stmt->execute();
    
    // Set the resulting array to associative
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $jsonPath = '../json/products.json';
    if (file_put_contents($jsonPath, json_encode($products, JSON_PRETTY_PRINT))) {
        // File write successful
    } else {
        error_log("Error: Failed to write JSON file.");
    }
} catch(PDOException $e) {
    // Handle database connection errors
    die(json_encode(["error" => "Connection failed: " . $e->getMessage()]));
} finally {
    // Close connection
    $conn = null;
}
?>