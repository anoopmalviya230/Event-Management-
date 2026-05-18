<?php
session_start();
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $user_id = $_SESSION['user_id'];

    // Booking Table 
    $sql = "INSERT INTO bookings (user_id, event_id) VALUES ('$user_id', '$event_id')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Booking Successful!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>