<?php
// Start the session to store user data
session_start();

// Database configuration
include 'db_connection.php';

try {
    // Retrieve POST data
    $user_input_username = $_POST['username']; 
    $user_input_password = $_POST['password'];
    
    // First check if user exists in customer table
    $stmt = $conn->prepare("SELECT * FROM customer WHERE customer_mobile_number = :username AND customer_password = :password");
    $stmt->bindParam(':username', $user_input_username);
    $stmt->bindParam(':password', $user_input_password);
    $stmt->execute();
    
    // For password verification, consider using:
    // if(password_verify($user_input_password, $row['customer_password'])) { ... }
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['username'] = $user_input_username;
        $_SESSION['role'] = 'Customer'; // Set role as customer
        header("Location: ../index.html"); // Redirect to customer dashboard
        exit();
    } else {
        // If not found in customer table, check employee table
        $stmt = $conn->prepare("SELECT * FROM employee WHERE employee_id = :username AND employee_password = :password");
        $stmt->bindParam(':username', $user_input_username);
        $stmt->bindParam(':password', $user_input_password);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // If user found in 'employee' table
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['username'] = $user_input_username; 
            $_SESSION['role'] = $row['employee_role']; 
            
            // Redirect based on role
            if ($_SESSION['role'] === 'Manager') {
                header("Location: ../html/managerDash.html");
            } elseif ($_SESSION['role'] === 'Admin') {
                header("Location: ../html/adminDash.html");
            } elseif ($_SESSION['role'] === 'CEO') {
                header("Location: ../html/adminDash.html");
            }
            exit();
        } else {
            // User not found in either table
            header("Location: ../html/signin.html?error=1");
            exit();
        }
    }
} catch(PDOException $e) {
    // Log error (avoid displaying database errors to users in production)
    error_log("Login error: " . $e->getMessage());
    header("Location: ../html/signin.html?error=2");
    exit();
}
?>