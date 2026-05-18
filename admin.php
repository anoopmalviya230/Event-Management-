<?php
session_start();
include 'db.php';

// 1. Check Admin Access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Add Event Logic (Updated with Location)
$msg = "";
if (isset($_POST['add_event'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $location = $_POST['location']; // New
    $price = $_POST['price'];
    $desc = $_POST['desc'];

    // SQL Query Updated
    $sql = "INSERT INTO events (title, event_date, location, price, description) VALUES ('$title', '$date', '$location', '$price', '$desc')";
    
    if ($conn->query($sql)) {
        $msg = "<div class='alert alert-success'>Event Added Successfully! ✅</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// 3. Delete Logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM events WHERE event_id=$id");
    header("Location: admin.php");
}

// 4. Stats & Data Fetching
$total_events = $conn->query("SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
$revenue_data = $conn->query("SELECT SUM(events.price) as total FROM bookings JOIN events ON bookings.event_id = events.event_id")->fetch_assoc();
$total_revenue = $revenue_data['total'] ? $revenue_data['total'] : 0;

$booking_sql = "SELECT users.username, events.title, events.price, bookings.booking_date 
                FROM bookings 
                JOIN users ON bookings.user_id = users.user_id 
                JOIN events ON bookings.event_id = events.event_id 
                ORDER BY bookings.booking_date DESC";
$bookings = $conn->query($booking_sql);

$events_list = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .card-box { border-radius: 10px; border: none; transition: 0.3s; color: white; }
        .card-box:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
        .bg-gradient-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark sticky-top px-4 shadow">
        <a class="navbar-brand fw-bold" href="#">⚙️ Admin Panel</a>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2">Go to Website</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container my-4">
        
        <div class="row mb-4">
            <div class="col-md-4"><div class="card card-box bg-gradient-primary p-3 mb-3"><h3><?php echo $total_events; ?></h3><span>Total Events</span></div></div>
            <div class="col-md-4"><div class="card card-box bg-gradient-success p-3 mb-3"><h3><?php echo $total_bookings; ?></h3><span>Total Bookings</span></div></div>
            <div class="col-md-4"><div class="card card-box bg-gradient-warning p-3 mb-3"><h3>₹<?php echo $total_revenue; ?></h3><span>Total Revenue</span></div></div>
        </div>

        <?php echo $msg; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold text-primary">➕ Add New Event</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Event Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Location 📍</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Mumbai Hall" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (₹)</label>
                                <input type="number" name="price" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="desc" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_event" class="btn btn-primary w-100">Create Event</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold text-dark">🛠 Manage Events</div>
                    <ul class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        <?php while($e = $events_list->fetch_assoc()) { ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong><?php echo $e['title']; ?></strong><br>
                                    <small class="text-muted">📍 <?php echo $e['location']; ?></small>
                                </span>
                                <div>
                                    <a href="edit_event.php?id=<?php echo $e['event_id']; ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                                    <a href="admin.php?delete=<?php echo $e['event_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">🗑</a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold text-success">📋 Recent Bookings</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light"><tr><th>User</th><th>Event</th><th>Price</th><th>Date</th></tr></thead>
                                <tbody>
                                    <?php while($row = $bookings->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo $row['title']; ?></td>
                                            <td>₹<?php echo $row['price']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($row['booking_date'])); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>