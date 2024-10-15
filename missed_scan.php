<?php
include('database.php');


$apiKey = 'f770208e20af697387421fcf32ba90da';

// Define the time windows
$morning_window_end = '12:00:00';
$afternoon_start = '13:00:00';
$afternoon_window_end = '17:00:00'; 

// Get the current time and date
date_default_timezone_set('Asia/Manila');
$current_time = date('H:i:s');
$current_date = date('Y-m-d');

// Query to find students who haven't scanned at all today
$missed_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, 
           CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
    FROM student_section ss
    INNER JOIN student s ON ss.student_id = s.student_id
    INNER JOIN parent p ON s.parent_id = p.parent_id
    WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
    AND s.student_id NOT IN (SELECT student_id FROM attendance WHERE date = ?)";
    
$missed_stmt = $conn->prepare($missed_sql);
$missed_stmt->bind_param("s", $current_date);
$missed_stmt->execute();
$missed_result = $missed_stmt->get_result();

// Loop through each student who missed both morning and afternoon scans
while ($missed_row = $missed_result->fetch_assoc()) {
    $missed_parent_name = $missed_row['parent_name'];
    $missed_parent_mobile = $missed_row['parent_mobile'];
    $missed_student = $missed_row['student'];

    // Format mobile number to international format
    if (strpos($missed_parent_mobile, '0') === 0) {
        $missed_parent_mobile = '+63' . substr($missed_parent_mobile, 1); 
    }

   
    $missed_message = '';
    if ($current_time <= $morning_window_end) {
        $missed_message = "Good day! $missed_parent_name, your student $missed_student is absent in the morning class.";
    } elseif ($current_time <= $afternoon_window_end) {
        $missed_message = "Good day! $missed_parent_name, your student $missed_student is absent in the afternoon class.";
    } else {
        $missed_message = "Good day! $missed_parent_name, your student $missed_student is absent for today's class.";
    }

    // Send SMS for the missed scans
    if ($missed_message) {
        $ch = curl_init();
        $missed_parameters = array(
            'apikey' => $apiKey,
            'number' => $missed_parent_mobile,
            'message' => $missed_message,
            'sendername' => 'BNHSAdmin'
        );

        curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($missed_parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output_missed = curl_exec($ch);
        curl_close($ch);

        // Debug response
        $response_missed = json_decode($output_missed, true);
        echo "Missed Scan Notification Response: <pre>";
        print_r($response_missed);
        echo "</pre>";
    }
}

// Query to find students who logged in the morning but missed the afternoon
$missed_afternoon_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, 
           CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
    FROM student_section ss
    INNER JOIN student s ON ss.student_id = s.student_id
    INNER JOIN parent p ON s.parent_id = p.parent_id
    WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
    AND s.student_id IN (
        SELECT student_id FROM attendance 
        WHERE date = ? AND time < ?
    )
    AND s.student_id NOT IN (
        SELECT student_id FROM attendance 
        WHERE date = ? AND time >= ?
    )";

$missed_afternoon_stmt = $conn->prepare($missed_afternoon_sql);
$missed_afternoon_stmt->bind_param("ssss", $current_date, $morning_window_end, $current_date, $afternoon_start);
$missed_afternoon_stmt->execute();
$missed_afternoon_result = $missed_afternoon_stmt->get_result();

// Loop through each student who missed afternoon attendance
while ($missed_afternoon_row = $missed_afternoon_result->fetch_assoc()) {
    $missed_parent_name = $missed_afternoon_row['parent_name'];
    $missed_parent_mobile = $missed_afternoon_row['parent_mobile'];
    $missed_student = $missed_afternoon_row['student'];

    // Format mobile number to international format
    if (strpos($missed_parent_mobile, '0') === 0) {
        $missed_parent_mobile = '+63' . substr($missed_parent_mobile, 1); 
    }

  
    $missed_message = "Good day! $missed_parent_name, your student $missed_student is absent in the afternoon class.";

    $ch = curl_init();
    $missed_parameters = array(
        'apikey' => $apiKey,
        'number' => $missed_parent_mobile,
        'message' => $missed_message,
        'sendername' => 'BNHSAdmin'
    );

    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($missed_parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output_missed = curl_exec($ch);
    curl_close($ch);

    // Debug response
    $response_missed = json_decode($output_missed, true);
    echo "Missed Afternoon Scan Notification Response: <pre>";
    print_r($response_missed);
    echo "</pre>";
}

$missed_stmt->close();
$missed_afternoon_stmt->close();
$conn->close();
?>
