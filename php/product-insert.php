<?php
include 'db_connection.php';

// Set target directory for product images
$targetDir = "../product_img/";

// Create directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

/**
 * Get next file number for sequential naming
 * 
 * @param string $dir Directory to scan
 * @param string $prefix Filename prefix
 * @return int Next available number
 */
function getNextFileNumber($dir, $prefix)
{
    $files = glob($dir . $prefix . '*'); 
    if (empty($files)) {
        return 1; 
    }

    $numbers = array_map(function ($file) use ($prefix) {
        $basename = basename($file);
        $number = intval(str_replace([$prefix, '.'], '', $basename));
        return $number;
    }, $files);

    return max($numbers) + 1; // Get the next number
}

try {
    // Get form data with basic validation
    $product_id = trim($_POST['product_id']);
    $product_category = trim($_POST['product_category']);
    $product_name = trim($_POST['product_name']);
    $product_price = floatval($_POST['product_price']);
    $product_quantity = intval($_POST['product_quantity']);
    
    // Validate required fields
    if (empty($product_id) || empty($product_name) || $product_price <= 0) {
        throw new Exception("Please fill in all required fields with valid values.");
    }

    // Check if image was uploaded successfully
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileExtension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        
        // Validate file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG and GIF files are allowed.");
        }
        
        // Create unique filename
        $prefix = "p";
        $nextNumber = getNextFileNumber($targetDir, $prefix);
        $newFileName = $prefix . $nextNumber . '.' . $fileExtension;
        $destination = $targetDir . $newFileName;

        // Move uploaded file to target directory
        if (move_uploaded_file($fileTmpPath, $destination)) {
            $product_location = $destination;
            
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO product 
                (product_id, product_category, product_name, product_price, product_quantity, product_location)
                VALUES (:id, :category, :name, :price, :quantity, :location)");
                
            // Bind parameters
            $stmt->bindParam(':id', $product_id);
            $stmt->bindParam(':category', $product_category);
            $stmt->bindParam(':name', $product_name);
            $stmt->bindParam(':price', $product_price);
            $stmt->bindParam(':quantity', $product_quantity);
            $stmt->bindParam(':location', $product_location);
            
            // Execute the query
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Product inserted successfully!']);
            } else {
                throw new Exception("Error inserting product in database.");
            }
        } else {
            throw new Exception("Error moving the uploaded file.");
        }
    } else {
        $errorCodes = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
        ];
        
        $errorMessage = isset($_FILES['product_image']) ? 
            $errorCodes[$_FILES['product_image']['error']] ?? "Unknown upload error." : 
            "No image uploaded.";
            
        throw new Exception($errorMessage);
    }
} catch (PDOException $e) {
    // Handle database errors
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    // Handle general errors
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>