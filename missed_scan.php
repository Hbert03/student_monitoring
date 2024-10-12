<?php
include('database.php');

// API key for sending SMS
$apiKey = 'f770208e20af697387421fcf32ba90da';

// Define the time windows for missed scans
$morning_window_end = '12:00:00';
$afternoon_window_end = '17:00:00'; 

// Get the current time
date_default_timezone_set('Asia/Manila');
$current_time = date('H:i:s');
$current_date = date('Y-m-d');

// Query to find students who haven't scanned today
$missed_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, 
                      CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
               FROM student_section ss
               INNER JOIN student s ON ss.student_id = s.student_id
               INNER JOIN parent p ON s.parent_id = p.parent_id
               WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
               AND s.student_id NOT IN (SELECT student_id FROM attendance WHERE date LIKE ?)";

$missed_stmt = $conn->prepare($missed_sql);
$attendance_date = $current_date . '%';
$missed_stmt->bind_param("s", $attendance_date);
$missed_stmt->execute();
$missed_result = $missed_stmt->get_result();

// Loop through each missed student
while ($missed_row = $missed_result->fetch_assoc()) {
    $missed_parent_name = $missed_row['parent_name'];
    $missed_parent_mobile = $missed_row['parent_mobile'];
    $missed_student = $missed_row['student'];

    // Format mobile number to international format
    if (strpos($missed_parent_mobile, '0') === 0) {
        $missed_parent_mobile = '+63' . substr($missed_parent_mobile, 1); 
    }

    // Determine if it's morning or afternoon and customize the message
    $missed_message = '';
    if ($current_time <= $morning_window_end) {
        $missed_message = "Good day! $missed_parent_name, your student $missed_student is absent in the morning class.";
    } elseif ($current_time <= $afternoon_window_end) {
        $missed_message = "Good day! $missed_parent_name, your student $missed_student is absent in the afternoon class.";
    } else {
        $missed_message = "Good day! $missed_parent_name, your student $missed_student has not logged in for today's attendance.";
    }

    // Send SMS
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

$missed_stmt->close();
$conn->close();
?>
