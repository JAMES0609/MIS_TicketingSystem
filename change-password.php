<?php
session_start();

require_once 'db.php'; 
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (!isset($_SESSION['user_id'])) {
            exit('Please login to access this page.');
        }

        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $user_id = $_SESSION['user_id'];

        // Fetch the current password from the database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && password_verify($old_password, $user['password'])) {
            // Hash new password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in the database
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->execute([$new_password_hash, $user_id]);

            $message = 'Password updated successfully.';
        } else {
            $message = 'Old password is incorrect.';
        }
    } catch (PDOException $e) {
        $message = "DB connection failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>

    <style>

body {
    background-color: gray;
}

.ChangePassword-Form {
    width: 500px;
    height: auto;
    margin: auto;
    margin-top: 50px;
    background-color: white;
    border: 3px solid #f1f1f1;
    border-radius: 12px;
    padding: 20px;
}

.container {
    padding: 16px;
}

input[type="password"] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

button {
    background-color: #04AA6D;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
    border-radius: 5px;
}

button:hover {
    opacity: 0.8;
}

h1 {
    text-align: center;
}

.message {
    text-align: center;
    margin-top: 10px;
}

        </style>
</head>
<body>
<div class="ChangePassword-Form">
    <h1>Change Password</h1>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <form action="" method="post">
        <div class="container">
            <label for="old_password"><b>Old Password:</b></label>
            <input type="password" name="old_password" required><br>
            <label for="new_password"><b>New Password:</b></label>
            <input type="password" name="new_password" required><br>
            <button type="submit">Change Password</button>
        </div>
    </form>
</div>
</body>
</html>
