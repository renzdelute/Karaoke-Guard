<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];

    // Get current picture path
    $stmt = $conn->prepare("SELECT picture FROM karaoke WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    $picture = $row['picture'];

    // Handle new picture upload if available
    if (!empty($_FILES['picture']['name'])) {
        $target_dir = "uploads/";
        $new_path = $target_dir . basename($_FILES["picture"]["name"]);

        // Delete old picture if it exists
        if (file_exists($picture)) {
            unlink($picture);
        }

        if (move_uploaded_file($_FILES["picture"]["tmp_name"], $new_path)) {
            $picture = $new_path;
        }
    }

    // Update database
    $update = $conn->prepare("UPDATE karaoke SET name = ?, picture = ? WHERE id = ?");
    $update->execute([$name, $picture, $id]);

    $_SESSION['message'] = "Karaoke updated successfully!";
    header("Location: karaoke.php");
    exit;
}
?>
