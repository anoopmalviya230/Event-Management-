<?php
include 'db.php';
session_start();


$user_name = "";
$user_role = "";
if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $u_sql = "SELECT username, role FROM users WHERE user_id = '$id'";
    $u_result = $conn->query($u_sql);
    if ($u_result->num_rows > 0) {
        $u_row = $u_result->fetch_assoc();
        $user_name = $u_row['username'];
        $user_role = $u_row['role'];
    }
}

// Search Logic
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM events WHERE title LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM events";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .hero-section {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 50px 0;
            text-align: center;
            margin-bottom: 30px;
        }
        .card {
            transition: transform 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">🎉 EventManager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>

                    <?php if(isset($_SESSION['user_id'])) { ?>
                        
                        <?php if($user_role == 'admin') { ?>
                            <li class="nav-item"><a class="nav-link text-warning" href="admin.php">Admin Panel</a></li>
                        <?php } ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                👤 <?php echo $user_name; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                            </ul>
                        </li>

                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="btn btn-primary ms-2" href="register.php">Sign Up</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container">
            <h1>Find & Book Amazing Events</h1>
            <p>Concerts, Tech Talks, Workshops & More</p>
            
            <form method="GET" class="d-flex justify-content-center mt-4">
                <input type="text" name="search" class="form-control w-50 me-2" placeholder="Search events..." value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-light text-primary fw-bold">Search</button>
            </form>
        </div>
    </div>

    <div class="container mb-5">
        <h2 class="text-center mb-4">Upcoming Events</h2>
        <div class="row">
            <?php if($result->num_rows > 0) { ?>
                <?php while($row = $result->fetch_assoc()) { ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo $row['title']; ?></h5>
                                <p class="card-text text-muted"><?php echo substr($row['description'], 0, 100); ?>...</p>
                                <ul class="list-unstyled">
                                    <li>📅 <strong>Date:</strong> <?php echo $row['event_date']; ?></li>
                                    <li>📍 <strong>Loc:</strong> <?php echo isset($row['location']) ? $row['location'] : 'TBA'; ?></li>
                                    <li>💰 <strong>Price:</strong> ₹<?php echo $row['price']; ?></li>
                                </ul>
                                
                                <div class="d-grid gap-2">
                                    <?php if(isset($_SESSION['user_id'])) { ?>
                                        <a href="book.php?event_id=<?php echo $row['event_id']; ?>" class="btn btn-success">Book Now</a>
                                    <?php } else { ?>
                                        <a href="login.php" class="btn btn-outline-primary">Login to Book</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12 text-center text-muted">No events found matching your search.</div>
            <?php } ?>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto" id="contact">
        <p class="mb-0">Contact Us: support@eventmanager.com | +91 7999602620</p>
        <small>&copy; 2025 EventManager</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>