<?php
require_once 'db.php'; // Ensure this path is correct

// Make sure you're receiving the expected POST data
if(isset($_POST['categoryId']) && isset($_POST['categoryName'])) {
    $categoryId = $_POST['categoryId'];
    $categoryName = $_POST['categoryName'];

    // Prepare an SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE Category SET category_name = ?, category_status = 'inactive' WHERE category_id = ?");
    $stmt->bind_param("si", $categoryName, $categoryId); // 's' for string, 'i' for integer

    if($stmt->execute()) {
        echo "Category Delete successfully.";
    } else {
        // Log error to PHP error log
        error_log("Error updating category: " . $stmt->error);
        http_response_code(500);
        echo "Failed to delete category.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request data.";
}

$conn->close();
?>