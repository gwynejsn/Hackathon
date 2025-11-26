<?php
session_start();
require_once "../src/helpers/db.php"; // DB connection

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $password_hash);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $password_hash)) {
                $_SESSION["user_id"] = $user_id;
                $_SESSION["email"] = $email;

                header("Location: index.php");
                exit();
            } else {
                $error = "Incorrect email or password.";
            }
        } else {
            $error = "Incorrect email or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Whistleblower Portal</title>
    <link rel="stylesheet" href="css/style.css"> <!-- optional -->
</head>
<body>

<div class="login-container">
    <h2>Employee Login</h2>

    <?php if (!empty($error)) : ?>
        <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <p>
        Or <a href="submit_anonymous.php">Submit an Anonymous Report</a>
    </p>

</div>

</body>
</html>
