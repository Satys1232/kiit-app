CREATE TABLE IF NOT EXISTS `teacher_slots` (
  `s_no` INT(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` VARCHAR(64) NOT NULL,
  `chamber_no` VARCHAR(50) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `available_slots` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`s_no`),
  UNIQUE KEY `unique_teacher` (`teacher_id`),
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `bookings` (
  `s_no` INT(11) NOT NULL AUTO_INCREMENT,
  `student_id` VARCHAR(64) NOT NULL,
  `teacher_id` VARCHAR(64) NOT NULL,
  `booking_date` DATE NOT NULL,
  `time_slot` VARCHAR(50) NOT NULL,
  `purpose` TEXT NOT NULL,
  `status` VARCHAR(20) DEFAULT 'pending',
  `notes` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`s_no`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
  KEY `idx_teacher_date` (`teacher_id`, `booking_date`),
  KEY `idx_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `teacher_slots` (teacher_id, chamber_no, bio, available_slots)
SELECT 
  id,
  'Chamber TBD',
  'Available for consultation',
  '{"monday":["9:00 AM","10:00 AM","11:00 AM","2:00 PM","3:00 PM"],"tuesday":["9:00 AM","10:00 AM","11:00 AM","2:00 PM","3:00 PM"],"wednesday":["9:00 AM","10:00 AM","11:00 AM","2:00 PM","3:00 PM"],"thursday":["9:00 AM","10:00 AM","11:00 AM","2:00 PM","3:00 PM"],"friday":["9:00 AM","10:00 AM","11:00 AM","2:00 PM","3:00 PM"]}'
FROM teachers
WHERE id NOT IN (SELECT teacher_id FROM teacher_slots);
