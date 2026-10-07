<?php
if(session_status()==
PHP_SESSION_NONE){
    session_start();
}
require 'config.php';

$user_id = $_SESSION['user_id'] ?? 0;

$medicines = [];

if ($user_id > 0) {

    $stmt = $conn->prepare("
        SELECT medicine_name, reminder_time
        FROM medicines
        WHERE user_id = ?
        AND start_date <= CURDATE()
        AND (end_date IS NULL OR end_date >= CURDATE())
        ORDER BY reminder_time
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $medicines[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        h1 {
            text-align: center;
        }

        .notification {
            background: white;
            padding: 20px;
            margin: 15px auto;
            max-width: 700px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        }

        .notification h3 {
            margin: 0 0 8px;
        }

        .notification p {
            margin: 5px 0;
        }

        .time {
            font-size: 13px;
            color: #777;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 25px;
        }

        .medicine {
            border-left: 5px solid #4caf50;
        }
    </style>
</head>

<body>

<h1>🔔 Notifications</h1>


<!-- Medicine Notifications -->

<?php foreach ($medicines as $medicine): ?>

    <div class="notification medicine">

        <h3>💊 Medicine Reminder</h3>

        <p>
            It is time to take
            <strong>
                <?php echo htmlspecialchars($medicine['medicine_name']); ?>
            </strong>.
        </p>

        <div class="time">
            Reminder Time:
            <?php echo date("h:i A", strtotime($medicine['reminder_time'])); ?>
        </div>

    </div>

<?php endforeach; ?>


<!-- Other Notifications -->

<div class="notification">
    <h3>📅 Upcoming Appointment</h3>
    <p>You have an upcoming doctor appointment.</p>
    <div class="time">Today</div>
</div>


<div class="notification">
    <h3>⏳ Pending Care Task</h3>
    <p>You have a pending care task to complete.</p>
    <div class="time">Today</div>
</div>


<div class="notification">
    <h3>💊 Medicine Pending</h3>
    <p>A medicine has not been marked as taken.</p>
    <div class="time">Today</div>
</div>


<a class="back" href="dashboard.php">
    ← Back to Dashboard
</a>


<script>

    // Browser notification permission
    if ("Notification" in window) {

        if (Notification.permission === "default") {
            Notification.requestPermission();
        }
    }


    // Check medicine reminder time
    function checkMedicineReminder() {

        const now = new Date();

        const currentTime =
            now.getHours().toString().padStart(2, '0') +
            ":" +
            now.getMinutes().toString().padStart(2, '0');


        <?php foreach ($medicines as $medicine): ?>

            const medicineTime =
                "<?php echo date('H:i', strtotime($medicine['reminder_time'])); ?>";

            const medicineName =
                "<?php echo addslashes($medicine['medicine_name']); ?>";


            if (currentTime === medicineTime) {

                const notificationKey =
                    "medicine_" + medicineName + "_" + currentTime;

                if (!sessionStorage.getItem(notificationKey)) {

                    if ("Notification" in window &&
                        Notification.permission === "granted") {

                        new Notification("💊 Medicine Reminder", {
                            body: "It is time to take " + medicineName
                        });
                    }

                    sessionStorage.setItem(notificationKey, "shown");
                }
            }

        <?php endforeach; ?>
    }


    // Check every 30 seconds
    setInterval(checkMedicineReminder, 30000);

    // Check immediately
    checkMedicineReminder();

</script>

</body>
</html>