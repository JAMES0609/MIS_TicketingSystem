<?php
include 'db.php';

if(isset($_POST['departmentName'])) {
    $departmentName = $conn->real_escape_string($_POST['departmentName']);
    $sql = "INSERT INTO Department (department_name, department_status) VALUES ('$departmentName', 'active')";

    if($conn->query($sql) === TRUE) {
        echo "New department added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>
