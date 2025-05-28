<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

require_once 'db.php';
require_once 'functions/paging.php';

// Handle maintenance start
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maintenance_id'])) {
    $id = $_POST['maintenance_id'];
    $stmt = $conn->prepare("UPDATE karaoke SET status = 'maintenance' WHERE id = ?");
    $stmt->execute([$id]);
}

// Handle finish maintenance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finish_maintenance_id'])) {
    $id = $_POST['finish_maintenance_id'];
    $stmt = $conn->prepare("UPDATE karaoke SET status = 'available' WHERE id = ?");
    $stmt->execute([$id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Karaoke List</title>
    <link rel="stylesheet" href="css/karaoke.css">
    <link rel="stylesheet" href="css/nimeroy.css">
</head>
<body>
<div class="top">
    <div class="box" id="box1">Karaoke Management System</div>
    <div class="nav-right" id="box2">
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

<div class="nav">
    <div class="nbox" id="home"><a href="dashboard.php">Home</a></div>
</div>

<!-- Add Karaoke Button -->
<div class="nav">
    <div class="nbox butangan" id="addKara">
        <a href="add_karaoke.php" style="text-decoration: none; color: white;">
            <button style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 6px; cursor: pointer;">
                Add New Karaoke
            </button>
        </a>
    </div>
</div>

<!-- Karaoke List -->
<div class="karaoke-list">
    <?php foreach ($karaokes as $kara): ?>
        <?php $is_maintenance = $kara['status'] === 'maintenance'; ?>
        <div class="karaoke-item <?= $is_maintenance ? 'maintenance' : '' ?>">
            <img src="<?= htmlspecialchars($kara['picture']) ?>" alt="Karaoke Image">
            <div class="karaoke-info">
                <h3><?= htmlspecialchars($kara['name']) ?></h3>
                <p>Status: <?= htmlspecialchars($kara['status'] ?? 'unknown') ?></p>

                <div class="karaoke-buttons">
                    <form action="update.php" method="get">
                        <input type="hidden" name="id" value="<?= $kara['id'] ?>">
                        <button type="submit" class="btn update" <?= $is_maintenance ? 'disabled' : '' ?>>Update</button>
                    </form>
                    <form action="delete.php" method="post" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="id" value="<?= $kara['id'] ?>">
                        <button type="submit" class="btn delete" <?= $is_maintenance ? 'disabled' : '' ?>>Delete</button>
                    </form>

                    <?php if ($is_maintenance): ?>
                        <form action="maintenance.php" method="post" onsubmit="return confirm('Finish maintenance for this karaoke?');">
                            <input type="hidden" name="id" value="<?= $kara['id'] ?>">
                            <input type="hidden" name="action" value="available">
                            <button type="submit" class="btn-finish-maintenance">Finish Maintenance</button>
                        </form>
                    <?php else: ?>
                        <form action="maintenance.php" method="post" onsubmit="return confirm('Are you sure you want to set this to maintenance?');">
                            <input type="hidden" name="id" value="<?= $kara['id'] ?>">
                            <input type="hidden" name="action" value="maintenance">
                            <button type="submit" class="btn-maintenance">Maintenance</button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a class="<?= $i == $page ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
    <?php endif; ?>
</div>
</body>
</html>
