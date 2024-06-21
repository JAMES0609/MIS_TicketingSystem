<?php
require 'vendor/autoload.php';  // Ensure dompdf is loaded

use Dompdf\Dompdf;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MIS_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Capture form data and validate
$department = $_POST['department'] ?? '';
$personnel = $_POST['personnel'] ?? '';
$category = $_POST['category'] ?? '';
$priority = $_POST['priority'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

// Basic validation
if (empty($department) || empty($personnel) || empty($category) || empty($priority) || empty($start_date) || empty($end_date)) {
    die("All fields are required.");
}

// Insert data into the database
$sql = "INSERT INTO service_requests (department, name, category, priority, date, schedule) 
        VALUES ('$department', '$personnel', '$category', '$priority', '$start_date', '$end_date')";

if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    exit;
}

// Generate PDF
ob_start();
include 'src/includes/header.php';
$header = ob_get_clean();

ob_start();
include 'src/includes/footer.php';
$footer = ob_get_clean();

$html = "
$header
<div class='report-content'>
    <h2>Report Submission</h2>
    <p><strong>Department:</strong> $department</p>
    <p><strong>Personnel:</strong> $personnel</p>
    <p><strong>Category:</strong> $category</p>
    <p><strong>Priority:</strong> $priority</p>
    <p><strong>Start Date:</strong> $start_date</p>
    <p><strong>End Date:</strong> $end_date</p>
</div>
$footer
";

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('report.pdf', array("Attachment" => false));

// Close the database connection
$conn->close();
?>
