<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departmentId = $_POST['departmentId'];
    $departmentName = $_POST['departmentName'];

    // Prepare an SQL statement to avoid SQL injection
    $stmt = $conn->prepare("UPDATE Department SET department_name = ? WHERE department_Id = ?");
    $stmt->bind_param("si", $departmentName, $departmentId);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>
