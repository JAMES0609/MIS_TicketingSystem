<?php 
include 'src/includes/header.php'; 
include 'src/includes/sidenav.php'; 
require_once 'db.php'; // Ensures the database connection is established

// SQL query to fetch active users from the users table
$sql = "SELECT id, username, role, email, name, contact_number, department, supervisor_head FROM users WHERE status = 'active'";
$result = $conn->query($sql);
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container mt-4">
            <div class="actions-with-search">
                <div class="btn-container" role="group" aria-label="User actions">
                    <button type="button" class="btn-add-user btn btn-primary" onclick="window.location.href='register.php'">+ Add User</button>
                    <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search Users...">
                </div>
            </div>
            <div class="user-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="th-itm" scope="col">ID</th>
                            <th class="th-itm" scope="col">Username</th>
                            <th class="th-itm" scope="col">Role</th>
                            <th class="th-itm" scope="col">Email</th>
                            <th class="th-itm" scope="col">Name</th>
                            <th class="th-itm" scope="col">Contact Number</th>
                            <th class="th-itm" scope="col">Department</th>
                            <th class="th-itm" scope="col">Supervisor Head</th>
                            <th class="th-itm" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="td-item"><?php echo $row['id']; ?></td>
                                    <td class="td-item"><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td class="td-item"><?php echo $row['role']; ?></td>
                                    <td class="td-item"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="td-item"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="td-item"><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                    <td class="td-item"><?php echo htmlspecialchars($row['department']); ?></td>
                                    <td class="td-item"><?php echo htmlspecialchars($row['supervisor_head']); ?></td>
                                    <td class="td-item">
                                        <button type="button" class="btn btn-primary btn-sm view-btn" onclick="openViewModal(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['username'])); ?>', '<?php echo htmlspecialchars($row['role']); ?>', '<?php echo addslashes(htmlspecialchars($row['email'])); ?>', '<?php echo addslashes(htmlspecialchars($row['name'])); ?>', '<?php echo addslashes(htmlspecialchars($row['contact_number'])); ?>', '<?php echo addslashes(htmlspecialchars($row['department'])); ?>', '<?php echo addslashes(htmlspecialchars($row['supervisor_head'])); ?>')">View</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9">No users found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include 'src/includes/footer.php'; ?>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">Edit User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateUserForm">
                    <input type="hidden" id="userId" value="">
                    <div><strong>ID:</strong> <span id="viewUserId"></span></div>
                    <div><strong>Username:</strong> <input type="text" id="viewUsername" class="form-control"></div>
                    <div><strong>Role:</strong> <input type="text" id="viewRole" class="form-control"></div>
                    <div><strong>Email:</strong> <input type="email" id="viewEmail" class="form-control"></div>
                    <div><strong>Name:</strong> <input type="text" id="viewName" class="form-control"></div>
                    <div><strong>Contact Number:</strong> <input type="text" id="viewContactNumber" class="form-control"></div>
                    <div><strong>Department:</strong> <input type="text" id="viewDepartment" class="form-control"></div>
                    <div><strong>Supervisor Head:</strong> <input type="text" id="viewSupervisorHead" class="form-control"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="updateUserDetails()">Update</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="path_to_bootstrap_js/bootstrap.bundle.min.js"></script>

<script>
    function openViewModal(id, username, role, email, name, contact_number, department, supervisor_head) {
        document.getElementById('userId').value = id;
        document.getElementById('viewUserId').innerText = id;
        document.getElementById('viewUsername').value = username;
        document.getElementById('viewRole').value = role;
        document.getElementById('viewEmail').value = email;
        document.getElementById('viewName').value = name;
        document.getElementById('viewContactNumber').value = contact_number;
        document.getElementById('viewDepartment').value = department;
        document.getElementById('viewSupervisorHead').value = supervisor_head;

        var modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
        modal.show();
    }

    function updateUserDetails() {
        var id = document.getElementById('userId').value;
        var username = document.getElementById('viewUsername').value;
        var role = document.getElementById('viewRole').value;
        var email = document.getElementById('viewEmail').value;
        var name = document.getElementById('viewName').value;
        var contact_number = document.getElementById('viewContactNumber').value;
        var department = document.getElementById('viewDepartment').value;
        var supervisor_head = document.getElementById('viewSupervisorHead').value;

        // AJAX request to a PHP script that updates the database
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update-user.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status == 200) {
                alert("Update successful");
                location.reload();  // Reload the page to see the updates
            } else {
                alert("Error updating record");
            }
        };
        xhr.send("id=" + id + "&username=" + encodeURIComponent(username) + "&role=" + encodeURIComponent(role) +
                 "&email=" + encodeURIComponent(email) + "&name=" + encodeURIComponent(name) +
                 "&contact_number=" + encodeURIComponent(contact_number) + "&department=" + encodeURIComponent(department) +
                 "&supervisor_head=" + encodeURIComponent(supervisor_head));
    }

    function confirmDelete() {
        var id = document.getElementById('userId').value;
        if (confirm('Are you sure you want to delete this user?')) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete-user.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status == 200) {
                    alert("User deleted successfully");
                    location.reload();
                } else {
                    alert("Error deleting user");
                }
            };
            xhr.send("id=" + id);
        }
    }
</script>

<?php 
$conn->close(); // Close database connection
?>
