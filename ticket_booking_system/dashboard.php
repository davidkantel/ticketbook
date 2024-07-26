<?php
session_start();
require_once('db_connection.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$message = '';

// Function to generate random transaction ID
function generateTransactionID() {
    return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);
}

// Process booking if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $route_id = $_POST['route_id'];
    $seat_number = $_POST['seat_number'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];
    
    // Check seat availability
    $check_seat_sql = "SELECT available_seats FROM routes WHERE route_id=?";
    $stmt = $conn->prepare($check_seat_sql);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['available_seats'] > 0) {
            // Proceed with booking
            $insert_booking_sql = "INSERT INTO bookings (user_id, route_id, seat_number) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_booking_sql);
            $stmt->bind_param("iii", $user_id, $route_id, $seat_number);
            
            if ($stmt->execute()) {
                // Update available seats
                $update_seats_sql = "UPDATE routes SET available_seats = available_seats - 1 WHERE route_id=?";
                $stmt = $conn->prepare($update_seats_sql);
                $stmt->bind_param("i", $route_id);
                $stmt->execute();
                
                // Insert payment details
                $booking_id = $stmt->insert_id; // Get the ID of the last inserted booking
                $insert_payment_sql = "INSERT INTO payments (booking_id, payment_method, amount) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_payment_sql);
                $stmt->bind_param("iss", $booking_id, $payment_method, $amount);
                
                if ($stmt->execute()) {
                    // Process payment via Mpesa API (Daraja)
                    if ($payment_method == 'mpesa') {
                        // Replace with your actual Daraja API credentials
                        $consumerKey = 'your_consumer_key';
                        $consumerSecret = 'your_consumer_secret';
                        $shortcode = 'your_shortcode'; // Mpesa Shortcode
                        $passkey = 'your_passkey'; // Lipa Na Mpesa Online Passkey
                        
                        // Generate transaction ID
                        $transactionID = generateTransactionID();
                        
                        // Construct the request payload
                        $payload = array(
                            "BusinessShortCode" => $shortcode,
                            "Password" => base64_encode($shortcode . $passkey . gmdate("YmdHis")),
                            "Timestamp" => gmdate("YmdHis"),
                            "TransactionType" => "CustomerPayBillOnline",
                            "Amount" => $amount,
                            "PartyA" => '254' . substr($_SESSION['phone_number'], 1), // User's phone number
                            "PartyB" => $shortcode,
                            "PhoneNumber" => '254' . substr($_SESSION['phone_number'], 1), // User's phone number
                            "CallBackURL" => "http://your-callback-url.com", // Replace with your callback URL
                            "AccountReference" => "Ticket Booking",
                            "TransactionDesc" => "Payment for Ticket Booking"
                        );
                        
                        // Initiate cURL session
                        $curl = curl_init();
                        
                        // Set the cURL options
                        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'); // Sandbox URL
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer ' . $access_token)); // Authorization header
                        curl_setopt($curl, CURLOPT_POST, true); // POST request
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload)); // POST data
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Receive server response
                        
                        // Execute cURL session
                        $response = curl_exec($curl);
                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        
                        // Close cURL session
                        curl_close($curl);
                        
                        // Check if request was successful
                        if ($httpcode == 200) {
                            // Process response from Mpesa API
                            $mpesa_response = json_decode($response, true);
                            
                            // Handle the response here
                            // Example: Log the response or update payment status in database
                            
                            $message = "Booking successful! Payment processing via Mpesa initiated.";
                        } else {
                            $message = "Error initiating payment via Mpesa. Please try again later.";
                        }
                    } else {
                        $message = "Booking and payment successful!";
                    }
                } else {
                    $message = "Error inserting payment details: " . $stmt->error;
                }
            } else {
                $message = "Error inserting booking details: " . $stmt->error;
            }
        } else {
            $message = "Seat not available!";
        }
    } else {
        $message = "Route not found!";
    }
}

// Define route options
$routes = array(
    1 => "Nairobi",
    2 => "Nakuru",
    3 => "Kisumu",
    4 => "Kiambu",
    5 => "Thika",
    6 => "Kisii"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ticket Booking System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-info">
    <div class="container mt-5 ">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
                <hr>
                <?php if (!empty($message)) : ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <h5 class="card-header text-center text-warning">Book Ticket</h5>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="form-group">
                                <label for="route_id">Select Route</label>
                                <select class="form-control" id="route_id" name="route_id" required>
                                    <option value="">Select Route</option>
                                    <?php foreach ($routes as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="seat_number">Seat Number</label>
                                <input type="number" class="form-control" id="seat_number" name="seat_number" required>
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="mpesa">Mpesa</option> <!-- Added Mpesa option -->
                                    <!-- Add more options as needed -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="text" class="form-control" id="amount" name="amount" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Book Ticket & Pay</button>
                        </form>
                    </div>
                </div>
                <hr>
                <p><a href="logout.php">Logout</a></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
