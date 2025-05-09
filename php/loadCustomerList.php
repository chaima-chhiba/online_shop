<?php
// Database configuration
include 'db_connection.php';

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch customer data
    $sql = "SELECT * FROM customer";
    $stmt = $conn->query($sql);

    echo "<h1>Customer Information</h1>";
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Information</title>
    <link rel="stylesheet" href="../css/loadCustomerList.css?v=<?php echo time(); ?>">
</head>

<!-- Search Box -->
<input type="text" id="customerSearchInput" onkeyup="customerSearchTable()" placeholder="Search for customers...">

<?php
    if ($stmt->rowCount() > 0) {
        echo "<div>";

        // Start table and apply some inline styles
        echo "<table id='customerTable'>
                <tr>
                    <th>Mobile Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Date of Birth</th>
                </tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['customer_mobile_number']}</td>
                    <td>{$row['customer_name']}</td>
                    <td>{$row['customer_email']}</td>
                    <td>{$row['customer_address']}</td>
                    <td>{$row['customer_dob']}</td>
                  </tr>";
        }

        echo "</table>";
        echo "</div>"; // End scrollable container
    } else {
        echo "No customers found.";
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Close connection by setting to null
$conn = null;
?>

</html>

<script src="../js/search_table.js"></script>