<?php 
include('database.php');
require 'vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey = 'f770208e20af697387421fcf32ba90da';

// Define time windows
$morning_window_end = '12:02:00';
$afternoon_start = '14:02:00';
$afternoon_window_end = '17:02:00';

date_default_timezone_set('Asia/Manila');
$current_date = date('Y-m-d H:i:s');

$task = isset($_GET['task']) ? $_GET['task'] : '';

switch ($task) {
    case 'morning':
        handleMorningTask($current_date, $morning_window_end);
        break;
    case 'afternoon':
        handleAfternoonTask($current_date, $afternoon_start);
        break;
    case 'evening':
        handleEveningTask($current_date, $afternoon_window_end);
        break;
    default:
        echo "Invalid task specified.";
        break;
}

function handleMorningTask($current_date, $morning_window_end) {
    global $conn;
    
    // Query for missed morning logout
    $missed_logout_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, p.email,
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
    $missed_logout_stmt->close();
    
   
    $absent_morning_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, p.email,
        CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
        FROM student_section ss
        INNER JOIN student s ON ss.student_id = s.student_id
        INNER JOIN parent p ON s.parent_id = p.parent_id
        WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
        AND s.student_id NOT IN (
            SELECT student_id FROM attendance 
            WHERE DATE(date) = ? AND TIME(date) < ? AND status = 'IN'
        )";
    $absent_morning_stmt = $conn->prepare($absent_morning_sql);
    $absent_morning_stmt->bind_param("ss", $current_date, $morning_window_end);
    $absent_morning_stmt->execute();
    $absent_morning_result = $absent_morning_stmt->get_result();

    while ($row = $absent_morning_result->fetch_assoc()) {
        sendSMS($row, "Good day! {$row['parent_name']}, your student {$row['student']} was absent in the morning class.");
    }
    $absent_morning_stmt->close();
}

function handleAfternoonTask($current_date, $afternoon_start) {
    global $conn;
    $missed_afternoon_login_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, p.email,
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
    $missed_afternoon_login_stmt->close();
}

function handleEveningTask($current_date, $afternoon_window_end) {
    global $conn;
    $missed_both_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, p.email,
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
    $missed_both_stmt->close();
}


function sendSMS($row, $message) {
    global $apiKey;
    $parent_mobile = $row['parent_mobile'];
    $parent_email = $row['email'];
    
    // Format PH mobile number
    if (strpos($parent_mobile, '0') === 0) {
        $parent_mobile = '+63' . substr($parent_mobile, 1);
    }

    // Send SMS
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

    $output = curl_exec($ch);
    curl_close($ch);

    // Send Email using PHPMailer
    if (!empty($parent_email)) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bonifacionhsa@gmail.com';
            $mail->Password = 'gccpharggeblqsmg'; 
            $mail->SMTPSecure = 'ssl'; 
            $mail->Port = 465;

            $mail->setFrom('bonifacionhsa@gmail.com', 'BNHSAdmin');
            $mail->addAddress($parent_email);

            $mail->isHTML(true);
            $mail->Subject = 'Attendance Notification';
            $mail->Body = $message;

            if ($mail->send()) {
                error_log("Email sent successfully to {$parent_email}");
            } else {
                error_log("Failed to send email to {$parent_email}. Error: " . $mail->ErrorInfo);
            }
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }
    }
}

$conn->close();
?>
