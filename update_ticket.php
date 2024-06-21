<?php
require_once 'db.php'; // Include your database connection script

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $ticketId = isset($_POST['ticket_id']) ? $conn->real_escape_string($_POST['ticket_id']) : null;

    try {
        if (!$ticketId) {
            throw new Exception("Ticket ID is missing.");
        }

        switch ($action) {
            case 'approve':
                if (empty($_POST['schedule_date'])) {
                    throw new Exception("Schedule date is required. Ticket ID: " . $ticketId);
                }
                $date = DateTime::createFromFormat('Y-m-d', $_POST['schedule_date']);
                if (!$date) {
                    throw new Exception("Invalid date format. Please use YYYY-MM-DD format.");
                }
                $schedule = $conn->real_escape_string($date->format('Y-m-d'));
                $updateSql = "UPDATE service_requests SET ticket_status = 'approved', schedule = '$schedule' WHERE request_id = '$ticketId'";
                break;
            case 'deny':
                $updateSql = "UPDATE service_requests SET ticket_status = 'denied' WHERE request_id = '$ticketId'";
                break;
                case 'reschedule':
                    if (empty($_POST['new_schedule_date'])) {
                        throw new Exception("New schedule date is required. Ticket ID: " . $ticketId);
                    }
                    $newDate = DateTime::createFromFormat('Y-m-d', $_POST['new_schedule_date']);
                    if (!$newDate) {
                        throw new Exception("Invalid date format. Please use YYYY-MM-DD format.");
                    }
                    $newSchedule = $conn->real_escape_string($newDate->format('Y-m-d'));
                    $updateSql = "UPDATE service_requests SET schedule = '$newSchedule' WHERE request_id = '$ticketId'";
                    break;
                    case 'close':
                        $updateSql = "UPDATE service_requests SET ticket_status = 'closed' WHERE request_id = '$ticketId'";
                        break;
                        case 'update':
                            $description = $conn->real_escape_string($_POST['description']);
                            $priority = $conn->real_escape_string($_POST['priority']);
                            $status = $conn->real_escape_string($_POST['status']);
                            $category = $conn->real_escape_string($_POST['category']);
                            $supervisor = $conn->real_escape_string($_POST['supervisor']);
                            $department = $conn->real_escape_string($_POST['department']);
                            $location = $conn->real_escape_string($_POST['location']);
                            $name = $conn->real_escape_string($_POST['name']);
                            $contact = $conn->real_escape_string($_POST['contact']);
                            $email = $conn->real_escape_string($_POST['email']);
                     
                                $updateSql = "UPDATE service_requests SET description = '$description', priority = '$priority', ticket_status = '$status', category = '$category', supervisor = '$supervisor', department = '$department', location = '$location', name = '$name', contact = '$contact', email = '$email' WHERE request_id = '$ticketId'";
                            break;
                            
                        
            default:
                throw new Exception("Invalid action.");
        }

        // Debug SQL query
        error_log("Executing SQL: $updateSql");

        if ($conn->query($updateSql) === TRUE) {
            echo json_encode(['message' => "Ticket status updated to $action successfully."]);
        } else {
            throw new Exception('Error updating ticket: ' . $conn->error);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request or missing action']);
}
?>


