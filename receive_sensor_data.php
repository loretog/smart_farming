<?php
/**
 * receive_sensor_data.php
 * 
 * This file accepts sensor data from IoT devices via POST/GET requests
 * and saves the data to organized text files for debugging and analysis.
 * 
 * Usage:
 * - POST JSON data: POST to this file with JSON content
 * - GET parameters: GET request with query parameters
 * - Raw POST data: POST with any content type
 */

// Set headers to allow cross-origin requests (useful for IoT devices)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Create logs directory if it doesn't exist
$logsDir = 'sensor_logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

// Create daily subdirectory for better organization
$today = date('Y-m-d');
$dailyDir = $logsDir . '/' . $today;
if (!is_dir($dailyDir)) {
    mkdir($dailyDir, 0755, true);
}

// Initialize response array
$response = [
    'status' => 'success',
    'message' => 'Data received successfully',
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'data_received' => null,
    'file_saved' => null
];

try {
    // Get all incoming data (GET, POST, COOKIE)
    $dataToLog = $_REQUEST;
    
    if (!empty($dataToLog)) {
        $response['data_received'] = 'Data received via ' . $_SERVER['REQUEST_METHOD'];
        
        // Generate filename based on timestamp
        $fileName = 'sensor_data_' . date('H-i-s') . '.txt';
        
        // Add metadata to the data
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'remote_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'unknown',
            'data' => $dataToLog
        ];
        
        // Convert to readable format
        $logContent = "=== SENSOR DATA LOG ===\n";
        $logContent .= "Timestamp: " . $logData['timestamp'] . "\n";
        $logContent .= "Remote IP: " . $logData['remote_ip'] . "\n";
        $logContent .= "User Agent: " . $logData['user_agent'] . "\n";
        $logContent .= "Request Method: " . $logData['request_method'] . "\n";
        $logContent .= "Content Type: " . $logData['content_type'] . "\n";
        $logContent .= "Data Received:\n";
        $logContent .= json_encode($logData['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $logContent .= "\n\n=== END LOG ===\n";
        
        // Save to file
        $filePath = $dailyDir . '/' . $fileName;
        if (file_put_contents($filePath, $logContent) !== false) {
            $response['file_saved'] = $filePath;
            $response['message'] = 'Data received and saved successfully';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to save data to file';
            $response['file_saved'] = 'Failed to save';
        }
    } else {
        $response['status'] = 'warning';
        $response['message'] = 'No data received';
        $response['data_received'] = 'Empty request';
    }
    
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Exception occurred: ' . $e->getMessage();
    $response['data_received'] = 'Exception during processing';
}

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Also log the response to a separate log file for debugging
$responseLog = date('Y-m-d H:i:s') . " - " . json_encode($response) . "\n";
$responseLogFile = $logsDir . '/api_responses.log';
file_put_contents($responseLogFile, $responseLog, FILE_APPEND | LOCK_EX);
?>
