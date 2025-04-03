<?php
// Database credentials
$servername = "192.168.11.208";
$username = "shreyash";
$password = "Pass@123";
$dbname = "mydb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Validate form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $website = mysqli_real_escape_string($conn, $_POST["website"]);
    $comment = mysqli_real_escape_string($conn, $_POST["comment"]);
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);

    // Check if required fields are empty
    if (empty($name) || empty($email) || empty($gender)) {
        die("Error: Name, Email, and Gender are required fields.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }

    // Prepare SQL statement
    $sql = "INSERT INTO student (name, email, website, comment, gender) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $website, $comment, $gender);
        if (mysqli_stmt_execute($stmt)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }
	mysqli_stmt_close($stmt);
    } else {
	echo "Error: " . mysqli_error($conn);
    }
}

// Close connection
mysqli_close($conn);
?>



