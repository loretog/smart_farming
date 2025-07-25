<?php
// db.php - Database connection
//$host = 'http://localhost:8080/';
//$db   = 'smart_agri'; // Change to your DB name
//$user = 'root';         // Change to your DB user
//$pass = 'rootpassword';             // Change to your DB password

$servername = "mysql"; // This is the service name of your MySQL container
$username = "root";    // As defined in MYSQL_USER
$password = "rootpassword"; // As defined in MYSQL_PASSWORD
$dbname = "smart_agri";    // As defined in MYSQL_DATABASE
$port = "3306";        // The default MySQL port inside the container

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname, $port);

//$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}