<?php
require_once 'db.php'; // Ensure this path is correct

// Make sure you're receiving the expected POST data
if (isset($_POST['departmentId'])) {
    $departmentId = $_POST['departmentId'];

    // Prepare an SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE Department SET department_status = 'inactive' WHERE department_Id = ?");
    $stmt->bind_param("i", $departmentId); // 'i' for integer

    if ($stmt->execute()) {
        echo "Department deleted successfully.";
    } else {
        // Log error to PHP error log
        error_log("Error deleting department: " . $stmt->error);
        http_response_code(500);
        echo "Failed to delete department.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request data.";
}

$conn->close();
?>
