<?php
$db_path = __DIR__ . '/bookings.db';

try {
    $pdo = new PDO("sqlite:$db_path");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS teacher_slots (
        s_no INTEGER PRIMARY KEY AUTOINCREMENT,
        teacher_id VARCHAR(40) NOT NULL UNIQUE,
        chamber_no VARCHAR(50),
        available_slots TEXT,
        bio VARCHAR(500)
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        s_no INTEGER PRIMARY KEY AUTOINCREMENT,
        student_id VARCHAR(40) NOT NULL,
        teacher_id VARCHAR(40) NOT NULL,
        booking_date DATE NOT NULL,
        time_slot VARCHAR(50) NOT NULL,
        purpose TEXT NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        notes VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $check = $pdo->query("SELECT COUNT(*) FROM teacher_slots WHERE teacher_id = 'T1718791191'")->fetchColumn();
    if ($check == 0) {
        $availableSlots = json_encode([
            "monday" => ["09:00-10:00", "14:00-15:00", "16:00-17:00"],
            "tuesday" => ["10:00-11:00", "15:00-16:00"],
            "wednesday" => ["09:00-10:00", "14:00-15:00"],
            "thursday" => ["10:00-11:00", "15:00-16:00"],
            "friday" => ["09:00-10:00", "14:00-15:00"]
        ]);
        
        $stmt = $pdo->prepare("INSERT INTO teacher_slots (teacher_id, chamber_no, available_slots, bio) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            'T1718791191',
            'Room 101',
            $availableSlots,
            'Experienced Hindi teacher with 5 years of teaching experience. Available for academic consultations and doubt clearing sessions.'
        ]);
        
        echo "Sample teacher availability added successfully!\n";
    } else {
        echo "Teacher availability already exists.\n";
    }
    
    echo "Bookings database initialized successfully!\n";
    echo "Database location: $db_path\n";
    
} catch(PDOException $e) {
    die("Database initialization failed: " . $e->getMessage() . "\n");
}
?>