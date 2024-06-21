<?php 

include 'src/includes/user-header.php';
  
 include 'src/includes/user-sidenav.php';
 require_once 'db.php'; 
 
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

                <input type="hidden" id="contact" name="contact" value="<?php echo htmlspecialchars($_SESSION['contact']); ?>" class="user-report-input">
                <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" class="user-report-input">
                <input type="hidden" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" class="user-report-input">
                <input type="hidden" class="user-report-form-group" id="department" name="department" value="<?php echo htmlspecialchars($_SESSION['department']); ?>" class="user-report-input">


        <div class="user-report-form-group">
            <label for="supervisor" class="user-report-label">Head/Supervisor:</label>
            <input type="text" id="supervisor" name="supervisor" required class="user-report-input">
        </div>
                    
                   

        <div class="user-report-form-group">
            <label for="category" class="user-report-label">Category:</label>
            <select id="category" name="category" required class="user-report-select">
                <option value="">Please select</option>
                <option value="Network">Network</option>
                <option value="Computer">Computer</option>
                <option value="System">System</option>
                <option value="Others">Others</option>
        
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