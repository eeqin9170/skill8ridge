<?php
session_start();
include 'db.php'; // Include your database connection file

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
if (isset($_POST['submit'])) {
    $user_id = $_GET['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Password update (if provided)
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash the new password
    } else {
        // Retain the original password if none provided
        $query = "SELECT password FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $password = $user['password'];
    }

    // Update the user info in the database
    $query = "UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $username, $email, $password, $role, $user_id);

    if ($stmt->execute()) {
        echo "User updated successfully!";
    } else {
        echo "Error updating user: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST" action="edit_user.php?id=<?php echo $user['id']; ?>">
    <label for="username">Username:</label>
    <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
    
    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
    
    <label for="password">Password:</label>
    <input type="password" name="password" placeholder="Leave blank to keep the same">
    
    <label for="role">Role:</label>
    <select name="role" required>
        <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
    </select>
    
    <button type="submit" name="submit">Update User</button>
</form>
</body>
</html>