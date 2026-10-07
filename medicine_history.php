<?php
require 'config.php';
require 'header.php';

$uid = $_SESSION['user_id'];

/* MEDICINE HISTORY */

$sql = "
    SELECT
        l.id,
        l.log_date,
        l.status,
        m.medicine_name,
        m.reminder_time,
        f.name AS member_name
    FROM medicine_logs l
    INNER JOIN medicines m
        ON l.medicine_id = m.id
    INNER JOIN family_members f
        ON m.member_id = f.id
    WHERE m.user_id = ?
    ORDER BY l.log_date DESC, m.reminder_time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();

$result = $stmt->get_result();
?>

<div class="container">

    <div class="hero">

        <h2>💊 Medicine History</h2>

        <p class="muted">
            View your previous medicine reminder records.
        </p>

    </div>


    <div class="card">

        <?php if ($result->num_rows > 0): ?>

            <table>

                <tr>

                    <th>Date</th>

                    <th>Family Member</th>

                    <th>Medicine</th>

                    <th>Reminder Time</th>

                    <th>Status</th>

                </tr>


                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($row['log_date']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['member_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['medicine_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['reminder_time']) ?>
                        </td>

                        <td>

                            <?php if ($row['status'] === 'Taken'): ?>

                                <span style="color:green;font-weight:bold;">
                                    ✓ Taken
                                </span>

                            <?php else: ?>

                                <span style="color:#dc2626;font-weight:bold;">
                                    ✕ Skipped
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>

        <?php else: ?>

            <div class="notice">

                No medicine history available yet.

                <br><br>

                Go to <b>Medicine Reminders</b> and
                click <b>Taken</b> or <b>Skip</b>.

            </div>

        <?php endif; ?>

    </div>

</div>