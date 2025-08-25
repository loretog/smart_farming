<?php
require_once 'db.php';

// Set headers to allow cross-origin requests if needed
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Function to send JSON response
function sendResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Check if this is a GET request (from Arduino)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get parameters from query string (Arduino sends these)
    $soilSensorID = isset($_GET['SoilSensorID']) ? (int)$_GET['SoilSensorID'] : null;
    $soilN = isset($_GET['SoilN']) ? (float)$_GET['SoilN'] : null;
    $soilP = isset($_GET['SoilP']) ? (float)$_GET['SoilP'] : null;
    $soilK = isset($_GET['SoilK']) ? (float)$_GET['SoilK'] : null;
    $soilEC = isset($_GET['SoilEC']) ? (float)$_GET['SoilEC'] : null;
    $soilPH = isset($_GET['SoilPH']) ? (float)$_GET['SoilPH'] : null;
    $soilT = isset($_GET['SoilT']) ? (float)$_GET['SoilT'] : null;
    $soilMois = isset($_GET['SoilMois']) ? (float)$_GET['SoilMois'] : null;
    $flowRate = isset($_GET['FlowRate']) ? (float)$_GET['FlowRate'] : null;
    
    // Validate required fields
    if (!$soilSensorID) {
        sendResponse(false, 'SoilSensorID is required');
    }
    
    // Check if sensor exists
    $checkStmt = $conn->prepare('SELECT soilSensorID FROM sensorinfo WHERE soilSensorID = ?');
    $checkStmt->bind_param('i', $soilSensorID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        sendResponse(false, 'Sensor ID ' . $soilSensorID . ' does not exist');
    }
    $checkStmt->close();
    
    // Validate data ranges
    if ($soilPH !== null && ($soilPH < 0 || $soilPH > 14)) {
        sendResponse(false, 'Soil pH must be between 0 and 14. Received: ' . $soilPH);
    }
    
    if ($soilMois !== null && ($soilMois < 0 || $soilMois > 100)) {
        sendResponse(false, 'Soil moisture must be between 0% and 100%. Received: ' . $soilMois . '%');
    }
    
    // Set current timestamp
    $dateTime = date('Y-m-d H:i:s');
    
    // Insert data into database
    $stmt = $conn->prepare('INSERT INTO sensordata (SoilSensorID, SoilN, SoilP, SoilK, SoilEC, SoilPH, SoilT, SoilMois, FlowRate, DateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
    // Handle NULL values properly
    $bindN = $soilN ?? 0;
    $bindP = $soilP ?? 0;
    $bindK = $soilK ?? 0;
    $bindEC = $soilEC ?? 0;
    $bindPH = $soilPH ?? 0.0;
    $bindT = $soilT ?? 0.0;
    $bindMois = $soilMois ?? 0.0;
    $bindFlow = $flowRate ?? 0.0;
    
    $stmt->bind_param('iiiiidddds', 
        $soilSensorID, 
        $bindN, 
        $bindP, 
        $bindK, 
        $bindEC, 
        $bindPH, 
        $bindT, 
        $bindMois, 
        $bindFlow, 
        $dateTime
    );
    
    if ($stmt->execute()) {
        $insertId = $conn->insert_id;
        sendResponse(true, 'Sensor data received and stored successfully', [
            'id' => $insertId,
            'sensor_id' => $soilSensorID,
            'timestamp' => $dateTime,
            'values' => [
                'N' => $soilN,
                'P' => $soilP,
                'K' => $soilK,
                'EC' => $soilEC,
                'pH' => $soilPH,
                'Temperature' => $soilT,
                'Moisture' => $soilMois,
                'FlowRate' => $flowRate
            ]
        ]);
    } else {
        sendResponse(false, 'Failed to store sensor data: ' . $conn->error);
    }
    
    $stmt->close();
}

// If not GET request, send error
sendResponse(false, 'Only GET requests are accepted for sensor data');
?>
