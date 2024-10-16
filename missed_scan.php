<?php
include('database.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = 'f770208e20af697387421fcf32ba90da';

// Define time windows
$morning_window_end = '12:00:00';
$afternoon_start = '13:00:00';
$afternoon_window_end = '17:00:00';

// Get the current time and date
date_default_timezone_set('Asia/Manila');
$current_time = date('H:i:s');
$current_date = date('Y-m-d');

// 1. Students who logged in the morning but did not log out in the afternoon
$missed_logout_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, 
           CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
    FROM student_section ss
    INNER JOIN student s ON ss.student_id = s.student_id
    INNER JOIN parent p ON s.parent_id = p.parent_id
    WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
    AND s.student_id IN (
        SELECT student_id FROM attendance 
        WHERE DATE(date) = ? AND TIME(date) < ? AND status = 'IN'
    )
    AND s.student_id NOT IN (
        SELECT student_id FROM attendance 
        WHERE DATE(date) = ? AND TIME(date) >= ? AND status = 'OUT'
    )";

$missed_logout_stmt = $conn->prepare($missed_logout_sql);
$missed_logout_stmt->bind_param("ssss", $current_date, $morning_window_end, $current_date, $afternoon_start);
$missed_logout_stmt->execute();
$missed_logout_result = $missed_logout_stmt->get_result();

while ($row = $missed_logout_result->fetch_assoc()) {
    sendSMS($row, "Good day! {$row['parent_name']}, your student {$row['student']} did not log out in the afternoon.");
}

// 2. Students who missed both morning login and afternoon logout
$missed_both_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, 
           CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
    FROM student_section ss
    INNER JOIN student s ON ss.student_id = s.student_id
    INNER JOIN parent p ON s.parent_id = p.parent_id
    WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
    AND s.student_id NOT IN (
        SELECT student_id FROM attendance WHERE DATE(date) = ?
    )";

$missed_both_stmt = $conn->prepare($missed_both_sql);
$missed_both_stmt->bind_param("s", $current_date);
$missed_both_stmt->execute();
$missed_both_result = $missed_both_stmt->get_result();

while ($row = $missed_both_result->fetch_assoc()) {
    sendSMS($row, "Good day! {$row['parent_name']}, your student {$row['student']} was absent for both morning and afternoon.");
}

// 3. Students who did not log in during the afternoon
$missed_afternoon_login_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, 
           CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
    FROM student_section ss
    INNER JOIN student s ON ss.student_id = s.student_id
    INNER JOIN parent p ON s.parent_id = p.parent_id
    WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
    AND s.student_id NOT IN (
        SELECT student_id FROM attendance 
        WHERE DATE(date) = ? AND TIME(date) >= ? AND TIME(date) < ?
    )";

$missed_afternoon_login_stmt = $conn->prepare($missed_afternoon_login_sql);
$missed_afternoon_login_stmt->bind_param("sss", $current_date, $afternoon_start, $afternoon_window_end);
$missed_afternoon_login_stmt->execute();
$missed_afternoon_login_result = $missed_afternoon_login_stmt->get_result();

while ($row = $missed_afternoon_login_result->fetch_assoc()) {
    sendSMS($row, "Good day! {$row['parent_name']}, your student {$row['student']} did not log in during the afternoon.");
}

// Helper function to send SMS
function sendSMS($row, $message) {
    global $apiKey;
    $parent_mobile = $row['parent_mobile'];
    
    // Format mobile number to international format
    if (strpos($parent_mobile, '0') === 0) {
        $parent_mobile = '+63' . substr($parent_mobile, 1);
    }

    $ch = curl_init();
    $parameters = array(
        'apikey' => $apiKey,
        'number' => $parent_mobile,
        'message' => $message,
        'sendername' => 'BNHSAdmin'
    );

    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    $output = curl_exec($ch);
    if ($output === false) {
        error_log("cURL Error: " . curl_error($ch));
    } else {
        $response = json_decode($output, true);
        if (isset($response['status']) && $response['status'] == 'success') {
            error_log("SMS sent successfully to {$parent_mobile}");
        } else {
            error_log("Failed to send SMS to {$parent_mobile}. Response: " . print_r($response, true));
        }
    }
    curl_close($ch);
}

$missed_logout_stmt->close();
$missed_both_stmt->close();
$missed_afternoon_login_stmt->close();
$conn->close();

?>
