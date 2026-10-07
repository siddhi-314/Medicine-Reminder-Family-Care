<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT id, name, password FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Invalid email or password.";
        }

    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login - Medicine Reminder & Family Care</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="login card">

    <h2>💊 Medicine Reminder</h2>

    <h3>Family Care App</h3>

    <p class="muted">
        Login to manage your family reminders
    </p>

    <?php if ($error != ""): ?>

        <div class="notice error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button
            type="submit"
            class="btn"
            style="width:100%;"
        >
            Login
        </button>

    </form>

    <p style="margin-top:20px;">

        Don't have an account?

        <a href="register.php">
            Create Account
        </a>

    </p>

</div>

</body>

</html>