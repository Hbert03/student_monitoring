<?php
include('database.php');


$student_id = $_POST['student_id'];


date_default_timezone_set('Asia/Manila');
$current_time = date('H:i:s');
$current_date = date('Y-m-d');
$status = '';


$attendance_windows = [
    'morning_in' => ['08:00:00', '08:00:01'], // Log in by 8 AM
    'morning_out' => ['12:00:00', '12:59:59'], // Out by 12 PM
    'afternoon_in' => ['13:00:00', '13:00:01'], // Log in by 1 PM
    'afternoon_out' => ['16:00:00', '22:00:00'], // Out between 4 PM and 10 PM
];


if ($current_time <= $attendance_windows['morning_in'][0]) {
    $status = 'IN';
} elseif ($current_time > $attendance_windows['morning_in'][0] && $current_time < $attendance_windows['morning_out'][0]) {
    $status = 'LATE'; 
} elseif ($current_time >= $attendance_windows['morning_out'][0] && $current_time <= $attendance_windows['morning_out'][1]) {
    $status = 'OUT'; 
} elseif ($current_time >= $attendance_windows['afternoon_in'][0] && $current_time < $attendance_windows['afternoon_in'][1]) {
    $status = 'LATE'; 
} elseif ($current_time >= $attendance_windows['afternoon_out'][0] && $current_time <= $attendance_windows['afternoon_out'][1]) {
    $status = 'OUT'; 
} else {
    $status = 'Invalid attendance time.'; 
}


if ($status != 'Invalid attendance time.') {
    $sql = "INSERT INTO attendance (date, student_id, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $current_date, $student_id, $status);

    if ($stmt->execute()) {
        echo "Attendance recorded successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
} else {
    echo $status;
}

$conn->close();
?>
