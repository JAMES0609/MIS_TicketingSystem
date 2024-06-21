<?php
require_once 'db.php'; // Ensure this path is correct

if (isset($_POST['id'])) {
    $userId = $_POST['id'];
    $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo "User status updated to inactive successfully";
    } else {
        echo "Error updating user status";
    }

    $stmt->close();
} else {
    echo "Invalid request";
}

$conn->close();
?>
