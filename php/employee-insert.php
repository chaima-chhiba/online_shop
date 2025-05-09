<?php
include 'db_connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get form data
        $employee_id = $_POST['employee_id'];
        $employee_password = $_POST['employee_password'];
        $employee_mobile_number = $_POST['employee_mobile_number'];
        $employee_email = $_POST['employee_email'];
        $employee_name = $_POST['employee_name'];
        $employee_gender = $_POST['employee_gender'];
        $employee_dob = $_POST['employee_dob'];
        $employee_address = $_POST['employee_address'];
        $employee_role = $_POST['employee_role'];
        $employee_joining_date = $_POST['employee_joining_date'];
        $employee_salary = $_POST['employee_salary'];

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO employee (
            employee_id, employee_password, employee_mobile_number, employee_email, 
            employee_name, employee_gender, employee_dob, employee_address, 
            employee_role, employee_joining_date, employee_salary
        ) VALUES (
            :employee_id, :employee_password, :employee_mobile_number, :employee_email, 
            :employee_name, :employee_gender, :employee_dob, :employee_address, 
            :employee_role, :employee_joining_date, :employee_salary
        )");

        // Bind parameters
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':employee_password', $employee_password);
        $stmt->bindParam(':employee_mobile_number', $employee_mobile_number);
        $stmt->bindParam(':employee_email', $employee_email);
        $stmt->bindParam(':employee_name', $employee_name);
        $stmt->bindParam(':employee_gender', $employee_gender);
        $stmt->bindParam(':employee_dob', $employee_dob);
        $stmt->bindParam(':employee_address', $employee_address);
        $stmt->bindParam(':employee_role', $employee_role);
        $stmt->bindParam(':employee_joining_date', $employee_joining_date);
        $stmt->bindParam(':employee_salary', $employee_salary, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();
        
        echo "Employee record inserted successfully.";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Connection closes automatically when script ends
}
?>