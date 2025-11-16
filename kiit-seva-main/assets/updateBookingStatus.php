<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit(); 
}

$booking_id = mysqli_real_escape_string($conn, $_POST['booking_id'] ?? '');
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');
$notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

if (empty($booking_id) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$teacher_id = mysqli_real_escape_string($conn, $_SESSION['id']);

// Verify this booking belongs to this teacher
$check_sql = "SELECT s_no FROM bookings WHERE s_no = '$booking_id' AND teacher_id = '$teacher_id'";
$check_result = mysqli_query($conn, $check_sql);

if (!$check_result || mysqli_num_rows($check_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found or unauthorized']);
    mysqli_close($conn);
    exit();
}

// Update the booking status
$update_sql = "UPDATE bookings 
               SET status = '$status', notes = '$notes' 
               WHERE s_no = '$booking_id'";

if (mysqli_query($conn, $update_sql)) {
    $action = ($status == 'confirmed') ? 'accepted' : $status;
    echo json_encode(['success' => true, 'message' => "Booking $action successfully"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>