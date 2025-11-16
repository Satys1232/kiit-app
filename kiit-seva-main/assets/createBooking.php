<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$student_id = $_SESSION['id'];
$teacher_id = mysqli_real_escape_string($conn, $_POST['teacher_id'] ?? '');
$booking_date = mysqli_real_escape_string($conn, $_POST['booking_date'] ?? '');
$time_slot = mysqli_real_escape_string($conn, $_POST['time_slot'] ?? '');
$purpose = mysqli_real_escape_string($conn, $_POST['purpose'] ?? '');

if (empty($teacher_id) || empty($booking_date) || empty($time_slot) || empty($purpose)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Verify teacher exists
$verify_sql = "SELECT id FROM teachers WHERE id = '$teacher_id'";
$verify_result = mysqli_query($conn, $verify_sql);

if (!$verify_result || mysqli_num_rows($verify_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid teacher selected']);
    mysqli_close($conn);
    exit();
}

// Check if the time slot is already booked
$check_sql = "SELECT s_no FROM bookings 
              WHERE teacher_id = '$teacher_id' 
              AND booking_date = '$booking_date' 
              AND time_slot = '$time_slot' 
              AND status != 'cancelled'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo json_encode(['success' => false, 'message' => 'This time slot is already booked']);
    mysqli_close($conn);
    exit();
}

// Insert the booking
$insert_sql = "INSERT INTO bookings (student_id, teacher_id, booking_date, time_slot, purpose, status) 
               VALUES ('$student_id', '$teacher_id', '$booking_date', '$time_slot', '$purpose', 'pending')";

if (mysqli_query($conn, $insert_sql)) {
    echo json_encode(['success' => true, 'message' => 'Booking request submitted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>