<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/profile.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="profile-container">
        <h1>Profile</h1>

        <?php
        session_start();
        
        try {
            if (!isset($_SESSION['username'])) {
                throw new Exception('You are not logged in.');
            }

            $sessionUsername = $_SESSION['username'];
            include 'db_connection.php';

            // Create PDO connection
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Check customer table first
            $stmt = $conn->prepare("SELECT * FROM customer WHERE customer_mobile_number = :username");
            $stmt->bindParam(':username', $sessionUsername);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                echo "<h2>Customer Details:</h2>";
                echo "<div class='info-box'>";
                echo "<p><span>Username:</span> " . htmlspecialchars($row['customer_mobile_number']) . "</p>";
                echo "<p><span>Name:</span> " . htmlspecialchars($row['customer_name']) . "</p>";
                echo "<p><span>Email:</span> " . htmlspecialchars($row['customer_email']) . "</p>";
                echo "<p><span>Address:</span> " . htmlspecialchars($row['customer_address']) . "</p>";
                echo "<p><span>Date of Birth:</span> " . htmlspecialchars($row['customer_dob']) . "</p>";
                echo "</div>";
                
                echo "<div class='buttons'>";
                echo "<button class='btn continue-shopping' onclick='window.location.href=\"../index.html\"'>Continue Shopping</button>";
                echo "<button class='btn signout' onclick='window.location.href=\"signout.php\"'>Sign Out</button>";
                echo "</div>";
            } else {
                // Check employee table if not found in customer table
                $stmt = $conn->prepare("SELECT * FROM employee WHERE employee_id = :username");
                $stmt->bindParam(':username', $sessionUsername);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch();
                    echo "<h2>Employee Details:</h2>";
                    echo "<div class='info-box'>";
                    echo "<p><span>Employee ID:</span> " . htmlspecialchars($row['employee_id']) . "</p>";
                    echo "<p><span>Name:</span> " . htmlspecialchars($row['employee_name']) . "</p>";
                    echo "<p><span>Email:</span> " . htmlspecialchars($row['employee_email']) . "</p>";
                    echo "<p><span>Address:</span> " . htmlspecialchars($row['employee_address']) . "</p>";
                    echo "<p><span>Gender:</span> " . htmlspecialchars($row['employee_gender']) . "</p>";
                    echo "<p><span>Date of Birth:</span> " . htmlspecialchars($row['employee_dob']) . "</p>";
                    echo "<p><span>Role:</span> " . htmlspecialchars($row['employee_role']) . "</p>";
                    echo "<p><span>Joining Date:</span> " . htmlspecialchars($row['employee_joining_date']) . "</p>";
                    echo "<p><span>Salary:</span> " . htmlspecialchars($row['employee_salary']) . "</p>";
                    echo "</div>";
                } else {
                    throw new Exception('User not found in database.');
                }
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo "<p class='error-message'>Database error occurred. Please try again later.</p>";
        } catch (Exception $e) {
            echo "<p class='error-message'>" . htmlspecialchars($e->getMessage()) . "</p>";
        } finally {
            // Close connection
            $conn = null;
        }
        ?>
    </div>
</body>
</html>