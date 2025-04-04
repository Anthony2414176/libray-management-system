<?php
session_start();
include "app/database/connect.php"; // connect database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // To check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        // To hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // To insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            header("Location: index.php"); // To redirect to login page
            exit();
        } else {
            $error = "Registration failed. Try again.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Login</title>
</head>
<body class="login">
    <div class="login-form">
        <h1>Library Login</h1>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form id="login-form" action="index.php" method="post">
            <div class="form-item">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-item">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-item">
                <label for="role">Role</label>
                <select name="role" required>
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div><a href="reset_password.php">Forgot password?</a></div>
            <div>
                <button type="submit">Login</button>
            </div>
        </form>
        
        <p id="no_password">Don't have an account? <a href="register.php">Register</a></p>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>