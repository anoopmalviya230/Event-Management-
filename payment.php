<?php
session_start();
include 'db.php';

// 1. Razorpay Keys 
$keyId = "rzp_test_RoeT2SJ8JrHmnx";      
$keySecret = "1w2B3BrxzLklqQfeNn6SjYOU"; 

// 2. Check In
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// 3. event user details
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $price = $_GET['price'];
    
    // event name
    $event_sql = "SELECT title FROM events WHERE event_id = '$event_id'";
    $event_row = $conn->query($event_sql)->fetch_assoc();
    $title = $event_row['title'];

    
    $user_id = $_SESSION['user_id'];
    $user_sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $user_row = $conn->query($user_sql)->fetch_assoc();
    
} else {
    header("Location: index.php"); exit();
}


$amount = $price * 100; 

$url = "https://api.razorpay.com/v1/orders";
$data = [
    'receipt'         => 'order_rcptid_' . time(),
    'amount'          => $amount,
    'currency'        => 'INR',
    'payment_capture' => 1
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ':' . $keySecret);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$order = json_decode($response, true);
$razorpayOrderId = $order['id']; 


if (!isset($order['id'])) {
    die("Error creating Razorpay Order. Check API Keys.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay with Razorpay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .pay-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 400px; }
    </style>
</head>
<body>

    <div class="pay-card">
        <h3 class="text-primary mb-3">Confirm Booking</h3>
        <p class="text-muted">Event: <strong><?php echo $title; ?></strong></p>
        <h2 class="mb-4">₹<?php echo $price; ?></h2>

        <button id="rzp-button1" class="btn btn-primary w-100 py-2 fs-5">Scan QR & Pay Now</button>
        <a href="index.php" class="btn btn-link mt-3">Cancel</a>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
    var options = {
        "key": "<?php echo $keyId; ?>", 
        "amount": "<?php echo $amount; ?>", 
        "currency": "INR",
        "name": "Event Manager",
        "description": "Booking for <?php echo $title; ?>",
        "order_id": "<?php echo $razorpayOrderId; ?>", 
        "handler": function (response){
            
            
            window.location.href = "verify_payment.php?payment_id=" + response.razorpay_payment_id + 
                                   "&order_id=" + response.razorpay_order_id + 
                                   "&signature=" + response.razorpay_signature + 
                                   "&event_id=<?php echo $event_id; ?>";
        },
        "prefill": {
            "name": "<?php echo $user_row['username']; ?>",
            "email": "<?php echo $user_row['email']; ?>"
        },
        "theme": {
            "color": "#3399cc"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button1').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
    </script>

</body>
</html>