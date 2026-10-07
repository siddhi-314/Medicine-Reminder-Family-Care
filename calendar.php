<?php
require 'config.php';
require 'header.php';

$uid = $_SESSION['user_id'];

/* MEDICINE REMINDERS */
$medicines = [];

$stmt = $conn->prepare("
    SELECT 
        m.start_date,
        m.end_date,
        m.reminder_time,
        m.medicine_name,
        f.name AS member_name
    FROM medicines m
    JOIN family_members f ON f.id = m.member_id
    WHERE m.user_id = ?
");

$stmt->bind_param("i", $uid);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}


/* APPOINTMENTS */
$appointments = [];

$stmt = $conn->prepare("
    SELECT 
        a.appointment_date,
        a.appointment_time,
        a.doctor_name,
        f.name AS member_name
    FROM appointments a
    JOIN family_members f ON f.id = a.member_id
    WHERE a.user_id = ?
");

$stmt->bind_param("i", $uid);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}


/* CARE TASKS */
$tasks = [];

$stmt = $conn->prepare("
    SELECT 
        c.task_date,
        c.task_time,
        c.task_name,
        f.name AS member_name,
        c.status
    FROM care_tasks c
    JOIN family_members f ON f.id = c.member_id
    WHERE c.user_id = ?
");

$stmt->bind_param("i", $uid);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}
?>

<div class="container">

    <div class="hero">

        <h2>📅 Family Care Calendar</h2>

        <p class="muted">
            View medicines, appointments and care tasks by date.
        </p>

    </div>


    <!-- MEDICINE REMINDERS -->

    <div class="card">

        <h3>💊 Medicine Reminders</h3>

        <?php if (count($medicines) > 0): ?>

            <table>

                <tr>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Medicine</th>
                    <th>Time</th>
                </tr>

                <?php foreach ($medicines as $medicine): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($medicine['start_date']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($medicine['member_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($medicine['medicine_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($medicine['reminder_time']) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p class="muted">
                No medicine reminders available.
            </p>

        <?php endif; ?>

    </div>


    <!-- APPOINTMENTS -->

    <div class="card">

        <h3>🩺 Appointments</h3>

        <?php if (count($appointments) > 0): ?>

            <table>

                <tr>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Doctor / Clinic</th>
                    <th>Time</th>
                </tr>

                <?php foreach ($appointments as $appointment): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($appointment['appointment_date']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($appointment['member_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($appointment['doctor_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($appointment['appointment_time']) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p class="muted">
                No appointments available.
            </p>

        <?php endif; ?>

    </div>


    <!-- CARE TASKS -->

    <div class="card">

        <h3>✅ Care Tasks</h3>

        <?php if (count($tasks) > 0): ?>

            <table>

                <tr>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Task</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>

                <?php foreach ($tasks as $task): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($task['task_date']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($task['member_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($task['task_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($task['task_time']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($task['status']) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </table>

        <?php else: ?>

            <p class="muted">
                No care tasks available.
            </p>

        <?php endif; ?>

    </div>

</div>