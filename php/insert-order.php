<?php
session_start();
include 'db_connection.php';

header('Content-Type: text/html; charset=UTF-8');

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['username'])) {
            header('Location: ../html/signin.html');
            exit();
        }

        // Validate and sanitize inputs
        $username = $_SESSION['username'];
        $paymentMethod = filter_input(INPUT_POST, 'payment-method', FILTER_SANITIZE_STRING);
        $totalCost = filter_input(INPUT_POST, 'total-cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $status = 'pending';

        // Generate order ID in a transaction to ensure consistency
        $conn->beginTransaction();

        try {
            // Get the latest order ID
            $stmt = $conn->query("SELECT order_id FROM ordered ORDER BY order_id DESC LIMIT 1");
            $newOrderId = '0001'; // Default value
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $newOrderId = str_pad($row['order_id'] + 1, 4, '0', STR_PAD_LEFT);
            }

            // Insert into ordered table
            $query1 = $conn->prepare("INSERT INTO ordered (order_id, customer_mobile_number, total_cost, payment_method, order_status) 
                                     VALUES (:order_id, :username, :total_cost, :payment_method, :status)");
            $query1->bindParam(':order_id', $newOrderId);
            $query1->bindParam(':username', $username);
            $query1->bindParam(':total_cost', $totalCost);
            $query1->bindParam(':payment_method', $paymentMethod);
            $query1->bindParam(':status', $status);
            $query1->execute();

            // Insert into sells table
            $query2 = $conn->prepare("INSERT INTO sells (customer_mobile_number, price, time, payment_method) 
                                     VALUES (:username, :total_cost, NOW(), :payment_method)");
            $query2->bindParam(':username', $username);
            $query2->bindParam(':total_cost', $totalCost);
            $query2->bindParam(':payment_method', $paymentMethod);
            $query2->execute();

            $conn->commit();

            // Order placed successfully
            echo <<<HTML
                <style>
                    .order-slip {
                        border: 1px solid #ddd;
                        padding: 20px;
                        margin-top: 20px;
                        background-color: #f9f9f9;
                        width: 80%;
                        margin-left: auto;
                        margin-right: auto;
                        text-align: center;
                    }
                    .order-slip h2 {
                        color: green;
                    }
                    .order-slip p {
                        font-size: 16px;
                    }
                    .order-slip button {
                        padding: 10px 20px;
                        background-color: #4CAF50;
                        color: white;
                        border: none;
                        cursor: pointer;
                        margin: 5px;
                    }
                    .order-slip button:hover {
                        background-color: #45a049;
                    }
                </style>
                <div class='order-slip'>
                    <h2>Order Placed Successfully!</h2>
                    <p>Your Order ID is: <strong>$newOrderId</strong></p>
                    <p>Total Cost: <strong>TND$totalCost</strong></p>
                    <p>Payment Method: <strong>$paymentMethod</strong></p>
                    <button onclick='window.print();'>Print this Page</button>
                    <button onclick='window.location.href = "../index.html";'>Go back to Home page</button>
                </div>
HTML;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e; // Re-throw to outer catch block
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo "Error processing your order. Please try again later.";
} finally {
    $conn = null;
}
?>