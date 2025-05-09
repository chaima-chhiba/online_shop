<?php
include 'db_connection.php';

try {
    // Query to get the last product ID
    $stmt = $conn->prepare("SELECT product_id FROM product ORDER BY product_id DESC LIMIT 1");
    $stmt->execute();
    
    // Check if any rows were returned
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastId = $row['product_id'];
        
        // Increment the ID and pad with zeros
        $newId = str_pad((int) $lastId + 1, 4, "0", STR_PAD_LEFT);
    } else {
        // Start with 0001 if no ID found
        $newId = "0001";
    }
    
} catch (PDOException $e) {
    // Log error and return a default ID
    error_log("Error in auto-ID generation: " . $e->getMessage());
    $newId = "0001"; // Default ID in case of error
}

// No need to close connection - PDO handles this automatically
?>