<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

// Fetch all plants
$plants = [];
$stmt = $conn->prepare('SELECT plantID, plantName, plantVariety FROM plantinfo ORDER BY plantName');
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $plants[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Plants - Smart Farming</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 800px; margin: 60px auto; background: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .nav-links { text-align: center; margin-bottom: 2em; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .plant-grid { display: grid; gap: 1em; }
        .plant-card { border: 1px solid #ddd; padding: 1em; border-radius: 4px; background: #f9f9f9; }
        .plant-name { font-weight: bold; font-size: 1.1em; margin-bottom: 0.5em; }
        .plant-variety { color: #666; margin-bottom: 1em; }
        .plant-actions { display: flex; gap: 0.5em; }
        .btn { padding: 0.5em 1em; border-radius: 4px; text-decoration: none; font-size: 0.9em; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-primary:hover { background: #0056b3; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-success:hover { background: #218838; }
        .empty-state { text-align: center; color: #666; padding: 2em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Plants</h2>
        
        <div class="nav-links">
            <a href="dashboard.php">← Back to Dashboard</a> | 
            <a href="add_plant.php">Add New Plant</a>
        </div>
        
        <?php if (empty($plants)): ?>
            <div class="empty-state">
                <p>No plants added yet.</p>
                <a href="add_plant.php" class="btn btn-success">Add Your First Plant</a>
            </div>
        <?php else: ?>
            <div class="plant-grid">
                <?php foreach ($plants as $plant): ?>
                    <div class="plant-card">
                        <div class="plant-name"><?php echo htmlspecialchars($plant['plantName']); ?></div>
                        <?php if ($plant['plantVariety']): ?>
                            <div class="plant-variety">Variety: <?php echo htmlspecialchars($plant['plantVariety']); ?></div>
                        <?php endif; ?>
                        <div class="plant-actions">
                            <a href="add_nutrition.php?plantID=<?php echo $plant['plantID']; ?>" class="btn btn-primary">Add Nutrition Needs</a>
                            <a href="view_nutrition.php?plantID=<?php echo $plant['plantID']; ?>" class="btn btn-success">View Nutrition</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 