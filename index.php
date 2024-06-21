<?php 
include 'src/includes/header.php'; 
include 'src/includes/sidenav.php'; 
require_once 'db.php'; // This should establish your database connection

// Get the selected year from the form or set a default year
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

// Query to count Pending tickets for the selected year
$pendingQuery = "SELECT COUNT(*) AS count FROM service_requests WHERE ticket_status IN ('new', 'open', 'approved','pending') AND YEAR(`date`) = $selectedYear";
$pendingResult = mysqli_query($conn, $pendingQuery);
$pendingRow = mysqli_fetch_assoc($pendingResult);
$pendingCount = $pendingRow['count'];

// Query to count Solved tickets for the selected year
$solvedQuery = "SELECT COUNT(*) AS count FROM service_requests WHERE ticket_status = 'closed' AND YEAR(`date`) = $selectedYear";
$solvedResult = mysqli_query($conn, $solvedQuery);
$solvedRow = mysqli_fetch_assoc($solvedResult);
$solvedCount = $solvedRow['count'];

// Query to count Denied tickets for the selected year
$deniedQuery = "SELECT COUNT(*) AS count FROM service_requests WHERE ticket_status = 'denied' AND YEAR(`date`) = $selectedYear";
$deniedResult = mysqli_query($conn, $deniedQuery);
$deniedRow = mysqli_fetch_assoc($deniedResult);
$deniedCount = $deniedRow['count'];

// Query to count All tickets for the selected year
$allTicketsQuery = "SELECT COUNT(*) AS count FROM service_requests WHERE YEAR(`date`) = $selectedYear";
$allTicketsResult = mysqli_query($conn, $allTicketsQuery);
$allTicketsRow = mysqli_fetch_assoc($allTicketsResult);
$allTicketsCount = $allTicketsRow['count'];

// Create an array with all the ticket counts for the selected year
$ticketCounts = [
    'Pending' => $pendingCount,
    'Solved' => $solvedCount,
    'Denied' => $deniedCount,
    'All Tickets' => $allTicketsCount,
];

// Query to count tickets per month for the selected year
$monthlyCountsQuery = "SELECT COUNT(*) AS count, MONTH(`date`) AS month FROM service_requests WHERE YEAR(`date`) = $selectedYear GROUP BY MONTH(`date`)";
$monthlyCountsResult = mysqli_query($conn, $monthlyCountsQuery);

// Initialize an array to store monthly ticket counts
$ticketCountsMonthly = [];

// Loop through the result and populate the array
while ($row = mysqli_fetch_assoc($monthlyCountsResult)) {
    $month = date("F", mktime(0, 0, 0, $row['month'], 1));
    $ticketCountsMonthly[$month] = $row['count'];
}
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Dashboard</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Ticket</li>
            </ol>
            <!-- Year Filter -->
            <form method="post" class="mb-4">
                <div class="form-group">
                    <label for="year">Select Year:</label>
                    <select class="form-control" id="year" name="year" onchange="this.form.submit()">
                        <?php for ($i = 2020; $i <= date('Y'); $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($i == $selectedYear) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </form>
            
            <!-- Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            Pending
                            <span class="small text-white" style="float: right; font-size: 18px;"><?php echo $ticketCounts['Pending']; ?></span>
                        </div>
                        <div class="card-footer">
                            <a class="small text-white stretched-link" href="tickets.php">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            Solved
                            <span class="small text-white" style="float: right; font-size: 18px;"><?php echo $ticketCounts['Solved']; ?></span>
                        </div>
                        <div class="card-footer">
                            <a class="small text-white stretched-link" href="tickets.php">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">
                            Denied
                            <span class="small text-white" style="float: right; font-size: 18px;"><?php echo $ticketCounts['Denied']; ?></span>
                        </div>
                        <div class="card-footer">
                            <a class="small text-white stretched-link" href="tickets.php">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            All Tickets
                            <span class="small text-white" style="float: right; font-size: 18px;"><?php echo $ticketCounts['All Tickets']; ?></span>
                        </div>
                        <div class="card-footer">
                            <a class="small text-white stretched-link" href="tickets.php">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bar Chart -->
            <div class="overflow-auto">
                <canvas id="ticketBarChart" width="800" height="300"></canvas>
            </div>

            <script>
                const ticketCounts = <?php echo json_encode(array_values($ticketCountsMonthly)); ?>;
                const months = <?php echo json_encode(array_keys($ticketCountsMonthly)); ?>;

                const ctx = document.getElementById('ticketBarChart').getContext('2d');
                const ticketBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Ticket Counts',
                            data: ticketCounts,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
    </main>
    <?php include 'src/includes/footer.php'; ?>
</div>
