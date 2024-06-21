<?php
// Assuming you have a connection $conn set up as in your database connection file
require 'db.php';

// Input validation or sanitization (optional but recommended)
$id = $_POST['id'];
$username = $_POST['username'];
$role = $_POST['role'];
$email = $_POST['email'];
$name = $_POST['name'];
$contact_number = $_POST['contact_number'];
$department = $_POST['department'];
$supervisor_head = $_POST['supervisor_head'];

// SQL to update user information
$sql = "UPDATE users SET username=?, role=?, email=?, name=?, contact_number=?, department=?, supervisor_head=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $username, $role, $email, $name, $contact_number, $department, $supervisor_head, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
