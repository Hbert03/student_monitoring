<?php
include ('database.php');

if (isset($_GET['task']) && $_GET['task'] === 'markAbsent') {

    // Get the list of all students
    $sql_get_students = "SELECT student_id FROM student";  
    $stmt_get_students = $conn->prepare($sql_get_students);
    $stmt_get_students->execute();
    $result = $stmt_get_students->get_result();
    
    // Loop through each student and check attendance
    while ($row = $result->fetch_assoc()) {
        $student_id = $row['student_id'];
        markAbsentIfNoScan($student_id, $conn);
    }
}

// Function to mark the student as absent if no attendance record is found
function markAbsentIfNoScan($student_id, $conn) {
    $current_date = date('Y-m-d');  

    // Check if there is an attendance record for this student today
    $sql_check_attendance = "SELECT * FROM attendance WHERE student_id = ? AND DATE(date) = ?";
    $stmt_check_attendance = $conn->prepare($sql_check_attendance);
    $stmt_check_attendance->bind_param("is", $student_id, $current_date);
    $stmt_check_attendance->execute();
    $result_check_attendance = $stmt_check_attendance->get_result();
    
    // If no attendance record exists for today, mark the student as absent
    if ($result_check_attendance->num_rows == 0) {
        // Insert the student as absent for today
        $sql_absent = "INSERT INTO attendance (student_id, status, date) VALUES (?, 'ABSENT', NOW())";
        $stmt_absent = $conn->prepare($sql_absent);
        $stmt_absent->bind_param("i", $student_id);
        
        if ($stmt_absent->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Student marked as absent.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error marking student as absent.']);
        }
        $stmt_absent->close();
    } else {
        echo json_encode(['status' => 'info', 'message' => 'Attendance already recorded for today.']);
    }

    $stmt_check_attendance->close();
}
?>
