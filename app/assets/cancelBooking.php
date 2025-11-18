<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$booking_id = mysqli_real_escape_string($conn, $_POST['booking_id'] ?? '');

if (empty($booking_id)) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit();
}

$student_id = mysqli_real_escape_string($conn, $_SESSION['id']);

// Verify this booking belongs to this student
$check_sql = "SELECT s_no FROM bookings WHERE s_no = '$booking_id' AND student_id = '$student_id'";
$check_result = mysqli_query($conn, $check_sql);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found or unauthorized']);
    mysqli_close($conn);
    exit();
}

// Cancel the booking
$update_sql = "UPDATE bookings 
               SET status = 'cancelled', notes = 'Cancelled by student' 
               WHERE s_no = '$booking_id'";

if (mysqli_query($conn, $update_sql)) {
    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>