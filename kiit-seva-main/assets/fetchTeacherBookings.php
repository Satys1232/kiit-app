<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$teacher_id = mysqli_real_escape_string($conn, $_SESSION['id']);

// Fetch bookings for this teacher
$sql = "SELECT b.*, 
               s.fname as student_fname, 
               s.lname as student_lname, 
               s.email as student_email, 
               s.phone as student_phone,
               s.class,
               s.section
        FROM bookings b
        LEFT JOIN students s ON b.student_id = s.id
        WHERE b.teacher_id = '$teacher_id'
        ORDER BY b.booking_date ASC, b.time_slot ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    mysqli_close($conn);
    exit();
}

$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bookings[] = [
        's_no' => $row['s_no'],
        'student_id' => $row['student_id'],
        'teacher_id' => $row['teacher_id'],
        'booking_date' => $row['booking_date'],
        'time_slot' => $row['time_slot'],
        'purpose' => $row['purpose'],
        'status' => $row['status'],
        'notes' => $row['notes'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at'],
        'student_name' => $row['student_fname'] . ' ' . $row['student_lname'],
        'student_email' => $row['student_email'],
        'student_phone' => $row['student_phone'],
        'class' => $row['class'],
        'section' => $row['section']
    ];
}

echo json_encode(['success' => true, 'bookings' => $bookings]);
mysqli_close($conn);
?>