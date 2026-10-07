<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email, mobile, address FROM users WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User information not found.");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-card {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        .profile-icon {
            text-align: center;
            font-size: 70px;
            margin-bottom: 20px;
        }

        .profile-info {
            margin: 15px 0;
            padding: 12px;
            background: #f8f8f8;
            border-radius: 8px;
        }

        .profile-info strong {
            display: inline-block;
            width: 150px;
        }

        .edit-btn {
            display: block;
            width: 150px;
            margin: 25px auto 10px;
            padding: 10px;
            text-align: center;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<h1>👤 My Profile</h1>

<div class="profile-card">

    <div class="profile-icon">👤</div>

    <div class="profile-info">
        <strong>Name:</strong>
        <?php echo htmlspecialchars($user['name']); ?>
    </div>

    <div class="profile-info">
        <strong>Email:</strong>
        <?php echo htmlspecialchars($user['email']); ?>
    </div>

    <div class="profile-info">
        <strong>Mobile:</strong>
        <?php echo htmlspecialchars($user['mobile']); ?>
    </div>

    <div class="profile-info">
        <strong>Role:</strong>
        Family Member
    </div>

    <div class="profile-info">
        <strong>Address:</strong>
        <?php echo htmlspecialchars($user['address']); ?>
    </div>

</div>

<a href="edit_profile.php" class="edit-btn">✏️ Edit Profile</a>

<a class="back" href="dashboard.php">← Back to Dashboard</a>

</body>
</html>