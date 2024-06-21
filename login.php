<?php include 'db.php';

$message = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = ($_POST['username']);
        $pass = ($_POST['password']);

        $stmt = $conn->prepare("SELECT id, password, role, email, name, contact_number, department FROM users WHERE username = :username");
        $stmt->bindParam(':username', $user);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['loggedin'] = true;
                
                $message = "success";

                $_SESSION['email'] = $row['email'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['contact'] = $row['contact_number'];
                $_SESSION['department'] = $row['department'];
          
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "User not found.";
        }
    }
} catch(PDOException $e) {
    $message = "Connection failed: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
</head>

<body>
<div class="Login-Form">
    <form action="login.php" method="post">
      <div class="imgcontainer">
        <img src="src/images/MIS_Logo.png" alt="Avatar" class="avatar">
      </div>

      <div class="container">
        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required>

        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required>
        
        <button type="submit">Login</button>

        <div class="center-content">
            <span class="fpsw"><a href="#">Forgot password?</a></span>
        </div>
        <br>
        <div class="center-content">
            <label>
              <input type="checkbox" checked="checked" name="remember"> Remember me
            </label>
        </div>
      </div>    
    </form>
</div>
</body>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var message = "<?php echo $message; ?>";
    if (message.length > 0) {
        if (message === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Logged in successfully!',
                text: 'Redirecting...'
            }).then((result) => {
                window.location.href = "<?php echo ($_SESSION['role'] === 'admin' ? 'index.php' : 'user-index.php'); ?>";
            });
        }
         else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message
            });
        }
    }
});
</script>

</html>
