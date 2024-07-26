<?php
session_start();
require_once('db_connection.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $route_id = $_POST['route_id'];
    $seat_number = $_POST['seat_number'];
    
    // Check if seat is available
    $check_seat_sql = "SELECT available_seats FROM routes WHERE route_id='$route_id'";
    $result = $conn->query($check_seat_sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['available_seats'] > 0) {
            // Proceed with booking
            $sql = "INSERT INTO bookings (user_id, route_id, seat_number) VALUES ('$user_id', '$route_id', '$seat_number')";
            
            if ($conn->query($sql) === TRUE) {
                // Update available seats
                $update_seats_sql = "UPDATE routes SET available_seats = available_seats - 1 WHERE route_id='$route_id'";
                $conn->query($update_seats_sql);
                
                echo "Booking successful!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Seat not available!";
        }
    } else {
        echo "Route not found!";
    }
}

$conn->close();
?>
