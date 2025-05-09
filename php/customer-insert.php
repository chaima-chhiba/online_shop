<?php
// Database configuration
include 'db_connection.php';

try {
    // Retrieve form data
    $mobile = $_POST['mobile'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    
    // Consider adding password hashing for security
    // $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Prepare SQL statement with named parameters
    $stmt = $conn->prepare("INSERT INTO customer (
        customer_mobile_number, customer_password, customer_name, 
        customer_email, customer_address, customer_dob
    ) VALUES (
        :mobile, :password, :name, 
        :email, :address, :dob
    )");
    
    // Bind parameters
    $stmt->bindParam(':mobile', $mobile);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':dob', $dob);
    
    // Execute the statement
    $stmt->execute();
    
    echo "Sign up successful! You can now <a href='../html/signin.html'>sign in</a>.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// PDO connection closes automatically when script ends
?>