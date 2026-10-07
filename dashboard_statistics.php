<?php
// Dashboard Statistics
$members = 0;
$meds = 0;
$apps = 0;
$tasks = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Statistics</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        /* Statistics Cards */
        .statistics {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            width: 210px;
            padding: 25px 15px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        .stat-icon {
            font-size: 35px;
            display: inline-block;
            margin-right: 8px;
        }

        .stat-title {
            font-size: 18px;
            font-weight: bold;
            display: inline;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            margin: 15px 0 5px;
        }

        /* Chart */
        .chart-box {
            background: white;
            max-width: 850px;
            margin: 40px auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        .chart-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .bar {
            display: flex;
            align-items: center;
            margin: 18px 0;
        }

        .bar-label {
            width: 190px;
            font-weight: bold;
        }

        .bar-area {
            flex: 1;
            background: #eeeeee;
            height: 30px;
            border-radius: 8px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 8px;
            width: 0%;
        }

        .bar-value {
            width: 45px;
            text-align: right;
            font-weight: bold;
            margin-left: 10px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 25px;
        }
    </style>
</head>

<body>

<h1>📊 Dashboard Statistics</h1>

<!-- Statistics Cards -->
<div class="statistics">

    <div class="stat-card">
        <span class="stat-icon">👨‍👩‍👧</span>
        <span class="stat-title">Family Members</span>
        <div class="stat-number"><?php echo $members; ?></div>
    </div>

    <div class="stat-card">
        <span class="stat-icon">💊</span>
        <span class="stat-title">Total Medicines</span>
        <div class="stat-number"><?php echo $meds; ?></div>
    </div>

    <div class="stat-card">
        <span class="stat-icon">📅</span>
        <span class="stat-title">Upcoming Appointments</span>
        <div class="stat-number"><?php echo $apps; ?></div>
    </div>

    <div class="stat-card">
        <span class="stat-icon">⏳</span>
        <span class="stat-title">Pending Care Tasks</span>
        <div class="stat-number"><?php echo $tasks; ?></div>
    </div>

</div>


<!-- Statistics Chart -->
<div class="chart-box">

    <h2 class="chart-title">📈 Family Care Statistics</h2>

    <div class="bar">
        <div class="bar-label">👨‍👩‍👧 Family Members</div>
        <div class="bar-area">
            <div class="bar-fill"
                 style="width: <?php echo min($members * 10, 100); ?>%;">
            </div>
        </div>
        <div class="bar-value"><?php echo $members; ?></div>
    </div>

    <div class="bar">
        <div class="bar-label">💊 Medicines</div>
        <div class="bar-area">
            <div class="bar-fill"
                 style="width: <?php echo min($meds * 10, 100); ?>%;">
            </div>
        </div>
        <div class="bar-value"><?php echo $meds; ?></div>
    </div>

    <div class="bar">
        <div class="bar-label">📅 Appointments</div>
        <div class="bar-area">
            <div class="bar-fill"
                 style="width: <?php echo min($apps * 10, 100); ?>%;">
            </div>
        </div>
        <div class="bar-value"><?php echo $apps; ?></div>
    </div>

    <div class="bar">
        <div class="bar-label">⏳ Care Tasks</div>
        <div class="bar-area">
            <div class="bar-fill"
                 style="width: <?php echo min($tasks * 10, 100); ?>%;">
            </div>
        </div>
        <div class="bar-value"><?php echo $tasks; ?></div>
    </div>

</div>

<a class="back" href="dashboard.php">← Back to Dashboard</a>

</body>
</html>