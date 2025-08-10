<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

$plantID = $_GET['plantID'] ?? '';
$errors = [];
$success = '';
$plantName = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nutritionSetName = trim($_POST['nutritionSetName'] ?? '');
    $soilN = $_POST['soilN'] ?? '';
    $soilP = $_POST['soilP'] ?? '';
    $soilK = $_POST['soilK'] ?? '';
    $soilEC = $_POST['soilEC'] ?? '';
    $soilPH = $_POST['soilPH'] ?? '';
    $soilT = $_POST['soilT'] ?? '';
    $soilM = $_POST['soilM'] ?? '';
    $flowRate = $_POST['flowRate'] ?? '';

    // Validate required fields
    if (!$nutritionSetName) {
        $errors[] = 'Nutrition set name is required.';
    }

    if (!$errors) {
        $stmt = $conn->prepare('INSERT INTO plantnutrionneed (nutritionSetName, plantID, soilN, soilP, soilK, soilEC, soilPH, soilT, soilM, flowRate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        if ($stmt === null) {
            $errors[] = 'Invalid SQL statement. Please try again.';
        } else {
            // Debug: Show the final SQL statement
            $finalSQL = getFinalSQL('INSERT INTO plantnutrionneed (nutritionSetName, plantID, soilN, soilP, soilK, soilEC, soilPH, soilT, soilM, flowRate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 'siiiiidddd', [$nutritionSetName, $plantID, $soilN, $soilP, $soilK, $soilEC, $soilPH, $soilT, $soilM, $flowRate]);
            
            try {
                $stmt->bind_param('siiiiidddd', $nutritionSetName, $plantID, $soilN, $soilP, $soilK, $soilEC, $soilPH, $soilT, $soilM, $flowRate);
            } catch (Exception $e) {
                $errors[] = 'Failed to bind parameters: ' . $e->getMessage();
                $errors[] = 'Debug SQL: ' . $finalSQL;
            }
        }
        if ($stmt->execute()) {
            $success = 'Nutrition needs added successfully! <a href="view_nutrition.php?plantID=' . $plantID . '">View nutrition details</a> or <a href="plants.php">view all plants</a>.';
        } else {
            $errors[] = 'Failed to add nutrition needs. Please try again.';
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
    <title>Add Nutrition Needs - Smart Farming</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 600px; margin: 60px auto; background: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .plant-info { background: #e9ecef; padding: 1em; border-radius: 4px; margin-bottom: 1em; text-align: center; }
        form { display: flex; flex-direction: column; gap: 1em; }
        .form-group { display: flex; flex-direction: column; gap: 0.5em; }
        label { font-weight: bold; }
        input[type=text], input[type=number], input[type=float] { padding: 0.75em; border: 1px solid #ccc; border-radius: 4px; }
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
        <h2>Add Nutrition Needs</h2>
        
        <div class="plant-info">
            <strong>Plant:</strong> <?php echo htmlspecialchars($plantName); ?>
        </div>
        
        <?php if ($errors): ?>
            <div class="error">
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="add_nutrition.php?plantID=<?php echo $plantID; ?>">
            <div class="form-group">
                <label for="nutritionSetName">Nutrition Set Name *</label>
                <input type="text" id="nutritionSetName" name="nutritionSetName" required value="<?php echo htmlspecialchars($_POST['nutritionSetName'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="soilN">Soil Nitrogen (N)</label>
                <input type="number" id="soilN" name="soilN" value="<?php echo htmlspecialchars($_POST['soilN'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="soilP">Soil Phosphorus (P)</label>
                <input type="number" id="soilP" name="soilP" value="<?php echo htmlspecialchars($_POST['soilP'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="soilK">Soil Potassium (K)</label>
                <input type="number" id="soilK" name="soilK" value="<?php echo htmlspecialchars($_POST['soilK'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="soilEC">Soil Electrical Conductivity</label>
                <input type="number" id="soilEC" name="soilEC" value="<?php echo htmlspecialchars($_POST['soilEC'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="soilPH">Soil pH</label>
                <input type="number" id="soilPH" name="soilPH" step="0.1" value="<?php echo htmlspecialchars($_POST['soilPH'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="soilT">Soil Temperature (°C)</label>
                <input type="number" id="soilT" name="soilT" step="0.1" value="<?php echo htmlspecialchars($_POST['soilT'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="soilM">Soil Moisture (%)</label>
                <input type="number" id="soilM" name="soilM" step="0.1" value="<?php echo htmlspecialchars($_POST['soilM'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="flowRate">Flow Rate (L/min)</label>
                <input type="number" id="flowRate" name="flowRate" step="0.1" value="<?php echo htmlspecialchars($_POST['flowRate'] ?? ''); ?>">
            </div>
            
            <button type="submit">Add Nutrition Needs</button>
        </form>
        
        <div class="nav-links">
            <a href="plants.php">← Back to Plants</a> | 
            <a href="dashboard.php">Dashboard</a>
        </div>
    </div>
</body>
</html> 