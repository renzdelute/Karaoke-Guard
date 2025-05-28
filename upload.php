<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $target_dir = "uploads/";
    $file_name = basename($_FILES["picture"]["name"]);
    $target_file = $target_dir . $file_name;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
        try {
            $sql = "INSERT INTO karaoke (name, picture) VALUES (:name, :picture)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':picture' => $target_file
            ]);

            header("Location: karaoke.php");
            exit;

        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    } else {
        echo "File upload failed.";
    }
}
?>