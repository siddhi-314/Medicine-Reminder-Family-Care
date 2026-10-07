<?php require 'config.php'; require 'header.php'; $uid=$_SESSION['user_id'];
if(isset($_POST['add'])){
$s=$conn->prepare("INSERT INTO medicines(user_id,member_id,medicine_name,start_date,end_date,reminder_time,frequency,notes) VALUES(?,?,?,?,?,?,?,?)");
$s->bind_param("iissssss",$uid,$_POST['member_id'],$_POST['medicine_name'],$_POST['start_date'],$_POST['end_date'],$_POST['reminder_time'],$_POST['frequency'],$_POST['notes']); $s->execute(); header("Location: medicines.php"); exit;
}
if(isset($_GET['delete'])){ $id=(int)$_GET['delete']; $conn->query("DELETE FROM medicines WHERE id=$id AND user_id=$uid"); header("Location: medicines.php"); exit; }
if(isset($_GET['status'],$_GET['id'])){ $id=(int)$_GET['id']; $status=$_GET['status']==='Taken'?'Taken':'Skipped'; $s=$conn->prepare("INSERT INTO medicine_logs(medicine_id,log_date,status) VALUES(?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status)"); $today=date('Y-m-d'); $s->bind_param("iss",$id,$today,$status); $s->execute(); header("Location: medicines.php"); exit; }
$members=$conn->query("SELECT id,name FROM family_members WHERE user_id=$uid ORDER BY name");
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$member_filter = isset($_GET['member']) ? (int)$_GET['member'] : 0;

$sql = "SELECT m.*,
        f.name AS member_name,
        COALESCE(
            (SELECT status
             FROM medicine_logs l
             WHERE l.medicine_id = m.id
             AND l.log_date = CURDATE()),
            'Pending'
        ) AS today_status
        FROM medicines m
        JOIN family_members f ON f.id = m.member_id
        WHERE m.user_id = ?";

$params = [$uid];
$types = "i";

if ($search !== '') {
    $sql .= " AND (m.medicine_name LIKE ? OR f.name LIKE ?)";
    $search_value = "%".$search."%";
    $params[] = $search_value;
    $params[] = $search_value;
    $types .= "ss";
}

if ($member_filter > 0) {
    $sql .= " AND m.member_id = ?";
    $params[] = $member_filter;
    $types .= "i";
}

$sql .= " ORDER BY m.reminder_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$list = $stmt->get_result();
?>
<div class="container"><h2>Medicine Reminders</h2>
<div class="form-card"><h3>Add Reminder</h3>
<?php if($members->num_rows===0): ?><div class="notice">First add a family member.</div><?php endif; ?>
<form method="post"><label>Family Member</label><select name="member_id" required><?php while($m=$members->fetch_assoc()): ?><option value="<?=$m['id']?>"><?=htmlspecialchars($m['name'])?></option><?php endwhile; ?></select>
<label>Medicine Name</label><input name="medicine_name" required>
<label>Start Date</label><input type="date" name="start_date" value="<?=date('Y-m-d')?>" required>
<label>End Date</label><input type="date" name="end_date">
<label>Reminder Time</label><input type="time" name="reminder_time" required>
<label>Frequency</label><select name="frequency"><option>Once daily</option><option>Twice daily</option><option>Three times daily</option><option>Custom</option></select>
<label>Notes</label><input name="notes" placeholder="Optional user-entered note">
<button class="btn" name="add">Save Reminder</button></form></div>
<div class="card"><table><tr><th>Member</th><th>Medicine</th><th>Time</th><th>Frequency</th><th>Status</th><th>Action</th></tr>
<!-- Search Box -->

<div class="form-card">

    <h3>🔎 Search / Filter Medicines</h3>

    <form method="get">

        <label>Search Medicine or Family Member</label>

        <input
            type="text"
            name="search"
            placeholder="Enter medicine name or member name"
            value="<?= htmlspecialchars($search) ?>"
        >

        <label>Family Member</label>

        <select name="member">

            <option value="0">All Family Members</option>

            <?php
            $filter_members = $conn->query(
                "SELECT id, name
                 FROM family_members
                 WHERE user_id=$uid
                 ORDER BY name"
            );

            while ($fm = $filter_members->fetch_assoc()):
            ?>

                <option
                    value="<?= $fm['id'] ?>"
                    <?= ($member_filter == $fm['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($fm['name']) ?>
                </option>

            <?php endwhile; ?>

        </select>

        <button type="submit" class="btn">
            🔎 Search
        </button>

        <a href="medicines.php" class="btn secondary">
            Clear
        </a>

    </form>

</div>

<div class="card">

    <h3>Today's Reminders</h3>

    <table>

        <tr>
            <th>Member</th>
            <th>Medicine</th>
            <th>Time</th>
            <th>Frequency</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($r=$list->fetch_assoc()): ?>

        <tr>

            <td>
                <?= htmlspecialchars($r['member_name']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['medicine_name']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['reminder_time']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['frequency']) ?>
            </td>

            <td>
                <?= htmlspecialchars($r['today_status']) ?>
            </td>

            <td class="actions">

                <a
                    class="btn success small"
                    href="?status=Taken&id=<?=$r['id']?>">
                    Taken
                </a>

                <a
                    class="btn secondary small"
                    href="?status=Skipped&id=<?=$r['id']?>">
                    Skip
                </a>

                <a
                    class="btn danger small"
                    href="?delete=<?=$r['id']?>"
                    onclick="return confirm('Delete reminder?')">
                    Delete
                </a>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

    <br>

    <button
        type="button"
        class="btn"
        onclick="enableMedicineNotifications()">
        🔔 Enable Medicine Notifications
    </button>

</div>


<script>

function checkMedicineReminder() {

    if (!("Notification" in window)) {
        return;
    }

    if (Notification.permission !== "granted") {
        return;
    }

    const medicines = <?= json_encode(
        $conn->query(
            "SELECT medicine_name, reminder_time
             FROM medicines
             WHERE user_id=$uid"
        )->fetch_all(MYSQLI_ASSOC)
    ) ?>;

    const now = new Date();

    const currentHour =
        String(now.getHours()).padStart(2, "0");

    const currentMinute =
        String(now.getMinutes()).padStart(2, "0");

    const currentTime =
        currentHour + ":" + currentMinute;


    medicines.forEach(function(medicine) {

        const medicineTime =
            medicine.reminder_time.substring(0, 5);

        if (medicineTime === currentTime) {

            const notificationKey =
                "medicine_" +
                medicine.medicine_name +
                "_" +
                currentTime +
                "_" +
                now.toDateString();


            if (!localStorage.getItem(notificationKey)) {

                new Notification(
                    "💊 Medicine Reminder",
                    {
                        body:
                        "Time to take " +
                        medicine.medicine_name
                    }
                );

                localStorage.setItem(
                    notificationKey,
                    "shown"
                );
            }
        }

    });
}


function enableMedicineNotifications() {

    if (!("Notification" in window)) {

        alert(
            "This browser does not support notifications."
        );

        return;
    }


    Notification.requestPermission()
        .then(function(permission) {

            if (permission === "granted") {

                alert(
                    "🔔 Medicine notifications enabled!"
                );

                checkMedicineReminder();

            } else {

                alert(
                    "Notification permission was not allowed."
                );

            }

        });
}


setInterval(
    checkMedicineReminder,
    60000
);

checkMedicineReminder();

</script>

</div>