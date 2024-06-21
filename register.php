<?php

 require_once 'db.php';

// Create a PDO connection outside the POST check to use it for fetching departments too
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch departments for the dropdown
    $deptQuery = $conn->query("SELECT department_name FROM Department GROUP BY department_name ORDER BY department_name");
    $departments = $deptQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $contact_number = $_POST['contact_number'] ?? null;
    $department = $_POST['department'] ?? null;
    $supervisor_head = $_POST['supervisor_head'] ?? null;
    $role = $_POST['role'];

    // Check if email already exists
    $emailCheck = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $emailCheck->bindParam(':email', $email);
    $emailCheck->execute();

    if ($emailCheck->rowCount() > 0) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'This email is already registered. Please use a different email.',
                });
              </script>";
    } else {
        try {
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password, email, name, contact_number, department, supervisor_head, role, status) VALUES (:username, :password, :email, :name, :contact_number, :department, :supervisor_head, :role, 'active')");
            $stmt->bindParam(':username', $user);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':department', $department);
            $stmt->bindParam(':supervisor_head', $supervisor_head);
            $stmt->bindParam(':role', $role);

            $stmt->execute();

            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Registration successful. You can now login.',
                    }).then(function() {
                        window.location.href = 'login.php';
                    });
                  </script>";
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An account with this username or email already exists. Please choose a different one.',
                        });
                      </script>";
            } else {
                echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error: " . $e->getMessage() . "',
                        });
                      </script>";
            }
        }
    }
}
?>


    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .Login-Form {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }

        .container {
            display: flex;
            flex-direction: column;
        }

        .container label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .container input[type="text"],
        .container input[type="password"],
        .container input[type="email"],
        .container select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .container button {
            background-color: green;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .container button:hover {
            background-color: darkgreen !important;
        }

        @media (max-width: 600px) {
            .Login-Form {
                width: 90%;
                padding: 10px;
            }

            .container button {
                padding: 12px;
            }
        }
    </style>
<?php include 'src/includes/header.php'; ?>
<?php include 'src/includes/sidenav.php'; ?>

<div id="layoutSidenav_content">
    <main>
<div class="Login-Form">
    <form action="register.php" method="post">
      <div class="container">
        <label for="username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="username" required>

        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required>

        <label for="email"><b>Email</b></label>
        <input type="email" placeholder="Enter Email" name="email" required>

        <label for="name"><b>Name</b></label>
        <input type="text" placeholder="Enter Full Name" name="name" required>

        <label for="contact_number"><b>Contact Number</b></label>
        <input type="text" placeholder="Enter Contact Number" name="contact_number">

        <label for="department"><b>Department</b></label>
        <select name="department">
            <option value="">Select a Department</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= htmlspecialchars($dept['department_name']) ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="supervisor_head"><b>Supervisor/Head</b></label>
        <input type="text" placeholder="Enter Supervisor/Head Name" name="supervisor_head">

        <label for="role"><b>Role</b></label>
        <select name="role" required>
          <option value="Admin">Admin</option>
          <option value="User" selected>User</option>
        </select>

        <button type="submit">Register</button>
      </div>    
    </form>
</div>

</main>

<?php include 'src/includes/footer.php'; ?>
</div>