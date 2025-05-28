<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Optional: delete the image file from server
    $stmt = $conn->prepare("SELECT picture FROM karaoke WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && file_exists($row['picture'])) {
        unlink($row['picture']);
    }

    $stmt = $conn->prepare("DELETE FROM karaoke WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = "Karaoke deleted!";
    header("Location: karaoke.php");
    exit;
}
?>
