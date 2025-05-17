<?php
session_start();
include 'db_connection.php';

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $searchQuery = '';
    $orders = [];

    // Handle search
    if (isset($_POST['search'])) {
        $searchQuery = htmlspecialchars($_POST['search_term']);
        $searchTerm = "%$searchQuery%";
        $stmt = $conn->prepare("SELECT * FROM ordered 
                               WHERE order_id LIKE :searchTerm 
                               OR customer_mobile_number LIKE :searchTerm 
                               ORDER BY order_id DESC");
        $stmt->bindParam(':searchTerm', $searchTerm);
        $stmt->execute();
        $orders = $stmt->fetchAll();
    } else {
        $stmt = $conn->query("SELECT * FROM ordered ORDER BY order_id DESC");
        $orders = $stmt->fetchAll();
    }

    // Handle status update
    if (isset($_POST['update_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];

        $updateStmt = $conn->prepare("UPDATE ordered SET order_status = :status WHERE order_id = :order_id");
        $updateStmt->bindParam(':status', $status);
        $updateStmt->bindParam(':order_id', $order_id);
        
        if ($updateStmt->execute()) {
            $_SESSION['toast'] = ['message' => 'Order status updated successfully!', 'type' => 'success'];
        } else {
            $_SESSION['toast'] = ['message' => 'Error updating order status', 'type' => 'error'];
        }
        header("Location: manage-order.php");
        exit();
    }

    // Handle order deletion
    if (isset($_POST['delete_order'])) {
        $order_id = $_POST['order_id'];

        $deleteStmt = $conn->prepare("DELETE FROM ordered WHERE order_id = :order_id");
        $deleteStmt->bindParam(':order_id', $order_id);
        
        if ($deleteStmt->execute()) {
            $_SESSION['toast'] = ['message' => 'Order deleted successfully!', 'type' => 'success'];
        } else {
            $_SESSION['toast'] = ['message' => 'Error deleting order', 'type' => 'error'];
        }
        header("Location: manage-order.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['toast'] = ['message' => 'Database error occurred', 'type' => 'error'];
    header("Location: manage-order.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track order</title>
    <link rel="stylesheet" href="../css/manage-order.css">
    <link rel="stylesheet" href="../css/toast.css">
</head>
<body>
    <main>
        <div class="admin-panel">
            <h2>Order Management</h2>

            <!-- Search Form -->
            <form method="POST" action="" class="search-form">
                <input type="text" name="search_term" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                       placeholder="Search by Order ID or Customer" required>
                <button type="submit" name="search">Search</button>
            </form>

            <?php if (!empty($orders)): ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Mobile</th>
                            <th>Total Cost</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_mobile_number']); ?></td>
                                <td>TND<?php echo number_format($row['total_cost'], 2); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($row['payment_method'])); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                        <select name="status">
                                            <option value="Pending" <?php echo ($row['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Shipped" <?php echo ($row['order_status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo ($row['order_status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                        </select>
                                        <button type="submit" name="update_status">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                        <button type="submit" name="delete_order" 
                                                onclick="return confirm('Are you sure you want to delete this order?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
        <div id="toast-container"></div>
    </main>
    <script src="../js/toast.js"></script>
    <script>
        <?php if (isset($_SESSION['toast'])): ?>
            showToast('<?php echo $_SESSION['toast']['message']; ?>', '<?php echo $_SESSION['toast']['type']; ?>', 2000);
            <?php unset($_SESSION['toast']); ?>
        <?php endif; ?>
    </script>
</body>
</html>

<?php
// Close connection
$conn = null;
?>