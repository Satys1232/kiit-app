<?php
session_start();
include("config.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';

function buildTeachersFromResult($result, $withSlots = true) {
    $teachers = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $teachers[] = [
            'id' => $row['id'],
            'fname' => $row['fname'],
            'lname' => $row['lname'],
            'subject' => $row['subject'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'chamber_no' => $withSlots ? ($row['chamber_no'] ?? 'Not set') : 'Not set',
            'bio' => $withSlots ? ($row['bio'] ?? '') : '',
            'available_slots' => $withSlots ? ($row['available_slots'] ?? '{}') : '{}'
        ];
    }
    return $teachers;
}

/* -------- First try: with teacher_slots (if table exists) -------- */
$sql = "SELECT 
            t.id,
            t.fname,
            t.lname,
            t.subject,
            t.email,
            t.phone,
            ts.chamber_no,
            ts.bio,
            ts.available_slots
        FROM teachers t
        LEFT JOIN teacher_slots ts ON t.id = ts.teacher_id";

if (!empty($search)) {
    $sql .= " WHERE (t.fname LIKE '%$search%' 
              OR t.lname LIKE '%$search%' 
              OR t.subject LIKE '%$search%'
              OR CONCAT(t.fname, ' ', t.lname) LIKE '%$search%')";
}

$sql .= " ORDER BY t.fname, t.lname";

$result = mysqli_query($conn, $sql);

if (!$result) {
    $error = mysqli_error($conn);

    // If teacher_slots table doesn't exist → fallback to only teachers table
    if (strpos($error, "teacher_slots") !== false) {
        $sql2 = "SELECT id, fname, lname, subject, email, phone FROM teachers";

        if (!empty($search)) {
            $sql2 .= " WHERE (fname LIKE '%$search%' 
                      OR lname LIKE '%$search%' 
                      OR subject LIKE '%$search%'
                      OR CONCAT(fname, ' ', lname) LIKE '%$search%')";
        }

        $sql2 .= " ORDER BY fname, lname";

        $result2 = mysqli_query($conn, $sql2);

        if (!$result2) {
            echo json_encode(['success' => false, 'message' => 'Database query failed: ' . mysqli_error($conn)]);
            mysqli_close($conn);
            exit();
        }

        $teachers = buildTeachersFromResult($result2, false);
        echo json_encode(['success' => true, 'teachers' => $teachers]);
        mysqli_close($conn);
        exit();
    }

    // Any other DB error
    echo json_encode(['success' => false, 'message' => 'Database query failed: ' . $error]);
    mysqli_close($conn);
    exit();
}

// Normal path: table exists (even if it has 0 rows)
$teachers = buildTeachersFromResult($result, true);
echo json_encode(['success' => true, 'teachers' => $teachers]);
mysqli_close($conn);
?>