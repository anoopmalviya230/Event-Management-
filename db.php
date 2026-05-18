<?php
// db.php
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "event_db";

// Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>