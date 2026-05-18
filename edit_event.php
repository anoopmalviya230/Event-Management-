<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin' || !isset($_GET['id'])) {
    header("Location: admin.php"); exit();
}

$id = $_GET['id'];
$msg = "";

if (isset($_POST['update_event'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $location = $_POST['location']; // New
    $price = $_POST['price'];
    $desc = $_POST['desc'];

    $sql = "UPDATE events SET title='$title', event_date='$date', location='$location', price='$price', description='$desc' WHERE event_id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Event Updated Successfully!'); window.location.href='admin.php';</script>";
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

$row = $conn->query("SELECT * FROM events WHERE event_id = '$id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head><title>Edit Event</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></head>
<body class="bg-light container mt-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark fw-bold">✏️ Edit Event</div>
        <div class="card-body">
            <?php echo $msg; ?>
            <form method="post">
                <div class="mb-3"><label>Event Title</label><input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>" required></div>
                <div class="mb-3"><label>Date</label><input type="date" name="date" class="form-control" value="<?php echo $row['event_date']; ?>" required></div>
                
                <div class="mb-3"><label>Location 📍</label><input type="text" name="location" class="form-control" value="<?php echo $row['location']; ?>" required></div>
                
                <div class="mb-3"><label>Price (₹)</label><input type="number" name="price" class="form-control" value="<?php echo $row['price']; ?>" required></div>
                <div class="mb-3"><label>Description</label><textarea name="desc" class="form-control" rows="4"><?php echo $row['description']; ?></textarea></div>
                
                <button type="submit" name="update_event" class="btn btn-primary w-100">Update Event</button>
                <a href="admin.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>