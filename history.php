<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

require_once 'db.php';
date_default_timezone_set('UTC'); // Or use 'Asia/Manila' if needed

$now = date('Y-m-d H:i:s');

try {
    // Mark expired rentals as 'expired'
    $expire_stmt = $conn->prepare("
        UPDATE rentals 
        SET status = 'expired' 
        WHERE end_time <= ? AND status = 'active'
    ");
    $expire_stmt->execute([$now]);

    // Set karaoke to available ONLY IF it has NO active rentals
    $release_stmt = $conn->prepare("
        UPDATE karaoke k
        SET status = 'available'
        WHERE NOT EXISTS (
            SELECT 1 FROM rentals r WHERE r.karaoke_id = k.id AND r.status = 'active'
        )
        AND k.status != 'maintenance'
    ");
    $release_stmt->execute();

    // Fetch full rental history sorted from latest to oldest
    $stmt = $conn->prepare("
        SELECT r.karaoke_id, k.name, r.start_time, r.end_time, r.status
        FROM rentals r
        JOIN karaoke k ON r.karaoke_id = k.id
        ORDER BY r.start_time DESC
    ");
    $stmt->execute();
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
    $history = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History</title>
    <link rel="stylesheet" href="css/nimeroy.css">
    <style>
        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #333;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-expired {
            color: green;
            font-weight: bold;
        }

        .status-canceled {
            color: orange;
            font-weight: bold;
        }

        .status-active {
            color: blue;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="top">
    <div class="box" id="box1">Karaoke Rental History</div>
    <div class="nav-right" id="box2">
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<div class="nav">
    <div class="nbox" id="home"><a href="dashboard.php">Home</a></div>
    <div class="nbox" id="status"><a href="status.php">Status</a></div>
</div>

<table>
    <thead>
        <tr>
            <th>Karaoke Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($history) === 0): ?>
            <tr>
                <td colspan="4">No rental history found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($history as $record): ?>
                <tr>
                    <td><?= htmlspecialchars($record['name']) ?></td>
                    <td><?= htmlspecialchars($record['start_time']) ?></td>
                    <td><?= htmlspecialchars($record['end_time']) ?></td>
                    <td class="status-<?= strtolower($record['status']) ?>">
                        <?= htmlspecialchars(ucfirst($record['status'])) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
