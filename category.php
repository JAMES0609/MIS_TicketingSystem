<?php 
include 'src/includes/header.php'; 
include 'src/includes/sidenav.php'; 
require_once 'db.php'; // Ensures the database connection is established

// SQL query to fetch data from the Category table
$sql = "SELECT category_id, category_name FROM Category WHERE category_status = 'active'";
$result = $conn->query($sql);
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container mt-4">
            <div class="actions-with-search">
                <div class="btn-container" role="group" aria-label="Category actions">
                    <button type="button" class="btn-add-category btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">+ Add Category</button>
                    <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search Categories...">
                </div>
            </div>
            <div class="category-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="th-itm" scope="col">Category ID</th>
                            <th class="th-itm" scope="col">Category Name</th>
                            <th class="th-itm" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>            
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="td-items"><?php echo $row['category_id']; ?></td>
                                    <td class="td-items"><?php echo htmlspecialchars($row['category_name']); ?></td>
                                    <td class="td-items">
                                        <button type="button" class="btn btn-primary btn-sm edit-btn" onclick="openEditModal(<?php echo $row['category_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['category_name'])); ?>')">Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" onclick="confirmDelete(<?php echo $row['category_id']; ?>, '<?php echo $row['category_name']; ?>')">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No categories found</td></tr>
                        <?php endif; ?>
                    </tbody>                    
                </table>
            </div>
        </div>
    </main>
    <?php include 'src/includes/footer.php'; ?>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addCategoryForm">
          <div class="mb-3">
            <label for="categoryName" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="categoryName" required>
          </div>
          <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategory
Modal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <input type="hidden" id="editCategoryId">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="editCategoryName" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    var searchValue = this.value.toLowerCase();
    var tableRows = document.querySelectorAll("#layoutSidenav_content table tbody tr");

    tableRows.forEach(function(row) {
        var categoryName = row.cells[1].textContent.toLowerCase();
        if (categoryName.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Function to open edit modal with data
function openEditModal(categoryId, categoryName) {
    var modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    document.getElementById('editCategoryId').value = categoryId;
    document.getElementById('editCategoryName').value = categoryName;
    modal.show();
}

function confirmDelete(categoryId, categoryName) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You won't be able to revert this!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_category.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    Swal.fire(
                        'Deleted!',
                        'Your category has been deleted.',
                        'success'
                    ).then((result) => {
                        if (result.value) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: 'Failed to delete category. Please try again!',
                    });
                }
            };
            xhr.send("categoryId=" + encodeURIComponent(categoryId) + "&categoryName=" + encodeURIComponent(categoryName));
        }
    });
}

document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var categoryName = document.getElementById('categoryName').value;

    if(categoryName === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter the category name',
        });
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_category.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'New category added successfully!',
            }).then((result) => {
                if (result.value) {
                    $('#addCategoryModal').modal('hide');
                    location.reload();
                }
            });
            document.getElementById('categoryName').value = '';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Failed to add category. Please try again!',
            });
        }
    };
    xhr.send("categoryName=" + encodeURIComponent(categoryName));
});

document.getElementById('editCategoryForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    var categoryId = document.getElementById('editCategoryId').value;
    var categoryName = document.getElementById('editCategoryName').value;

    if(categoryName === '') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please enter the category name',
        });
        return;
    }

    var formData = "categoryId=" + encodeURIComponent(categoryId) +
                   "&categoryName=" + encodeURIComponent(categoryName);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_category.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Category updated successfully!',
            }).then((result) => {
                if (result.value) {
                    $('#editCategoryModal').modal('hide');
                    location.reload();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: 'Failed to update category. Please try again!',
            });
        }
    };
   
    xhr.send(formData);
});

</script>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>

<?php
require_once 'db.php'; // Ensure this path is correct

// Make sure you're receiving the expected POST data for updating category
if(isset($_POST['categoryId']) && isset($_POST['categoryName'])) {
    $categoryId = $_POST['categoryId'];
    $categoryName = $_POST['categoryName'];

    // Prepare an SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE Category SET category_name = ? WHERE category_id = ?");
    $stmt->bind_param("si", $categoryName, $categoryId); // 's' for string, 'i' for integer

    if($stmt->execute()) {
        echo "Category updated successfully.";
    } else {
        // Log error to PHP error log
        error_log("Error updating category: " . $stmt->error);
        http_response_code(500);
        echo "Failed to update category.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Invalid request data.";
}

$conn->close();
?>
