<?php
require 'config.php';

if (!isset($_GET['id'])) {
    die("Report ID missing");
}

$id = (int)$_GET['id'];

if (isset($_POST['update'])) {

    $patient_name = $_POST['patient_name'];
    $report_date  = $_POST['report_date'];
    $doctor       = $_POST['doctor'];
    $hospital     = $_POST['hospital'];
    $report_type  = $_POST['report_type'];
    $status       = $_POST['status'];

    $stmt = $conn->prepare(
        "UPDATE medical_reports 
         SET patient_name=?, report_date=?, doctor=?, hospital=?, report_type=?, status=?
         WHERE id=?"
    );

    $stmt->bind_param(
        "ssssssi",
        $patient_name,
        $report_date,
        $doctor,
        $hospital,
        $report_type,
        $status,
        $id
    );

    if ($stmt->execute()) {
        header("Location: medical_report.php");
        exit();
    } else {
        echo "Update failed: " . $conn->error;
    }
}

$result = $conn->query(
    "SELECT * FROM medical_reports WHERE id=$id"
);

if (!$result || $result->num_rows == 0) {
    die("Report not found");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Medical Report</title>
</head>

<body>

<h2>✏️ Edit Medical Report</h2>

<form method="POST">

    Patient Name:<br>
    <input type="text" name="patient_name"
           value="<?php echo htmlspecialchars($row['patient_name']); ?>">
    <br><br>

    Report Date:<br>
    <input type="date" name="report_date"
           value="<?php echo $row['report_date']; ?>">
    <br><br>

    Doctor:<br>
    <input type="text" name="doctor"
           value="<?php echo htmlspecialchars($row['doctor']); ?>">
    <br><br>

    Hospital:<br>
    <input type="text" name="hospital"
           value="<?php echo htmlspecialchars($row['hospital']); ?>">
    <br><br>

    Report Type:<br>
    <input type="text" name="report_type"
           value="<?php echo htmlspecialchars($row['report_type']); ?>">
    <br><br>

    Status:<br>
    <input type="text" name="status"
           value="<?php echo htmlspecialchars($row['status']); ?>">
    <br><br>

    <button type="submit" name="update">
        Update Report
    </button>

</form>

</body>
</html>