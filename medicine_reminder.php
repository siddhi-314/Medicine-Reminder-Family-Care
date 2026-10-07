<?php
require 'config.php';

$result = $conn->query("SELECT * FROM medicines ORDER BY reminder_time ASC");

if (!$result) {
    die("Database Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medicine Reminder</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f7fa;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        h1 {
            text-align: center;
        }

        .medicine-card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ccc;
        }

        .medicine-card h2 {
            margin-top: 0;
        }

        .time {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>💊 Medicine Reminder</h1>

    <?php if ($result->num_rows > 0): ?>

        <?php while ($row = $result->fetch_assoc()): ?>

            <div class="medicine-card medicine-reminder"
                 data-medicine="<?php echo htmlspecialchars($row['medicine_name']); ?>"
                 data-time="<?php echo substr($row['reminder_time'], 0, 5); ?>">

                <h2>
                    💊 <?php echo htmlspecialchars($row['medicine_name']); ?>
                </h2>

                <p class="time">
                    ⏰ Reminder Time:
                    <?php echo date("h:i A", strtotime($row['reminder_time'])); ?>
                </p>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p style="text-align:center;">No medicines found.</p>

    <?php endif; ?>

</div>

<script>
if ("Notification" in window) {

    if (Notification.permission !== "granted") {
        Notification.requestPermission();
    }

    setInterval(function () {

        const now = new Date();

        const currentTime =
            String(now.getHours()).padStart(2, '0') + ":" +
            String(now.getMinutes()).padStart(2, '0');

        document.querySelectorAll(".medicine-reminder").forEach(function(item) {

            const medicine = item.dataset.medicine;
            const reminderTime = item.dataset.time;

            if (currentTime === reminderTime) {

                new Notification("💊 Medicine Reminder", {
                    body: medicine + " घेण्याची वेळ झाली आहे."
                });

            }

        });

    }, 30000);
}
</script>

</body>
</html>