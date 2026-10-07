<?php
require 'config.php';

$result = $conn->query("SELECT * FROM medical_reports ORDER BY report_date DESC");

if (!$result) {
    die("Database Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medical Reports</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }

        .report-card h2 {
            margin-top: 0;
            margin-bottom: 18px;
        }

        .report-card p {
            margin: 8px 0;
            font-size: 16px;
        }

        .edit-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 18px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .edit-btn:hover {
            background: #555;
        }

        .no-report {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 12px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>🩺 Medical Reports</h1>

    <?php if ($result->num_rows > 0): ?>

        <?php while ($row = $result->fetch_assoc()): ?>

            <div class="report-card">

                <h2>
                    <?php echo htmlspecialchars($row['report_type']); ?>
                </h2>

                <p>
                    <strong>Patient Name:</strong>
                    <?php echo htmlspecialchars($row['patient_name']); ?>
                </p>

                <p>
                    <strong>Report Date:</strong>
                    <?php echo htmlspecialchars($row['report_date']); ?>
                </p>

                <p>
                    <strong>Doctor:</strong>
                    <?php echo htmlspecialchars($row['doctor']); ?>
                </p>

                <p>
                    <strong>Hospital:</strong>
                    <?php echo htmlspecialchars($row['hospital']); ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?php echo htmlspecialchars($row['status']); ?>
                </p>

                <a href="edit_report.php?id=<?php echo $row['id']; ?>"
                   class="edit-btn">
                    ✏️ Edit Report
                </a>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="no-report">
            <h2>No Medical Reports Found</h2>
        </div>

    <?php endif; ?>

</div>

</body>
</html>