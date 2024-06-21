<?php
require_once 'db.php'; // Ensure this path is correct

$search = isset($_POST['search']) ? $_POST['search'] : '';

// Assuming you are using the LIKE SQL command for a simple search functionality
$sql = "SELECT * FROM service_requests WHERE `request_id` LIKE '%$search%' OR `category` LIKE '%$search%' OR `department` LIKE '%$search%' OR `priority` LIKE '%$search%' OR `ticket_status` LIKE '%$search%' ORDER BY request_id ASC";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();

