<?php
session_start();
require_once 'app/database/connect.php';

$message = "";

try {
    // Ensure the database connection is established
    if (!isset($conn)) {
        throw new Exception("Database connection not established.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $new_password = $_POST['new_password'];

        if (!empty($email) && !empty($new_password)) {
            // Check if the email exists in the database
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                // Update the user's password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);
                $stmt->execute();

                $message = "Your password has been updated successfully.";
            } else {
                $message = "No account found with that email address.";
            }
        } else {
            $message = "All fields are required.";
        }
    }
} catch (Exception $e) {
    $message = "An error occurred: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/css/styles.css">

</head>
<body>
    <div class="login-form">
        <h2>Reset Password</h2>
        <?php if ($message): ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required>

            <button type="submit">Reset Password</button>
        </form>
        <p><a href="index.php"> Login</a></p>
    </div>
</body>
</html>