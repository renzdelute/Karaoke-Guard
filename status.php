<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}
require_once 'db.php';

// Determine the current page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

// Number of records per page
$records_per_page = 9;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $records_per_page;

// Fetch the total number of karaoke records
$total_stmt = $conn->query("SELECT COUNT(*) FROM karaoke");
$total_karaokes = $total_stmt->fetchColumn();

// Calculate the total number of pages
$total_pages = ceil($total_karaokes / $records_per_page);

// Fetch the records for the current page, including rental end time
$stmt = $conn->prepare("
    SELECT k.*, r.end_time
    FROM karaoke k
    LEFT JOIN rentals r ON k.id = r.karaoke_id
    ORDER BY k.id DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$karaokes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Karaoke Status</title>
    <link rel="stylesheet" href="css/nimeroy.css">
    <link rel="stylesheet" href="kara.css">
    <style>
        .karaoke-status {
            display: block;
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            font-size: 1.2em;
        }
        .rental-timer {
            text-align: center;
            font-size: 1em;
            font-weight: bold;
            margin-top: 5px;
            color: #f0f0f0;
        }
        .btn-cancel {
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-cancel:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="top">
    <div class="box" id="box1">Karaoke Management System - Status</div>
    <div class="nav-right" id="box2">
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<div class="nav">
    <div class="nbox" id="home"><a href="dashboard.php">Home</a></div>
    <div class="nbox" id="karaoke"><a href="karaoke.php">Karaoke List</a></div>
</div>

<div class="karaoke-list">
    <?php foreach ($karaokes as $kara): ?>
        <div class="karaoke-item">
            <img src="<?= htmlspecialchars($kara['picture']) ?>" alt="Karaoke Image">
            <div class="karaoke-info">
                <h3><?= htmlspecialchars($kara['name']) ?></h3>
                <span class="karaoke-status status-<?= strtolower($kara['status']) === 'available' ? 'available' : 'rented' ?>">
                    <?= htmlspecialchars($kara['status']) ?>
                </span>
                <?php if (strtolower($kara['status']) === 'available'): ?>
                    <form action="rent.php" method="post">
                        <input type="hidden" name="id" value="<?= $kara['id'] ?>">
                        <button type="submit" class="btn-rent">Rent</button>
                    </form>
                <?php else: ?>
                    <?php if (!empty($kara['end_time'])): ?>
                        <div class="rental-timer" data-endtime="<?= $kara['end_time'] ?>">
                            Loading timer...
                        </div>
                    <?php endif; ?>
                    <form action="cancel_rental.php" method="post">
                        <input type="hidden" name="id" value="<?= $kara['id'] ?>">
                        <button type="submit" class="btn-cancel">Cancel Rental</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $page): ?>
            <a class="active" href="?page=<?= $i ?>"><?= $i ?></a>
        <?php else: ?>
            <a href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timers = document.querySelectorAll('.rental-timer');
        timers.forEach(timer => {
            const endTime = new Date(timer.dataset.endtime).getTime();

            function updateTimer() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance <= 0) {
                    timer.textContent = 'Rental period ended';
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timer.textContent = `Time remaining: ${hours}h ${minutes}m ${seconds}s`;
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        });
    });
</script>

</body>
</html>
