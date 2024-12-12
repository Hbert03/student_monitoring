<?php
include('database.php');
require 'vendor/autoload.php'; 

$apiKey = 'f770208e20af697387421fcf32ba90da';
$student_id = intval($_POST['student_id']);

    date_default_timezone_set('Asia/Manila');
    $current_time = new DateTime(date('H:i:s'));
    $status = '';

  
    $windows = [
        'morning_in' => [new DateTime('07:00:00'), new DateTime('08:00:00')],
        'morning_out' => [new DateTime('12:00:00'), new DateTime('12:59:59')],
        'afternoon_in' => [new DateTime('13:00:00'), new DateTime('14:00:00')],
        'afternoon_out' => [new DateTime('16:00:00'), new DateTime('18:00:00')],
    ];
    
    // Check if student exists
    $sql_check_student = "SELECT student_id FROM student WHERE student_id = ?";
    $stmt_check = $conn->prepare($sql_check_student);
    $stmt_check->bind_param("i", $student_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $student_exists = $result_check->num_rows > 0;
    $stmt_check->close();

    if (!$student_exists) {
        echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
        exit;
    }

    // Determine attendance status
    if ($current_time >= $windows['morning_in'][0] && $current_time <= $windows['morning_in'][1]) {
        $status = 'IN';
    } elseif ($current_time > $windows['morning_in'][1] && $current_time < $windows['morning_out'][0]) {
        $status = 'LATE';
    } elseif ($current_time >= $windows['morning_out'][0] && $current_time <= $windows['morning_out'][1]) {
        $status = 'OUT';
    } elseif ($current_time >= $windows['afternoon_in'][0] && $current_time <= $windows['afternoon_in'][1]) {
        $status = 'IN';
    } elseif ($current_time > $windows['afternoon_in'][1] && $current_time < $windows['afternoon_out'][0]) {
        $status = 'LATE';
    } elseif ($current_time >= $windows['afternoon_out'][0] && $current_time <= $windows['afternoon_out'][1]) {
        $status = 'OUT';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid attendance time.']);
        exit;
    }
    


// Fetch parent details
$sql = "SELECT t.parent_mobile, t.parent_name, t.email, CONCAT(s.student_firstname, ' ', s.student_lastname ) as student 
        FROM parent t 
        INNER JOIN student s ON s.parent_id = t.parent_id 
        WHERE s.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$parent = $result->fetch_assoc();
$stmt->close();

if ($parent) {
    $parent_name = $parent['parent_name'];
    $parent_mobile = $parent['parent_mobile'];
    $parent_email = $parent['email'];
    $parent_student = $parent['student'];

    // Format the mobile number
    if (strpos($parent_mobile, '0') === 0) {
        $parent_mobile = '+63' . substr($parent_mobile, 1); 
    }

    // Construct the SMS message
    $message = "Good day! $parent_name, your student $parent_student is marked as $status.";

    // Send SMS using Semaphore
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
        $mail->send();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Email error: {$mail->ErrorInfo}"]);
        exit;
    }

    // Insert attendance record
    $sql = "INSERT INTO attendance (student_id, status) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $student_id, $status);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Scan Successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error recording attendance.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Parent details not found.']);
}

$conn->close();
?>
                                