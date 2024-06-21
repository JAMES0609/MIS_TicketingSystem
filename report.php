<?php
// Ensure all Composer dependencies are loaded
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Enable error reporting for debugging (optional, remove in production)
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture and sanitize form data
    $department = $conn->real_escape_string($_POST['department'] ?? '');
    $personnel = $conn->real_escape_string($_POST['personnel'] ?? '');
    $category = $conn->real_escape_string($_POST['category'] ?? '');
    $priority = $conn->real_escape_string($_POST['priority'] ?? '');
    $start_date = $conn->real_escape_string($_POST['start_date'] ?? '');
    $end_date = $conn->real_escape_string($_POST['end_date'] ?? '');

    // Build query with filters
    $query = "SELECT * FROM service_requests WHERE 1=1";
    if ($department) $query .= " AND department = '$department'";
    if ($personnel) $query .= " AND name LIKE '%$personnel%'";
    if ($category) $query .= " AND category = '$category'";
    if ($priority) $query .= " AND priority = '$priority'";
    if ($start_date) $query .= " AND date >= '$start_date'";
    if ($end_date) $query .= " AND date <= '$end_date'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 8px 12px; border: 1px solid #ddd; }
                th { background-color: #f2f2f2; }
                h2 { text-align: center; }
            </style>
        </head>
        <body>
            <h2>Service Requests Report</h2>
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Supervisor</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Ticket Status</th>
                        <th>Email</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>' . $row['request_id'] . '</td>
                        <td>' . $row['date'] . '</td>
                        <td>' . $row['name'] . '</td>
                        <td>' . $row['contact'] . '</td>
                        <td>' . $row['supervisor'] . '</td>
                        <td>' . $row['department'] . '</td>
                        <td>' . $row['category'] . '</td>
                        <td>' . $row['location'] . '</td>
                        <td>' . $row['description'] . '</td>
                        <td>' . $row['priority'] . '</td>
                        <td>' . $row['ticket_status'] . '</td>
                        <td>' . $row['email'] . '</td>
                        <td>' . $row['schedule'] . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';
    } else {
        $html = "<p>No records found for the given criteria.</p>";
    }

    $conn->close();

    // Generate PDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('service_requests_report.pdf', array("Attachment" => true));
    exit; // Ensure the script stops after PDF generation
}
?>

<?php include 'src/includes/header.php'; ?>
<?php include 'src/includes/sidenav.php'; ?>

<div id="layoutSidenav_content">
    <main>
        <div class="report-content">
            <h2>Report Submission Form</h2>

            <form action="report.php" method="post">
                <!-- Form Groups -->
                <div class="report-form-group">
                    <label for="department">Department:</label>
                    <select id="department" name="department">
                        <option value="">Select Department</option>
                        <option value="finance">Finance</option>
                        <option value="operations">Admin</option>
                        <option value="hr">HR</option>
                    </select>
                </div>

                <div class="report-form-group">
                    <label for="personnel">Personnel:</label>
                    <input type="text" id="personnel" name="personnel" placeholder="Enter personnel name(s)">
                </div>

                <div class="report-form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category">
                        <option value="">Select Category</option>
                        <option value="financial">Financial</option>
                        <option value="operational">Admin</option>
                        <option value="hr">HR</option>
                    </select>
                </div>

                <div class="report-form-group">
                    <label>Priority:</label>
                    <input type="radio" id="high" name="priority" value="high">
                    <label for="high">High</label>
                    <input type="radio" id="medium" name="priority" value="medium">
                    <label for="medium">Medium</label>
                    <input type="radio" id="low" name="priority" value="low">
                    <label for="low">Low</label>
                </div>

                <div class="report-form-group">
                    <label for="start-date">Start Date:</label>
                    <input type="date" id="start-date" name="start_date">
                </div>

                <div class="report-form-group">
                    <label for="end-date">End Date:</label>
                    <input type="date" id="end-date" name="end_date">
                </div>

                <input type="submit" value="Generate Report">
            </form>
        </div>
    </main>

    <?php include 'src/includes/footer.php'; ?>
</div>
