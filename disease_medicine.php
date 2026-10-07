<?php
require 'config.php';

$results = null;
$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    if ($search != "") {
        $stmt = $conn->prepare(
            "SELECT * FROM disease_medicines
             WHERE disease LIKE ?
             ORDER BY disease"
        );

        $like = "%" . $search . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();

        $results = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Disease Medicine Search</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: auto;
        }

        h1 {
            text-align: center;
        }

        .search-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ccc;
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 12px;
            font-size: 16px;
        }

        .search-box button {
            padding: 12px 20px;
            cursor: pointer;
        }

        .result {
            background: white;
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #ccc;
        }

        .no-result {
            text-align: center;
            margin-top: 25px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>💊 Disease Medicine Search</h1>

    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            placeholder="Search disease..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">🔍 Search</button>

    </form>

    <?php if ($results !== null): ?>

        <?php if ($results->num_rows > 0): ?>

            <?php while ($row = $results->fetch_assoc()): ?>

                <div class="result">

                    <h2>
                        Disease:
                        <?php echo htmlspecialchars($row['disease']); ?>
                    </h2>

                    <p>
                        <strong>Medicine:</strong>
                        <?php echo htmlspecialchars($row['medicine']); ?>
                    </p>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="no-result">
                <h3>No medicine found</h3>
            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>