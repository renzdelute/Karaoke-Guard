<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM karaoke WHERE id = ?");
$stmt->execute([$id]);
$karaoke = $stmt->fetch();

if (!$karaoke) {
    echo "Karaoke entry not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Karaoke</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #2e2b2b;
      font-family: Arial, sans-serif;
      color: #fff;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .update-container {
      background-color: #3b3b3b;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.6);
      text-align: center;
      width: 100%;
      max-width: 450px;
    }

    .update-container h2 {
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    label {
      align-self: flex-start;
      margin-top: 15px;
      font-weight: bold;
    }

    input[type="text"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: none;
      border-radius: 6px;
      background-color: #555;
      color: #fff;
    }

    img {
      margin: 15px 0;
      border-radius: 6px;
      width: 150px;
      height: auto;
    }

    button,
    .cancel-btn {
      margin-top: 20px;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
    }

    button[name="update"] {
      background-color: #4caf50;
      color: white;
      margin-right: 10px;
    }

    .cancel-btn {
      background-color: #f44336;
      color: white;
    }

    button:hover,
    .cancel-btn:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>
  <div class="update-container">
    <h2>Update Karaoke</h2>
    <form action="update_save.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $karaoke['id'] ?>">

      <label for="name">Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($karaoke['name']) ?>" required>

      <p>Current Picture:</p>
      <img src="<?= htmlspecialchars($karaoke['picture']) ?>" alt="Karaoke Image">

      <label for="picture">Change Picture (optional):</label>
      <input type="file" name="picture" accept="image/*">

      <div style="display: flex; justify-content: center;">
        <button type="submit" name="update">Save Changes</button>
        <a href="karaoke.php" class="cancel-btn">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
