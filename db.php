<?php
$host = "localhost";
$user = "root";  // Change if using a different user
$pass = "";      // Add password if set
$dbname = "skillbridge";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>