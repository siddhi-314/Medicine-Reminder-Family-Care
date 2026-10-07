<?php
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
?>
<div class="navbar">
<div class="brand">💊 Family Care</div>
<div>
<a href="dashboard.php">Dashboard</a>
<a href="members.php">Family</a>
<a href="medicines.php">Medicines</a>
<a href="appointments.php">Appointments</a>
<a href="care_tasks.php">Care Tasks</a>
<a href="feedback.php">Feedback</a>
<a href="logout.php">Logout</a>
</div></div>
