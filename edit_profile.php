<?php

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];

/* Get user data */
$result = $conn->query("SELECT * FROM users WHERE id = $uid");

if (!$result || $result->num_rows == 0) {
    die("User not found");
}

$user = $result->fetch_assoc();

/* Update profile */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];

    $sql = "UPDATE users 
            SET name='$name',
                email='$email',
                mobile='$mobile',
                address='$address'
            WHERE id=$uid";

    if ($conn->query($sql)) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Edit Profile</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            width: 450px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
            color: #4b3f99;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            height: 80px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #4b3f99;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>

</head>

<body>

<div class="container">

    <h2>✏️ Edit Profile</h2>

    <form method="POST">

        <label>Name</label>
        <input type="text" name="name"
               value="<?php echo htmlspecialchars($user['name']); ?>"
               required>

        <label>Email</label>
        <input type="email" name="email"
               value="<?php echo htmlspecialchars($user['email']); ?>"
               required>

        <label>Mobile</label>
        <input type="text" name="mobile"
               value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>"
               required>

        <label>Address</label>
        <textarea name="address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>

        <button type="submit">
            💾 Save Changes
        </button>

    </form>

    <a class="back" href="profile.php">
        ← Back to Profile
    </a>

</div>

</body>
</html>