<?php 

include 'src/includes/header.php';
  
 include 'src/includes/sidenav.php';
 require_once 'db.php'; 
 
 
// ------ for department ------ //
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $deptQuery = $conn->query("SELECT department_name FROM Department GROUP BY department_name ORDER BY department_name");
    $departments = $deptQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

// ------ for category ------ //
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $categoryQuery = $conn->query("SELECT category_name FROM Category GROUP BY category_name ORDER BY category_name");
    $categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
 ?>

  <div id="layoutSidenav_content">
        <main>
<body>
    <h2 class="user-report-heading">Repair and Service Request Form</h2>

    <form action="process_request.php" method="post" class="user-report-form">

            <?php 
            // Generate today's date in YYYY-MM-DD format
                $todayDate = date('Y-m-d');
                $ticketStatus = "new";
            ?>

            <!-- Hidden date input -->
            <input type="hidden" id="date" name="date" value="<?php echo $todayDate; ?>" required class="user-report-input" readonly>
            <input type="hidden" id="ticket_status" name="ticket_status" value="<?php echo $ticketStatus; ?>" required class="user-report-input" readonly>
            <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" class="user-report-input">


        <div class="user-report-form-group">
            <label for="name" class="user-report-label">Request for:</label>
            <input type="text" id="name" name="name" required class="user-report-input">
        </div>

        <div class="user-report-form-group">
            <label for="contact" class="user-report-label">Contact</label>
            <input type="text" id="contact" name="contact" required class="user-report-input">
        </div>

                
        <div class="user-report-form-group">
            <label for="supervisor" class="user-report-label">Head/Supervisor:</label>
            <input type="text" id="supervisor" name="supervisor" required class="user-report-input">
        </div>
               

        <div class="user-report-form-group">
            <label for="department" class="user-report-label">Department</label>
            <select id="department" name="department" required class="user-report-select">
                <option value="">Select a Department</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= htmlspecialchars($dept['department_name']) ?>" <?= isset($_POST['department']) && $_POST['department'] == $dept['department_name'] ? 'selected' : '' ?>><?= htmlspecialchars($dept['department_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>


        <div class="user-report-form-group">
            <label for="category" class="user-report-label">Category</label>
            <select id="category" name="category" required class="user-report-select">
                <option value="">Select a Category</option>
                <?php foreach ($categories as $categ): ?>
                    <option value="<?= htmlspecialchars($categ['category_name']) ?>" <?= isset($_POST['category']) && $_POST['category'] == $categ['category_name'] ? 'selected' : '' ?>><?= htmlspecialchars($categ['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="user-report-form-group">
            <label for="location" class="user-report-label">Location of Work:</label>
            <input type="text" id="location" name="location" required class="user-report-input">
        </div>

        <div class="user-report-form-group">
            <label for="description" class="user-report-label">Description of Work Request:</label>
            <textarea id="description" name="description" rows="4" required class="user-report-textarea"></textarea>
        </div>

        <div class="user-report-radio-group">
            <span class="user-report-radio-label">Priority:</span>
            <label for="low" class="user-report-radio-label"><input type="radio" id="low" name="priority" value="low" required class="user-report-radio">Low</label>
            <label for="medium" class="user-report-radio-label"><input type="radio" id="medium" name="priority" value="medium" required class="user-report-radio">Medium</label>
            <label for="high" class="user-report-radio-label"><input type="radio" id="high" name="priority" value="high" required class="user-report-radio">High</label>
        </div>

        <button type="submit" class="user-report-submit">Submit Request</button>
    </form>

    </main>

    <?php include 'src/includes/footer.php';?>