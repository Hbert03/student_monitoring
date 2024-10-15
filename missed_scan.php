<?php
include('database.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
if (!$missed_stmt) {
    die("Error preparing missed scans SQL: " . $conn->error);
}
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
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $output_missed = curl_exec($ch);
        if ($output_missed === false) {
            echo "cURL Error: " . curl_error($ch);
        } else {
            $response_missed = json_decode($output_missed, true);
            if (isset($response_missed['status']) && $response_missed['status'] == 'success') {
                echo "SMS sent successfully!";
            } else {
                echo "Failed to send SMS. Response: <pre>";
                print_r($response_missed);
                echo "</pre>";
            }
        }
        curl_close($ch);
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
        WHERE DATE(date) = ? AND TIME(date) < ?
    )
    AND s.student_id NOT IN (
        SELECT student_id FROM attendance 
        WHERE DATE(date) = ? AND TIME(date) >= ?)";


$missed_afternoon_stmt = $conn->prepare($missed_afternoon_sql);
if (!$missed_afternoon_stmt) {
    die("Error preparing missed afternoon SQL: " . $conn->error);
}
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
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    $output_missed = curl_exec($ch);
    if ($output_missed === false) {
        echo "cURL Error: " . curl_error($ch);
    } else {
        $response_missed = json_decode($output_missed, true);
        if (isset($response_missed['status']) && $response_missed['status'] == 'success') {
            echo "SMS sent successfully!";
        } else {
            echo "Failed to send SMS. Response: <pre>";
            print_r($response_missed);
            echo "</pre>";
        }
    }
    curl_close($ch);
}

$missed_stmt->close();
$missed_afternoon_stmt->close();
$conn->close();

?>
