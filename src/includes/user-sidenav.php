<div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">User</div>
                            <a class="nav-link" href="user-index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>     
                            <a class="nav-link" href="user-request-ticket.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-ticket-alt"></i></div>
                                Request Tickets
                            </a>       
                          
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:  <?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : 'Unknown'; ?></div>
                    <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Unknown'; ?>
            </div>
                </nav>
                </div>