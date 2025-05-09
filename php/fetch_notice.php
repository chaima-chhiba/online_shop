<?php
// fetch_notices.php
include 'db_connection.php';

header('Content-Type: application/json');

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Fetch all notices
    $stmt = $conn->query("SELECT * FROM notice");
    $notices = $stmt->fetchAll();

    echo json_encode($notices ?: []);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
} finally {
    // Close connection
    $conn = null;
}
?>