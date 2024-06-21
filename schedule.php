<?php include 'src/includes/header.php'; ?>
  
<?php include 'src/includes/sidenav.php'; ?>

<div id="layoutSidenav_content">
    <main>
   
        <div class="schedule-container">
            <div class="schedule-scrollable">
                <?php
                // Include your database connection file here
                require_once 'db.php'; 

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // SQL query to select records with ticket status "approved"
                $sql = "SELECT * FROM service_requests WHERE ticket_status = 'approved'";
                $result = $conn->query($sql);

                // Check if there are results
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        // Convert date to desired format
                        $formatted_date = date("F\nj\nY", strtotime($row["schedule"]));

                        // Explode formatted date to separate month, day, and year
                        $date_parts = explode("\n", $formatted_date);

                        echo "<div class='schedule-item'>";
                        echo "<div class='date-info'>";
                        echo "<div class='month'>" . $date_parts[0] . "</div>";
                        echo "<div class='day day-large'>" . $date_parts[1] . "</div>";
                        echo "<div class='year'>" . $date_parts[2] . "</div>";
                        echo "</div>";
                        echo "<div class='item-info' style='width: 50%;'>"; 
                        echo "<div class='ticket-info'>";
                        echo "<h4>Ticket#" . $row["request_id"] . "</h4>"; 
                        echo "<div class='description-container'>";
                        echo "<p>Description: " . wordwrap($row["description"], 50, "<br>") . "</p>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";     
                        echo "<div class='image-container'>";
                        echo "</div>";
                        echo "</div>";
                        
                    }
                } else {
                    echo "0 results";
                }
                $conn->close();
                ?>

            </div>
        </div>

    </main>

    <?php include 'src/includes/footer.php'; ?>
</div>


