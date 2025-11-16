<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$student_id = mysqli_real_escape_string($conn, $_SESSION['id']);

// Fetch bookings for this student
$sql = "SELECT b.*, 
               t.fname as teacher_fname, 
               t.lname as teacher_lname, 
               t.subject,
               t.email as teacher_email
        FROM bookings b
        LEFT JOIN teachers t ON b.teacher_id = t.id
        WHERE b.student_id = '$student_id'
        ORDER BY b.booking_date DESC, b.time_slot ASC";

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
        'teacher_name' => $row['teacher_fname'] . ' ' . $row['teacher_lname'],
        'subject' => $row['subject'],
        'teacher_email' => $row['teacher_email']
    ];
}

echo json_encode(['success' => true, 'bookings' => $bookings]);
mysqli_close($conn);
?>