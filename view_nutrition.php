<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

$plantID = $_GET['plantID'] ?? '';
$plantName = '';
$nutritionData = [];

// Validate plantID and get plant info
if (!$plantID) {
    header('Location: plants.php');
    exit;
}

$stmt = $conn->prepare('SELECT plantName FROM plantinfo WHERE plantID = ?');
$stmt->bind_param('i', $plantID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: plants.php');
    exit;
}
$plant = $result->fetch_assoc();
$plantName = $plant['plantName'];
$stmt->close();

// Get nutrition data for this plant
$stmt = $conn->prepare('SELECT * FROM plantnutrionneed WHERE plantID = ? ORDER BY nutritionSetName');
$stmt->bind_param('i', $plantID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $nutritionData[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Nutrition - Smart Farming</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 60px auto; background: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .plant-info { background: #e9ecef; padding: 1em; border-radius: 4px; margin-bottom: 1em; text-align: center; }
        .nav-links { text-align: center; margin-bottom: 2em; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { padding: 0.5em 1em; border-radius: 4px; text-decoration: none; font-size: 0.9em; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-primary:hover { background: #0056b3; }
        .empty-state { text-align: center; color: #666; padding: 2em; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em; }
        th, td { padding: 0.75em; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .nutrition-table { width: 100%; border-collapse: collapse; margin-top: 1em; }
        .nutrition-table th, .nutrition-table td { padding: 0.75em; text-align: left; border: 1px solid #ddd; }
        .nutrition-table th { background: #f8f9fa; font-weight: bold; color: #495057; }
        .nutrition-table tr:nth-child(even) { background: #f9f9f9; }
        .nutrition-table tr:hover { background: #e9ecef; }
        .nutrition-set-name { background: #007bff; color: #fff; font-weight: bold; }
        .nutrition-set-name td { text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Plant Nutrition Details</h2>
        
        <div class="plant-info">
            <strong>Plant:</strong> <?php echo htmlspecialchars($plantName); ?>
        </div>
        
        <div class="nav-links">
            <a href="plants.php">← Back to Plants</a> | 
            <a href="add_nutrition.php?plantID=<?php echo $plantID; ?>" class="btn btn-primary">Add New Nutrition Set</a> | 
            <a href="dashboard.php">Dashboard</a>
        </div>
        
        <?php if (empty($nutritionData)): ?>
            <div class="empty-state">
                <p>No nutrition data found for this plant.</p>
                <a href="add_nutrition.php?plantID=<?php echo $plantID; ?>" class="btn btn-primary">Add Nutrition Needs</a>
            </div>
        <?php else: ?>
            <table class="nutrition-table">
                <thead>
                    <tr>
                        <th>Nutrition Set</th>
                        <th>Nitrogen (N)</th>
                        <th>Phosphorus (P)</th>
                        <th>Potassium (K)</th>
                        <th>Electrical Conductivity</th>
                        <th>pH</th>
                        <th>Temperature (°C)</th>
                        <th>Moisture (%)</th>
                        <th>Flow Rate (L/min)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nutritionData as $nutrition): ?>
                        <tr class="nutrition-set-name">
                            <td colspan="9"><?php echo htmlspecialchars($nutrition['nutritionSetName']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Values</strong></td>
                            <td><?php echo $nutrition['soilN'] !== null ? htmlspecialchars($nutrition['soilN']) : '-'; ?></td>
                            <td><?php echo $nutrition['soilP'] !== null ? htmlspecialchars($nutrition['soilP']) : '-'; ?></td>
                            <td><?php echo $nutrition['soilK'] !== null ? htmlspecialchars($nutrition['soilK']) : '-'; ?></td>
                            <td><?php echo $nutrition['soilEC'] !== null ? htmlspecialchars($nutrition['soilEC']) : '-'; ?></td>
                            <td><?php echo $nutrition['soilPH'] !== null ? htmlspecialchars($nutrition['soilPH']) : '-'; ?></td>
                            <td><?php echo $nutrition['soilT'] !== null ? htmlspecialchars($nutrition['soilT']) : '-'; ?></td>
                            <td><?php echo $nutrition['soilM'] !== null ? htmlspecialchars($nutrition['soilM']) : '-'; ?></td>
                            <td><?php echo $nutrition['flowRate'] !== null ? htmlspecialchars($nutrition['flowRate']) : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html> 