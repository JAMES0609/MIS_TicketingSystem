<?php include 'src/includes/user-header.php'; ?>
<?php include 'src/includes/user-sidenav.php'; ?>

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

                // SQL query to select records with ticket status "approved" and order them by schedule date
                $sql = "SELECT * FROM service_requests WHERE ticket_status = 'approved' ORDER BY schedule ASC";
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
                        echo "<div class='item-info'>";
                        echo "<div class='ticket-info'>";
                        echo "<h4>Ticket#" . $row["request_id"] . "</h4>";
                        echo "<div class='description-container'>";
                        echo "<p>Description: " . wordwrap($row["description"], 50, "<br>") . "</p>";
                        echo "</div>";
                        echo "</div>";
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

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .schedule-container {
        display: flex;
        justify-content: center;
        padding: 20px;
    }

    .schedule-scrollable {
        width: 100%;
        max-width: 1200px;
    }

    .schedule-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        margin-bottom: 20px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .date-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-weight: bold;
    }

    .date-info .month {
        font-size: 16px;
        color: #555;
    }

    .date-info .day {
        font-size: 24px;
        color: #000;
    }

    .date-info .year {
        font-size: 14px;
        color: #888;
    }

    .item-info {
        flex: 1;
        margin-left: 20px;
        margin-right: 20px;
    }

    .ticket-info h4 {
        margin: 0 0 10px 0;
    }

    .description-container p {
        margin: 0;
        white-space: pre-wrap;
        word-break: break-word;
    }

    @media (max-width: 768px) {
        .schedule-item {
            flex-direction: column;
            text-align: center;
        }

        .item-info {
            margin: 20px 0;
        }
    }
</style>
