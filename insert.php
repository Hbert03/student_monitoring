<?php
include('database.php');
require 'vendor/autoload.php'; 

$apiKey = 'f770208e20af697387421fcf32ba90da';
$student_id = intval($_POST['student_id']);

date_default_timezone_set('Asia/Manila');
$current_time = date('H:i:s');
$current_date = date('Y-m-d H:i:s');
$status = '';

// Define attendance windows
$attendance_windows = [
    'morning_in' => ['01:00:00', '08:00:00'], 
    'morning_out' => ['12:00:00', '12:59:59'], 
    'afternoon_in' => ['13:00:00', '14:00:00'],
    'afternoon_out' => ['16:00:00', '18:00:00'],
];

// Determine attendance status
if ($current_time >= $attendance_windows['morning_in'][0] && $current_time <= $attendance_windows['morning_in'][1]) {
    $status = 'IN';
} elseif ($current_time > $attendance_windows['morning_in'][1] && $current_time < $attendance_windows['morning_out'][0]) {
    $status = 'LATE'; 
} elseif ($current_time >= $attendance_windows['morning_out'][0] && $current_time <= $attendance_windows['morning_out'][1]) {
    $status = 'OUT'; 
} elseif ($current_time >= $attendance_windows['afternoon_in'][0] && $current_time <= $attendance_windows['afternoon_in'][1]) {
    $status = 'IN'; 
} elseif ($current_time > $attendance_windows['afternoon_in'][1] && $current_time < $attendance_windows['afternoon_out'][0]) {
    $status = 'LATE'; 
} elseif ($current_time >= $attendance_windows['afternoon_out'][0] && $current_time <= $attendance_windows['afternoon_out'][1]) {
    $status = 'OUT'; 
} else {
    $status = 'Invalid attendance time.';
}

echo "Current Time: $current_time, Status: $status<br>";

if ($status != 'Invalid attendance time.') {
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

        // Construct SMS and Email message based on the attendance status
        $message = '';
        if ($status == 'LATE') {
            if ($current_time > $attendance_windows['morning_in'][1] && $current_time < $attendance_windows['morning_out'][0]) {
                $message = "Good day! $parent_name, your student $parent_student is present but late for the morning class.";
            } elseif ($current_time > $attendance_windows['afternoon_in'][1] && $current_time < $attendance_windows['afternoon_out'][0]) {
                $message = "Good day! $parent_name, your student $parent_student is present but late for the afternoon class.";
            }
        } elseif ($status == 'IN') {
            if ($current_time >= $attendance_windows['morning_in'][0] && $current_time <= $attendance_windows['morning_in'][1]) {
                $message = "Good day! $parent_name, your student $parent_student is present for the morning class.";
            } elseif ($current_time >= $attendance_windows['afternoon_in'][0] && $current_time <= $attendance_windows['afternoon_in'][1]) {
                $message = "Good day! $parent_name, your student $parent_student is present for the afternoon class.";
            }
        } elseif ($status == 'OUT') {
            if ($current_time >= $attendance_windows['morning_out'][0] && $current_time <= $attendance_windows['morning_out'][1]) {
                $message = "Good day! $parent_name, your student $parent_student has logged out for the morning class.";
            } elseif ($current_time >= $attendance_windows['afternoon_out'][0] && $current_time <= $attendance_windows['afternoon_out'][1]) {
                $message = "Good day! $parent_name, your student $parent_student has logged out for the afternoon class.";
            }
        } else {
            $message = "Invalid attendance time.";
        }

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
            //Server settings
            $mail->SMTPDebug = 2; 
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bonifacionhsa@gmail.com';
            $mail->Password = 'gccpharggeblqsmg'; 
            $mail->SMTPSecure = 'ssl'; 
            $mail->Port = 465;
            

            //Recipients
            $mail->setFrom('bonifacionhsa@gmail.com', 'BNHSAdmin');
            $mail->addAddress($parent_email); 

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Attendance Notification';
            $mail->Body = $message;

            $mail->send();
            echo 'Email has been sent<br>';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        // Decode response and debug
        $response = json_decode($output, true);
        echo "Semaphore Response: <pre>";
        print_r($response);
        echo "</pre>";

        // Insert attendance record
        $sql = "INSERT INTO attendance (date, student_id, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $current_date, $student_id, $status);

        if ($stmt->execute()) {
            echo "Attendance recorded successfully!<br>";

            // Check if SMS was sent successfully
            if (isset($response[0]['status']) && $response[0]['status'] == 'Sent') {
                echo "SMS sent successfully!";
            } elseif (isset($response[0]['status']) && $response[0]['status'] == 'Pending') {
                echo "SMS is pending. It may take some time to be sent.";
            } else {
                echo "Error sending SMS. Full Response: ";
                print_r($response);
            }
        } else {
            echo "Error recording attendance: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Parent details not found for student_id: $student_id";
    }
} else {
    echo $status;
}

$conn->close();
?>
