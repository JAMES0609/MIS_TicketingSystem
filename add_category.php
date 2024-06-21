<?php
require_once 'db.php';  // Ensure this path correctly points to your database connection script

// Check for the expected POST variables
if(isset($_POST['categoryName'])) {
    $categoryName = $_POST['categoryName'];

    // Prepare SQL to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO Category (category_name, category_status) VALUES (?, 'active')");
    $stmt->bind_param("s", $categoryName);

    // Execute and check the result
    if($stmt->execute()) {
        echo "Category added successfully";
    } else {
        error_log("Error on insert: " . $stmt->error);
        http_response_code(500);
        echo "Error adding category";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Category name not provided";
}

$conn->close();
?>
