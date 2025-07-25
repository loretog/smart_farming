<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Farming</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 600px; margin: 60px auto; background: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .logout { text-align: right; margin-bottom: 1em; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .nav-links { display: flex; flex-direction: column; gap: 1em; margin-top: 2em; }
        .nav-links a { background: #28a745; color: #fff; padding: 1em; border-radius: 4px; text-align: center; font-weight: bold; }
        .nav-links a:hover { background: #218838; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <h2>Welcome, <?php echo $username; ?>!</h2>
        <p>Manage your plants and their nutrition needs.</p>
        
        <div class="nav-links">
            <a href="add_plant.php">Add New Plant</a>
            <a href="plants.php">View My Plants</a>
        </div>
    </div>
</body>
</html> 