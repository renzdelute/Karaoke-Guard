<?php
session_start();
// Redirect to login page if not logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/nimeroy.css">
</head>
<body>
    <div class="top">
        <div class="box" id="box1"> Karaoke Management System </div>
         <div class="nav-right" id="box2">
                <form action="logout.php" method="post">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>           
         </div>
    </div>

    <div class="nav">
        <div class="nbox" id="karaoke"><a href="karaoke.php">karaoke</a></div>
        <div class="nbox" id="status"><a href="status.php">Status</a></div>
        <div class="nbox" id="history"><a href="history.php">History</a></div>

    </div>

    

</body>
</html>