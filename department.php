<?php 
include 'src/includes/header.php'; 
include 'src/includes/sidenav.php'; 
require_once 'db.php'; // Ensures the database connection is established

// SQL query to fetch data from the Department table
$sql = "SELECT department_Id, department_name FROM Department WHERE department_status = 'active'";
$result = $conn->query($sql);

?>

<div id="layoutSidenav_content">
    <main>
        <div class="container mt-4">
            <div class="actions-with-search">
                <div class="btn-container" role="group" aria-label="Department actions">
                    <button type="button" class="btn-add-department btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">+ Department</button>
                    <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search Departments...">
                </div>
            </div>
            <div class="department-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="th-itm" scope="col">Department ID</th>
                            <th class="th-itm" scope="col">Department Name</th>
                            <th class="th-itm" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>            
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="td-items "><?php echo $row['department_Id']; ?></td>
                                    <td class="td-items "><?php echo htmlspecialchars($row['department_name']); ?></td>
                                    <td class="td-items ">
                                        <button type="button" class="btn btn-primary btn-sm view-btn" onclick="openEditModal(<?php echo $row['department_Id']; ?>, '<?php echo addslashes(htmlspecialchars($row['department_name'])); ?>')">Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" onclick="confirmDelete(<?php echo $row['department_Id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No departments found</td></tr>
                        <?php endif; ?>
                    </tbody>                    
                </table>
            </div>
        </div>
    </main>
    <?php include 'src/includes/footer.php'; ?>
</div>


<!-- Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDepartmentModalLabel">Add New Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addDepartmentForm">
          <div class="mb-3">
            <label for="departmentName" class="form-label">Department Name</label>
            <input type="text" class="form-control" id="departmentName" required>
          </div>
          <button type="submit" class="btn btn-primary">Add Department</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDepartmentForm">
                    <input type="hidden" id="editDepartmentId">
                    <div class="mb-3">
                        <label for="departmentName" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="editDepartmentName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(departmentId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you really want to delete this department?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_department.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Department deleted successfully!',
                    }).then((result) => {
                        if (result.value) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Failed to delete department. Please try again!',
                    });
                }
            };
            xhr.send("departmentId=" + encodeURIComponent(departmentId));
        }
    });
}

document.getElementById('addDepartmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var departmentName = document.getElementById('departmentName').value;

    if(departmentName === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter the department name',
        });
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_department.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'New department added successfully!',
            }).then((result) => {
                if (result.value) {
                    $('#addDepartmentModal').modal('hide');
                    location.reload();
                }
            });
            document.getElementById('departmentName').value = '';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Failed to add department. Please try again!',
            });
        }
    };
    xhr.send("departmentName=" + encodeURIComponent(departmentName));
});

document.getElementById('searchInput').addEventListener('keyup', function() {
    var searchValue = this.value.toLowerCase();
    var tableRows = document.querySelectorAll("#layoutSidenav_content table tbody tr");

    tableRows.forEach(function(row) {
        var departmentName = row.cells[1].textContent.toLowerCase();
        if (departmentName.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Function to open edit modal with data
function openEditModal(departmentId, departmentName) {
    var modal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
    document.getElementById('editDepartmentId').value = departmentId;
    document.getElementById('editDepartmentName').value = departmentName;
    modal.show();
}

document.getElementById('editDepartmentForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    var departmentId = document.getElementById('editDepartmentId').value;
    var departmentName = document.getElementById('editDepartmentName').value;

    if(departmentName === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter the department name',
        });
        return;
    }

    // Prepare the data to be sent in a key-value pair format
    var formData = "departmentId=" + encodeURIComponent(departmentId) +
                   "&departmentName=" + encodeURIComponent(departmentName);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_department.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Department updated successfully!',
            }).then((result) => {
                if (result.value) {
                    $('#editDepartmentModal').modal('hide');
                    location.reload(); // Reload the page to see the changes
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Failed to update department. Please try again!',
            });
        }
    };
    xhr.send(formData);

    // Hide the modal using Bootstrap's JavaScript functions
    var modal = bootstrap.Modal.getInstance(document.getElementById('editDepartmentModal'));
    modal.hide();
});
</script>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
