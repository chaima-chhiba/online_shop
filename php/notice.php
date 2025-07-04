<?php
// Database configuration
include 'db_connection.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $notice_id = $_POST['notice_id'];
        $notice_content = $_POST['notice_content'];

        $sql = "INSERT INTO notice (notice_id, notice_content) VALUES (:notice_id, :notice_content)";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([':notice_id' => $notice_id, ':notice_content' => $notice_content])) {
            $message = "Notice added successfully!";
        } else {
            $message = "Error adding notice.";
        }
    } elseif (isset($_POST['update'])) {
        $notice_id = $_POST['notice_id'];
        $notice_content = $_POST['notice_content'];

        // Check if notice exists
        $check_sql = "SELECT * FROM notice WHERE notice_id = :notice_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute([':notice_id' => $notice_id]);

        if ($check_stmt->rowCount() > 0) {
            $sql = "UPDATE notice SET notice_content = :notice_content WHERE notice_id = :notice_id";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([':notice_id' => $notice_id, ':notice_content' => $notice_content])) {
                $message = "Notice updated successfully!";
            } else {
                $message = "Error updating notice.";
            }
        } else {
            $message = "Notice ID not found for update!";
        }
    } elseif (isset($_POST['delete'])) {
        $notice_id = $_POST['notice_id'];

        $sql = "DELETE FROM notice WHERE notice_id = :notice_id";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([':notice_id' => $notice_id])) {
            $message = "Notice deleted successfully!";
        } else {
            $message = "Error deleting notice.";
        }
    }
}

// Fetch all notices
$notices = [];
$sql = "SELECT * FROM notice";
$stmt = $conn->query($sql);
if ($stmt) {
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Management</title>
    <link rel="stylesheet" href="../css/notice.css?v=<?php echo time(); ?>">
    <script>
        function confirmDelete(noticeId) {
            if (confirm("Are you sure you want to delete this notice?")) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = ''; // Current page

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'notice_id';
                input.value = noticeId;
                form.appendChild(input);

                // Create a hidden input for the delete action
                var deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete';
                form.appendChild(deleteInput);

                // Append the form to the body and submit it
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Notice Management</h2>

        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="notice_id">Notice ID</label>
                <input type="text" id="notice_id" name="notice_id" required>
            </div>
            <div class="form-group">
                <label for="notice_content">Notice Content</label>
                <textarea id="notice_content" name="notice_content" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" name="add">Add Notice</button>
                <button type="submit" name="update">Update Notice</button>
            </div>
        </form>

        <h3>All Notices</h3>
        <table>
            <thead>
                <tr>
                    <th>Notice ID</th>
                    <th>Notice Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($notices) > 0): ?>
                    <?php foreach ($notices as $notice): ?>
                        <tr>
                            <td data-label="Notice ID"><?php echo $notice['notice_id']; ?></td>
                            <td data-label="Notice Content"><?php echo $notice['notice_content']; ?></td>
                            <td data-label="Actions">
                                <button onclick="confirmDelete('<?php echo $notice['notice_id']; ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No notices found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
