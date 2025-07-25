<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plantName = trim($_POST['plantName'] ?? '');
    $plantVariety = trim($_POST['plantVariety'] ?? '');

    // Validate
    if (!$plantName) {
        $errors[] = 'Plant name is required.';
    }

    if (!$errors) {
        $stmt = $conn->prepare('INSERT INTO plantinfo (plantName, plantVariety) VALUES (?, ?)');
        $stmt->bind_param('ss', $plantName, $plantVariety);
        if ($stmt->execute()) {
            $plantID = $conn->insert_id; // Get the auto-generated ID
            $success = 'Plant added successfully! <a href="add_nutrition.php?plantID=' . $plantID . '">Add nutrition needs</a> or <a href="plants.php">view all plants</a>.';
        } else {
            $errors[] = 'Failed to add plant. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Plant - Smart Farming</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 500px; margin: 60px auto; background: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        form { display: flex; flex-direction: column; gap: 1em; }
        input[type=text] { padding: 0.75em; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #28a745; color: #fff; border: none; padding: 0.75em; border-radius: 4px; font-weight: bold; cursor: pointer; }
        button:hover { background: #218838; }
        .nav-links { text-align: center; margin-top: 1em; }
        .error { color: #b30000; background: #ffe5e5; padding: 0.5em; border-radius: 4px; margin-bottom: 1em; }
        .success { color: #155724; background: #d4edda; padding: 0.5em; border-radius: 4px; margin-bottom: 1em; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Plant</h2>
        <?php if ($errors): ?>
            <div class="error">
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="add_plant.php">
            <input type="text" name="plantName" placeholder="Plant Name" required value="<?php echo htmlspecialchars($_POST['plantName'] ?? ''); ?>">
            <input type="text" name="plantVariety" placeholder="Plant Variety (optional)" value="<?php echo htmlspecialchars($_POST['plantVariety'] ?? ''); ?>">
            <button type="submit">Add Plant</button>
        </form>
        
        <div class="nav-links">
            <a href="dashboard.php">← Back to Dashboard</a> | 
            <a href="plants.php">View All Plants</a>
        </div>
    </div>
</body>
</html> 