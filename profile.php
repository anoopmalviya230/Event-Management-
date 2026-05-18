<?php
include 'db.php';
session_start();

// check user login or not
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// user details fetch
$user_sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();

// user history
$booking_sql = "SELECT events.title, events.event_date, events.price, bookings.booking_date 
                FROM bookings 
                JOIN events ON bookings.event_id = events.event_id 
                WHERE bookings.user_id = '$user_id'";
$booking_result = $conn->query($booking_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    
    <a href="index.php" class="btn btn-secondary mb-3">← Back to Home</a>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>My Profile</h4>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo $user_data['username']; ?></p>
                    <p><strong>Email:</strong> <?php echo $user_data['email']; ?></p>
                    <p><strong>Role:</strong> <span class="badge bg-info"><?php echo $user_data['role']; ?></span></p>
                    <a href="logout.php" class="btn btn-danger w-100">Logout</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h3>My Bookings</h3>
            <?php if($booking_result->num_rows > 0) { ?>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Event Name</th>
                            <th>Event Date</th>
                            <th>Price</th>
                            <th>Booked On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $booking_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['event_date']; ?></td>
                                <td>₹<?php echo $row['price']; ?></td>
                                <td><?php echo date('d-M-Y', strtotime($row['booking_date'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="alert alert-info">You haven't booked any events yet.</div>
            <?php } ?>
        </div>
    </div>

</body>
</html>