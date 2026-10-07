<?php
require 'config.php';
require 'header.php';

$uid = $_SESSION['user_id'];
$today = date('Y-m-d');


/* =========================
   TAKE / SKIP MEDICINE
   ========================= */

if (isset($_GET['status'], $_GET['id'])) {

    $id = (int)$_GET['id'];

    $status = ($_GET['status'] === 'Taken')
        ? 'Taken'
        : 'Skipped';

    $check = $conn->prepare("
        SELECT id
        FROM medicines
        WHERE id = ? AND user_id = ?
    ");

    $check->bind_param("ii", $id, $uid);
    $check->execute();

    $valid = $check->get_result();

    if ($valid->num_rows > 0) {

        $stmt = $conn->prepare("
            INSERT INTO medicine_logs
            (medicine_id, log_date, status)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
            status = VALUES(status)
        ");

        $stmt->bind_param(
            "iss",
            $id,
            $today,
            $status
        );

        $stmt->execute();
    }

    header("Location: daily_tracker.php");
    exit;
}


/* =========================
   GET TODAY'S MEDICINES
   ========================= */

$sql = "
SELECT
    m.id,
    m.medicine_name,
    m.reminder_time,
    m.frequency,
    f.name AS member_name,

    COALESCE(
        (
            SELECT l.status
            FROM medicine_logs l
            WHERE l.medicine_id = m.id
            AND l.log_date = ?
        ),
        'Pending'
    ) AS today_status

FROM medicines m

JOIN family_members f
ON f.id = m.member_id

WHERE m.user_id = ?

AND m.start_date <= ?

AND (
    m.end_date IS NULL
    OR m.end_date = ''
    OR m.end_date >= ?
)

ORDER BY m.reminder_time
";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "siss",
    $today,
    $uid,
    $today,
    $today
);

$stmt->execute();

$result = $stmt->get_result();


/* =========================
   COUNTS
   ========================= */

$total = 0;
$taken = 0;
$skipped = 0;
$pending = 0;

$medicines = [];


while ($row = $result->fetch_assoc()) {

    $medicines[] = $row;

    $total++;

    if ($row['today_status'] === 'Taken') {

        $taken++;

    } elseif ($row['today_status'] === 'Skipped') {

        $skipped++;

    } else {

        $pending++;
    }
}


/* =========================
   PROGRESS
   ========================= */

$progress = 0;

if ($total > 0) {

    $progress = round(
        ($taken / $total) * 100
    );
}

?>

<div class="container">

    <div class="hero">

        <h2>💊 Daily Medicine Tracker</h2>

        <p class="muted">
            Track your family's medicine status for today.
        </p>

    </div>


    <!-- SUMMARY CARDS -->

    <div class="card-grid">

        <div class="card">

            <div class="stat">
                <?= $total ?>
            </div>

            <h3>Total Medicines</h3>

        </div>


        <div class="card">

            <div class="stat">
                <?= $taken ?>
            </div>

            <h3>✓ Taken</h3>

        </div>


        <div class="card">

            <div class="stat">
                <?= $skipped ?>
            </div>

            <h3>✕ Skipped</h3>

        </div>


        <div class="card">

            <div class="stat">
                <?= $pending ?>
            </div>

            <h3>⏳ Pending</h3>

        </div>

    </div>


    <!-- PROGRESS -->

    <div class="card">

        <h3>📊 Today's Progress</h3>

        <p>
            <?= $taken ?> of <?= $total ?>
            medicines taken
        </p>

        <div style="
            width:100%;
            background:#eee;
            height:20px;
            border-radius:10px;
            overflow:hidden;
        ">

            <div style="
                width:<?= $progress ?>%;
                height:20px;
                background:#28a745;
            "></div>

        </div>

        <p>
            <b><?= $progress ?>%</b> completed
        </p>

    </div>


    <!-- TODAY'S MEDICINE TABLE -->

    <div class="card">

        <h3>📋 Today's Medicines</h3>

        <?php if ($total > 0): ?>

        <table>

            <tr>

                <th>Member</th>

                <th>Medicine</th>

                <th>Time</th>

                <th>Frequency</th>

                <th>Status</th>

                <th>Action</th>

            </tr>


            <?php foreach ($medicines as $medicine): ?>

            <tr>

                <td>
                    <?= htmlspecialchars(
                        $medicine['member_name']
                    ) ?>
                </td>


                <td>
                    <?= htmlspecialchars(
                        $medicine['medicine_name']
                    ) ?>
                </td>


                <td>
                    <?= htmlspecialchars(
                        $medicine['reminder_time']
                    ) ?>
                </td>


                <td>
                    <?= htmlspecialchars(
                        $medicine['frequency']
                    ) ?>
                </td>


                <td>

                    <?php if (
                        $medicine['today_status']
                        === 'Taken'
                    ): ?>

                        <span style="
                            color:green;
                            font-weight:bold;
                        ">
                            ✓ Taken
                        </span>

                    <?php elseif (
                        $medicine['today_status']
                        === 'Skipped'
                    ): ?>

                        <span style="
                            color:red;
                            font-weight:bold;
                        ">
                            ✕ Skipped
                        </span>

                    <?php else: ?>

                        <span style="
                            color:#d97706;
                            font-weight:bold;
                        ">
                            ⏳ Pending
                        </span>

                    <?php endif; ?>

                </td>


                <td>

                    <a
                        class="btn success small"
                        href="?status=Taken&id=<?= $medicine['id'] ?>"
                    >
                        Taken
                    </a>


                    <a
                        class="btn secondary small"
                        href="?status=Skipped&id=<?= $medicine['id'] ?>"
                    >
                        Skip
                    </a>

                </td>

            </tr>

            <?php endforeach; ?>

        </table>

        <?php else: ?>

            <p class="muted">
                No medicines scheduled for today.
            </p>

        <?php endif; ?>

    </div>

</div>

<div class="footer">
    Medicine Reminder & Family Care App
</div>