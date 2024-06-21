<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'db.php'; // Your database connection file

// Fetching data from the database
$sql = "SELECT ticket_id, start_date, name, contact, supervisor, department, category, location, description, priority FROM service_requests";
$result = $conn->query($sql);

// HTML and CSS structure
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Requests</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>View Service Requests</h2>
    <table>
        <tr>
            <th>Ticket ID</th>
            <th>Start Date</th>
            <th>Name</th>
            <th>Contact Number</th>
            <th>Supervisor</th>
            <th>Department/Office</th>
            <th>Category</th>
            <th>Location</th>
            <th>Description</th>
            <th>Priority</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['ticket_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                echo "<td>" . htmlspecialchars($row['supervisor']) . "</td>";
                echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['priority']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No requests found</td></tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
$conn->close();
?>
