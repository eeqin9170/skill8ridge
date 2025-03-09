<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM jobs WHERE job_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: jobs.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
