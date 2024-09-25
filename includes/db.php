<?php
//$servername = "localhost";  // Use 'localhost' for local development
//$username = "root";         // Default username for XAMPP is 'root'
//$password = "";             // Leave password empty for XAMPP
//$dbname = "attendance_db";  // Name of your database

// Create connection
//$conn = new mysqli($servername, $username, //$password, $dbname);

// Check connection
//if ($conn->connect_error) {
  //  die("Connection failed: " . $conn->connect_error);
//}
?>


<?php
// includes/db.php

$host = 'localhost';
$dbname = 'attendance_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
