<?php
require 'config.php';
require 'header.php';

$uid = $_SESSION['user_id'];

$members = $conn->query(
    "SELECT COUNT(*) c FROM family_members WHERE user_id=$uid"
)->fetch_assoc()['c'];

$meds = $conn->query(
    "SELECT COUNT(*) c FROM medicines WHERE user_id=$uid"
)->fetch_assoc()['c'];

$apps = $conn->query(
    "SELECT COUNT(*) c FROM appointments 
     WHERE user_id=$uid AND appointment_date>=CURDATE()"
)->fetch_assoc()['c'];

$tasks = $conn->query(
    "SELECT COUNT(*) c FROM care_tasks 
     WHERE user_id=$uid AND task_date=CURDATE() 
     AND status='Pending'"
)->fetch_assoc()['c'];
?>

<style>
.dashboard-container {
    max-width: 1100px;
    margin: 30px auto;
    padding: 20px;
}

.hero {
    text-align: center;
    margin-bottom: 35px;
}

.hero h1 {
    font-size: 32px;
    margin-bottom: 10px;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 22px;
}

.dashboard-card {
    background: white;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.12);
    transition: 0.2s;
}

.dashboard-card:hover {
    transform: translateY(-3px);
}

.dashboard-card h2,
.dashboard-card h3 {
    margin-top: 0;
}

.dashboard-card .stat {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 8px;
}

.dashboard-card a {
    display: inline-block;
    margin-top: 12px;
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 6px;
    background: #eeeeee;
}

.full-card {
    margin-top: 22px;
}

@media (max-width: 700px) {
    .card-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard-container">

    <!-- Welcome -->
    <div class="hero">
        <h1>
            Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋
        </h1>

        <p class="muted">
            Manage family reminders, appointments and care tasks in one place.
        </p>
    </div>


    <!-- Dashboard Features -->
    <div class="card-grid">

        <!-- Profile -->
        <div class="dashboard-card">
            <h2>👤 Profile</h2>
            <p>View your profile information.</p>
            <a href="profile.php">View Profile</a>
        </div>


        <!-- Family Members -->
        <div class="dashboard-card">
            <div class="stat"><?= $members ?></div>
            <h3>👨‍👩‍👧 Family Members</h3>
            <p class="muted">Manage your family members.</p>
            <a href="members.php">Manage</a>
        </div>


        <!-- Medicine Reminders -->
        <div class="dashboard-card">
            <div class="stat"><?= $meds ?></div>
            <h3>💊 Medicine Reminders</h3>
            <p class="muted">Manage medicine reminders.</p>
            <a href="medicines.php">Manage</a>
        </div>


        <!-- Medicine History -->
        <div class="dashboard-card">
            <h3>📋 Medicine History</h3>
            <p class="muted">
                View taken and skipped medicine records.
            </p>
            <a href="medicine_history.php">View History</a>
        </div>


        <!-- Upcoming Appointments -->
        <div class="dashboard-card">
            <div class="stat"><?= $apps ?></div>
            <h3>🩺 Upcoming Appointments</h3>
            <p class="muted">View upcoming appointments.</p>
            <a href="appointments.php">View</a>
        </div>


        <!-- Pending Care Tasks -->
        <div class="dashboard-card">
            <div class="stat"><?= $tasks ?></div>
            <h3>⏳ Pending Care Tasks</h3>
            <p class="muted">View today's pending care tasks.</p>
            <a href="care_tasks.php">View</a>
        </div>


        <!-- Daily Medicine Tracker -->
        <div class="dashboard-card">
            <h3>📊 Daily Medicine Tracker</h3>
            <p class="muted">
                Track today's taken, skipped and pending medicines.
            </p>
            <a href="daily_medicine_tracker.php">Open Tracker</a>
        </div>


        <!-- Family Care Calendar -->
        <div class="dashboard-card">
            <h3>📅 Family Care Calendar</h3>
            <p class="muted">
                View medicines, appointments and care tasks.
            </p>
            <a href="calendar.php">Open Calendar</a>
        </div>


        <!-- Dashboard Statistics -->
        <div class="dashboard-card">
            <h3>📊 Dashboard Statistics</h3>
            <p class="muted">
                View family care statistics and summary.
            </p>
            <a href="dashboard_statistics.php">View Statistics</a>
        </div>


        <!-- Notifications -->
        <div class="dashboard-card">
            <h3>🔔 Notifications</h3>
            <p class="muted">
                View medicine, appointment and care reminders.
            </p>
            <a href="notification.php">View Notifications</a>
        </div>


        <!-- Medical Reports -->
        <div class="dashboard-card">
            <h3>📄 Medical Reports</h3>
            <p class="muted">
                View family members' medical reports.
            </p>
            <a href="medical_report.php">View Reports</a>
        </div>
        <div class="dashboard-card">
    <h3>💊 Disease Medicine</h3>

    <p>Search medicine by disease.</p>

    <a href="disease_medicine.php" class="card-button">
        View
    </a>
</div>


    <!-- Important -->
    <div class="dashboard-card full-card">
        <h3>Important</h3>

        <p class="muted">
            This app stores user-entered reminders and records.
            It does not diagnose conditions, prescribe medicines,
            or calculate dosage.
        </p>
    </div>

</div>

<div class="footer">
    Medicine Reminder & Family Care App
</div>