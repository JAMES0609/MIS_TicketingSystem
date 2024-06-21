<?php

require_once 'db.php'; // Make sure the database connection file is correctly included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO service_requests (date, name, contact, email, supervisor, department, category, location, description, priority, ticket_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssssssss", $_POST['date'], $_POST['name'], $_POST['contact'], $_POST['email'], $_POST['supervisor'], $_POST['department'], $_POST['category'], $_POST['location'], $_POST['description'], $_POST['priority'], $_POST['ticket_status']);

        if ($stmt->execute()) {
            // Optionally, redirect to a confirmation page
            header("Location: user-index.php");
            exit;
        } else {
            echo "Something went wrong: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
    $conn->close();
}
?>
