<?php 
include 'src/includes/header.php'; 
include 'src/includes/sidenav.php'; 
require_once 'db.php'; // Ensure this path is correct and the file properly sets up your database connection

// Error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle per page selection and page number
$perPageOptions = [10, 20, 50, 'all'];  // Allowed per-page options
$defaultPerPage = 10;
$perPage = isset($_GET['perPage']) && in_array($_GET['perPage'], $perPageOptions) ? $_GET['perPage'] : $defaultPerPage;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Check if a search term is set
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Create SQL query for searching
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $whereClause = "WHERE `request_id` LIKE '%$search%' OR `category` LIKE '%$search%' OR `department` LIKE '%$search%' OR `priority` LIKE '%$search%' OR `ticket_status` LIKE '%$search%'";
} else {
    $whereClause = "";
}

// Determine if 'all' is selected for pagination
if ($perPage === 'all') {
    // If 'all' is selected, fetch all records without a LIMIT clause
    $sql = "SELECT * FROM service_requests $whereClause ORDER BY request_id ASC";
    $result = $conn->query($sql);
    $totalRow['total'] = $result->num_rows; // Count total results for 'all' scenario
    $totalPages = 1;  // Only one page is needed
    $startItem = 1;   // Start item number
    $endItem = $totalRow['total'];  // End item is total number of records
} else {
    // Pagination calculations
    $offset = ($page - 1) * $perPage;
    $sql = "SELECT * FROM service_requests $whereClause ORDER BY request_id ASC LIMIT $offset, $perPage";
    $totalSql = "SELECT COUNT(*) as total FROM service_requests $whereClause";
    $totalResult = $conn->query($totalSql);
    if ($totalResult) {
        $totalRow = $totalResult->fetch_assoc();
        $totalPages = ceil($totalRow['total'] / $perPage);
    } else {
        die('SQL error: ' . $conn->error);
    }
    $result = $conn->query($sql);
    if (!$result) {
        die('SQL error: ' . $conn->error);
    }
    // Calculate starting and ending item numbers
    $startItem = $offset + 1;
    $endItem = min($startItem + $perPage - 1, $totalRow['total']);
}

// Output errors or results
if ($conn->error) {
    echo "SQL Error: " . $conn->error;
} else {
    // Display or process $result records
    // Further processing here...
}


try {
    // Create a single PDO connection for the script
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch departments
    $deptQuery = $conn->query("SELECT department_name FROM Department GROUP BY department_name ORDER BY department_name");
    $departments = $deptQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch categories
    $categoryQuery = $conn->query("SELECT category_name FROM Category GROUP BY category_name ORDER BY category_name");
    $categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle error gracefully, potentially logging it and informing the user
    error_log("DB connection failed: " . $e->getMessage()); // Log error to server's error log
    exit('Database connection failed. Please try again later.'); // Inform user
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container mt-4">
        <div class="actions-with-search">
        <div class="btn-container" role="group" aria-label="Ticket actions">
                <button type="button" class="btn-tickets" onclick="redirectToTicketPage()">+ Ticket</button>
                <input type="text" id="searchInput" class="form-control search-bar" class="fa fa-search" placeholder="Search tickets...">

            </div>
        </div>
            <div class="table-responsive-tickets">
                <table class="table table-striped">
<thead>
    <tr>
        <th class="th-itm" scope="col">
            <!-- Checkbox column -->
        </th>
        <th class="th-itm" scope="col">Ticket ID</th>
        <th class="th-itm" scope="col">
    Priority
    <select id="filter-priority" class="form-control filter-dropdown" style="width: auto; display: inline-block; padding: 0 8px;">
        <option value="">All</option>
        <option value="high">High</option>
        <option value="medium">Medium</option>
        <option value="low">Low</option>
    </select>
</th>
<th class="th-itm" scope="col">
    Category
    <select id="filter-category" name="filter-category" class="form-control filter-dropdown" style="width: auto; display: inline-block; padding: 0 8px;">
        <option value="">All</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['category_name']) ?>" <?= isset($_POST['filter-category']) && $_POST['filter-category'] == $category['category_name'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['category_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</th>

<th class="th-itm" scope="col">
    Department/Office
    <select id="filter-department" name="filter-department" class="form-control filter-dropdown" style="width: auto; display: inline-block; padding: 0 8px;">
        <option value="">All</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= htmlspecialchars($dept['department_name']) ?>" <?= isset($_POST['filter-department']) && $_POST['filter-department'] == $dept['department_name'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($dept['department_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</th>

<th class="th-itm" scope="col">
    Status
    <select id="filter-status" class="form-control filter-dropdown" style="width: auto; display: inline-block; padding: 0 8px;">
        <option value="">All</option>
        <option value="open">Open</option>
        <option value="closed">Closed</option>
        <option value="pending">Pending</option>
    </select>
</th>

<th class="th-itm" scope="col">
    <div class="date-filter">
        Date
        <input type="date" id="filter-date-start" class="form-control date-input">
  
        <input type="date" id="filter-date-end" class="form-control date-input">
    </div>
</th>

        <th class="th-itm" scope="col">Actions</th>
    </tr>
</thead>
                    <tbody>            
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                <td class="td-items"><input type="checkbox" name="selected_tickets[]"></td>
                                <td class="td-items"><?php echo $row['request_id']; ?></td>
                                <td class="td-items"><?php echo $row['priority']; ?></td>
                                <td class="td-items"><?php echo $row['category']; ?></td>
                                <td class="td-items"><?php echo htmlspecialchars($row['department']); ?></td>
                                <td class="td-items"><?php echo $row['ticket_status'];?></td>
                                <td class="td-items"><?php echo $row['date']; ?></td>
                                <td class="td-items">
                                    <button type="button" class="btn btn-primary btn-sm view-btn" 
                                        data-request-id="<?php echo $row['request_id']; ?>"
                                        data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                        data-priority="<?php echo $row['priority']; ?>"
                                        data-ticket-status="<?php echo $row['ticket_status']; ?>"
                                        data-category="<?php echo $row['category']; ?>"
                                        data-supervisor="<?php echo htmlspecialchars($row['supervisor']); ?>"
                                        data-department="<?php echo htmlspecialchars($row['department']); ?>"
                                        data-location="<?php echo htmlspecialchars($row['location']); ?>"
                                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                        data-contact="<?php echo $row['contact']; ?>"
                                        data-email="<?php echo $row['email']; ?>"
                                        data-schedule="<?php echo $row['schedule']; ?>"
                                        data-date="<?php echo $row['date']; ?>">View
                                    </button>
                                </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="13">No tickets found</td></tr>
                        <?php endif; ?>
                    </tbody>
                    
                </table>
                
            </div>
            
            <div class="flex-container">
            <div>
                    <!-- Show number of displayed data and total -->
                    <p>Showing <?= $startItem ?> to <?= $endItem ?> of <?= $totalRow['total'] ?> entries</p>
                </div>
       <!-- Dropdown for selecting per page count -->
<div id="dropdownContainer">
    <select id="perPageSelect" onchange="location = this.value;">
        <option value="">Show data</option>
        <option value="?perPage=10" <?php if(isset($_GET['perPage']) && $_GET['perPage'] == '10') echo 'selected'; ?>>10 entries</option>
        <option value="?perPage=20" <?php if(isset($_GET['perPage']) && $_GET['perPage'] == '20') echo 'selected'; ?>>20 entries</option>
        <option value="?perPage=50" <?php if(isset($_GET['perPage']) && $_GET['perPage'] == '50') echo 'selected'; ?>>50 entries</option>
        <option value="?perPage=all" <?php if(isset($_GET['perPage']) && $_GET['perPage'] == 'all') echo 'selected'; ?>>Show All</option>
    </select>
</div>

  <!-- Pagination Section -->
  <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation example" class="pagination-container">
                    <ul class="pagination">
                        
                        <!-- Previous Button -->
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                    <!-- Page Number Links -->
                    <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $startPage + 4);

                        for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>


                        <!-- Next Button -->
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&gt;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </main>
        
    
<?php include 'src/includes/footer.php'; ?>
</div>


<!-- Modal HTML structure -->
<div id="ticketModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 class="ticketdetails_title">Ticket Details</h2>
        <p><strong>Ticket ID:</strong> <span id="modal-ticket-id"></span></p>
        <p><strong>Description:</strong> <span id="modal-description"></span></p>
        <p><strong>Priority:</strong> <span id="modal-priority"></span></p>
        <p><strong>Status:</strong> <span id="modal-status"></span></p>
        <p><strong>Category:</strong> <span id="modal-category"></span></p>
        <p><strong>Supervisor:</strong> <span id="modal-supervisor"></span></p>
        <p><strong>Department/Office:</strong> <span id="modal-department"></span></p>
        <p><strong>Location:</strong> <span id="modal-location"></span></p>
        <p><strong>Name:</strong> <span id="modal-name"></span></p>
        <p><strong>Contact:</strong> <span id="modal-contact"></span></p>
        <p><strong>Email:</strong> <span id="modal-email"></span></p>
        <p><strong>Date Created:</strong> <span id="modal-date"></span></p>
        <p><strong>Schedule:</strong> <span id="modal-schedule"></span></p>
        <div class="modal-button">
            <input type="hidden" name="ticket_id" id="modal-ticket-id-input" value="">
            
                <button id="editBtn" type="button" class="btn-tickets">Edit</button>
                
                <!-- Edit Ticket Modal -->
                <div id="editTicketModal" class="modal">
                    <div class="modal-content">
                    <span class="close" onclick="closeModal('editTicketModal')">&times;</span>
                        <h2>Edit Ticket Details</h2>
                        <form id="editTicketForm">
                            <p><strong>Ticket ID:</strong> <span id="edit-modal-ticket-id"></span></p>
                            <p><strong>Description:</strong> <input type="text" id="edit-modal-description" name="description"></p>
                            <p><strong>Priority:</strong> <input type="text" id="edit-modal-priority" name="priority"></p>
                            <p><strong>Status:</strong> <input type="text" id="edit-modal-status" name="status"></p>
                            <p><strong>Category:</strong> <input type="text" id="edit-modal-category" name="category"></p>
                            <p><strong>Supervisor:</strong> <input type="text" id="edit-modal-supervisor" name="supervisor"></p>
                            <p><strong>Department/Office:</strong> <input type="text" id="edit-modal-department" name="department"></p>
                            <p><strong>Location:</strong> <input type="text" id="edit-modal-location" name="location"></p>
                            <p><strong>Name:</strong> <input type="text" id="edit-modal-name" name="name"></p>
                            <p><strong>Contact:</strong> <input type="text" id="edit-modal-contact" name="contact"></p>
                            <p><strong>Email:</strong> <input type="text" id="edit-modal-email" name="email"></p>
                            <p><strong>Date Created:</strong> <span id="edit-modal-date"></span></p>
                            <button type="submit" class="btn-tickets">Update Ticket</button>
                        </form>
                    </div>
                </div>


                <button id="approveBtn" class="btn-tickets">Approve</button>
                <div id="approvalModal" class="modal">
                    <div class="modal-content">
                        <!-- Your form for selecting the schedule date -->
                        <span class="approve-modal-close-btn">&times;</span>
                        <form  id="approvalForm">
                            <label id="approve-modal-lab" for="scheduleDate" class="modal-datepicker-title">Select Schedule Date:</label>
                            
                            <input type="date" id="scheduleDate" name="scheduleDate" class="modal-datepicker">
                            
                            <button id="modal-approve-btn" type="submit" class="modal-datepicker-button">Approve</button>
                        </form>
                    </div>
                </div>

                <button id="denyBtn" class="btn-tickets">Deny</button>


                <button id="rescheduleBtn" class="btn-tickets">Re-schedule</button>

                    <!-- Reschedule Modal -->
                        <div id="rescheduleModal" class="modal">
                            <div class="modal-content" >
                                <span class="resched-modal-close-btn">&times;</span>
                                <form id="rescheduleForm">
                                <label for="newScheduleDate" class="modal-datepicker-title" >Select New Schedule Date:</label>
                                <input type="date" id="newScheduleDate" name="newScheduleDate" class="modal-datepicker">
                                <button type="submit" class="modal-datepicker-button">Reschedule</button>
                            </form>
                        </div>
                    </div>
                    
                <button id="markAsClosedBtn" class="btn-tickets">Mark as Closed</button>

        </div>
    </div>
    
</div>



<script>

document.addEventListener('DOMContentLoaded', function () {
    

document.getElementsByClassName('approve-modal-close-btn')[0].addEventListener('click', function() {
    document.getElementById('approvalModal').style.display = 'none';
});
document.getElementsByClassName('resched-modal-close-btn')[0].addEventListener('click', function() {
    document.getElementById('rescheduleModal').style.display = 'none';
});
document.getElementById('rescheduleModal').style.display = 'none';


// Function to handle edit button click
function openEditForm() {
    const ticketId = document.getElementById('modal-ticket-id').innerText;
    document.getElementById('edit-modal-ticket-id').innerText = ticketId;
    document.getElementById('edit-modal-description').value = document.getElementById('modal-description').innerText;
    document.getElementById('edit-modal-priority').value = document.getElementById('modal-priority').innerText;
    document.getElementById('edit-modal-status').value = document.getElementById('modal-status').innerText;
    document.getElementById('edit-modal-category').value = document.getElementById('modal-category').innerText;
    document.getElementById('edit-modal-supervisor').value = document.getElementById('modal-supervisor').innerText;
    document.getElementById('edit-modal-department').value = document.getElementById('modal-department').innerText;
    document.getElementById('edit-modal-location').value = document.getElementById('modal-location').innerText;
    document.getElementById('edit-modal-name').value = document.getElementById('modal-name').innerText;
    document.getElementById('edit-modal-contact').value = document.getElementById('modal-contact').innerText;
    document.getElementById('edit-modal-email').value = document.getElementById('modal-email').innerText;
    document.getElementById('edit-modal-date').innerText = document.getElementById('modal-date').innerText;
    
    document.getElementById('editTicketModal').style.display = 'block';
}

document.getElementById('editBtn').addEventListener('click', openEditForm);

document.getElementById('editTicketForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const ticketId = document.getElementById('edit-modal-ticket-id').innerText;  // Get the ticket ID
    const formData = new FormData(event.target);
    formData.append('ticket_id', ticketId);  // Ensure ticket_id is included
    formData.append('action', 'update');     // Assuming 'update' is the intended action

    // Debugging: Log FormData contents
    for (var pair of formData.entries()) {
        console.log(pair[0]+ ', ' + pair[1]);
    }

    fetch('update_ticket.php', {
        method: 'POST',
        body: formData  // Send as FormData, not JSON
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        console.log('Success:', data);
        Swal.fire('Updated!', 'The ticket details have been updated successfully.', 'success');
        document.getElementById('editTicketModal').style.display = 'none';
    })
    .catch((error) => {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to update the ticket. Please try again.', 'error');
    });
});


// Function to close the edit modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Add event listener to close the edit modal when clicking on the close button
document.querySelector('#editTicketModal .close').addEventListener('click', function() {
    closeModal('editTicketModal');
});

window.addEventListener('click', function(event) {
    var modal = document.getElementById('editTicketModal');
    if (event.target === modal) {
        closeModal('editTicketModal');
    }
});

// Function to handle approve button click
function openApprovalForm() {
    // Show your form or modal here
    document.getElementById('approvalModal').style.display = 'block';
}

// Event listener for the Approve button
document.getElementById('approveBtn').addEventListener('click', openApprovalForm);

// Function to handle form submission
function submitApprovalForm() {
    var ticketId = document.getElementById('modal-ticket-id-input').value;
    var scheduleDate = document.getElementById('scheduleDate').value;

        // Check if the scheduleDate input is empty
        if (!scheduleDate) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select a schedule date before proceeding!'
        });
        return; // Stop the function if no date is selected
    }

    
    
    Swal.fire({
        title: 'Confirm Schedule Date',
        text: `You are setting the schedule to: ${scheduleDate}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, set it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_ticket.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    Swal.fire(
                        'Approved!',
                        'The ticket has been scheduled successfully.',
                        'success'
                    );
                }
            };
            xhr.send('ticket_id=' + encodeURIComponent(ticketId) + '&schedule_date=' + encodeURIComponent(scheduleDate) + '&action=approve');
            document.getElementById('approvalModal').style.display = 'none';
        }
    });
}

document.getElementById('approvalForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission behavior
    submitApprovalForm();
});


// Function to handle deny button click
function denyTicket() {
    var ticketId = document.getElementById('modal-ticket-id-input').value;

    // Show confirmation dialog using SweetAlert
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, deny it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // If confirmed, send AJAX request to deny the ticket
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_ticket.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Response:', xhr.responseText);
                    // Optionally, refresh the page or update the UI here
                }
            };
            xhr.send('action=deny&ticket_id=' + encodeURIComponent(ticketId));
        }
    });
}

// Set event listener for the deny button
document.getElementById('denyBtn').addEventListener('click', denyTicket);


// Function to open reschedule modal
function openRescheduleForm() {
    document.getElementById('rescheduleModal').style.display = 'block';
}

// Function to handle form submission for rescheduling
function submitRescheduleForm() {
    var ticketId = document.getElementById('modal-ticket-id-input').value;
    var newScheduleDate = document.getElementById('newScheduleDate').value;

    if (!newScheduleDate) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please select a new schedule date before proceeding!'
        });
        return;
    }

    Swal.fire({
        title: 'Confirm New Schedule Date',
        text: `You are setting the new schedule to: ${newScheduleDate}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, reschedule it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_ticket.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    Swal.fire(
                        'Rescheduled!',
                        'The ticket has been rescheduled successfully.',
                        'success'
                    );
                    document.getElementById('rescheduleModal').style.display = 'none';
                }
            };
            xhr.send('ticket_id=' + encodeURIComponent(ticketId) + '&new_schedule_date=' + encodeURIComponent(newScheduleDate) + '&action=reschedule');
        }
    });
}

document.getElementById('rescheduleBtn').addEventListener('click', openRescheduleForm);
document.getElementById('rescheduleForm').addEventListener('submit', function(event) {
    event.preventDefault();
    submitRescheduleForm();
});


// Function to handle mark as closed button click
function markAsClosed() {
    var ticketId = document.getElementById('modal-ticket-id-input').value;

    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to close the ticket. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, close it!'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_ticket.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    Swal.fire(
                        'Closed!',
                        'The ticket has been marked as closed successfully.',
                        'success'
                    );
                    // Optionally, refresh the page or update the UI here
                }
            };
            xhr.send('action=close&ticket_id=' + encodeURIComponent(ticketId));
        }
    });
}

// Add event listener for the Mark as Closed button
document.getElementById('markAsClosedBtn').addEventListener('click', markAsClosed);


    // Show modal when clicking on the button
    document.getElementById('showModalBtn').addEventListener('click', function () {
        showModal();
    });

    // Hide modal when clicking on the close button
    closeButton.addEventListener('click', function () {
        hideModal();
    });

    // Hide modal when clicking outside of it
    window.onclick = function (event) {
        if (event.target == modal) {
            hideModal();
        }
    };
});

function redirectToTicketPage() {
        window.location.href = 'admin-request-ticket.php';
    }

</script>




