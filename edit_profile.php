<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = $conn->real_escape_string($_POST['bio']);
    $skills = $conn->real_escape_string($_POST['skills']);

    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

        $sql = "UPDATE users SET bio='$bio', skills='$skills', profile_picture='" . $_FILES["profile_picture"]["name"] . "' WHERE id=$user_id";
    } else {
        $sql = "UPDATE users SET bio='$bio', skills='$skills' WHERE id=$user_id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "Profile updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Bio:</label>
            <textarea name="bio" class="form-control"><?php echo $user['bio']; ?></textarea>

            <label>Skills (comma separated):</label>
            <input type="text" name="skills" class="form-control" value="<?php echo $user['skills']; ?>">

            <label>Profile Picture:</label>
            <input type="file" name="profile_picture" class="form-control">
            
            <button type="submit" class="btn btn-success mt-3">Save Changes</button>
        </form>
    </div>
</body>
</html>
