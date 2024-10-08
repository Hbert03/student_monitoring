<?php
include('database.php');


$apiKey = 'f770208e20af697387421fcf32ba90da';
$student_id = intval($_POST['student_id']);


date_default_timezone_set('Asia/Manila');
$current_time = date('H:i:s');
$current_date = date('Y-m-d H:i:s');
$status = '';


$attendance_windows = [
    'morning_in' => ['01:00:00', '08:00:00'], 
    'morning_out' => ['12:00:00', '12:59:59'], 
    'afternoon_in' => ['13:00:00', '14:00:00'],
    'afternoon_out' => ['16:00:00', '22:00:00'],
];

// Determine the attendance status based on the current time
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
    // Fetch parent details based on student ID
    $sql = "SELECT t.parent_mobile, t.parent_name, CONCAT(s.student_firstname, ' ', s.student_lastname ) as student FROM parent t INNER JOIN student s ON s.parent_id = t.parent_id WHERE s.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $parent = $result->fetch_assoc();
    $stmt->close();


    if ($parent) {
        $parent_name = $parent['parent_name'];
        $parent_mobile = $parent['parent_mobile'];
        $parent_student = $parent['student'];
 
    
        if (strpos($parent_mobile, '0') === 0) {
    $parent_mobile = '+63' . substr($parent_mobile, 1); 
        }

           
        if ($status == 'LATE') {
            if ($current_time > $attendance_windows['morning_in'][1] && $current_time < $attendance_windows['morning_out'][0]) {
       
                $message = "Good day! $parent_name, your student $parent_student is present but late for the morning class.";
            } elseif ($current_time > $attendance_windows['afternoon_in'][1] && $current_time < $attendance_windows['afternoon_out'][0]) {
            
                $message = "Good day! $parent_name, your student $parent_student is present but late for the afternoon class.";
            }
        } elseif ($status == 'IN') {
            if ($current_time >= $attendance_windows['morning_in'][0] && $current_time <= $attendance_windows['morning_in'][1]) {
              
                $message = "Good day! $parent_name, your student $parent_student has logged in for the morning class.";
            } elseif ($current_time >= $attendance_windows['afternoon_in'][0] && $current_time <= $attendance_windows['afternoon_in'][1]) {
            
                $message = "Good day! $parent_name, your student $parent_student has logged in for the afternoon class.";
            }
        } elseif ($status == 'OUT') {
            if ($current_time >= $attendance_windows['morning_out'][0] && $current_time <= $attendance_windows['morning_out'][1]) {
                // Morning out
                $message = "Good day! $parent_name, your student $parent_student has logged out for the morning class.";
            } elseif ($current_time >= $attendance_windows['afternoon_out'][0] && $current_time <= $attendance_windows['afternoon_out'][1]) {
                // Afternoon out
                $message = "Good day! $parent_name, your student $parent_student has logged out for the afternoon class.";
            }
        } else {
            $message = "Invalid attendance time.";
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

            $output = curl_exec($ch);
            curl_close($ch);

            // Decode the response from Semaphore
            $response = json_decode($output, true);

            // Debugging: Print the full response
            echo "Semaphore Response: <pre>";
            print_r($response);
            echo "</pre>";


        // **Insert attendance into the database regardless of SMS status**
        $sql = "INSERT INTO attendance (date, student_id, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $current_date, $student_id, $status);

        if ($stmt->execute()) {
            echo "Attendance recorded successfully!<br>";

            // Check if the SMS was sent successfully
            if (isset($response[0]['status']) && $response[0]['status'] == 'Sent') {
                echo "SMS sent successfully!";
            } elseif (isset($response[0]['status']) && $response[0]['status'] == 'Pending') {
                echo "SMS is pending. It may take some time to be sent.";
            } else {
                // If SMS failed, log the error
                echo "Error sending SMS. Full Response: ";
                print_r($response);
            }
        } else {
            // Debugging: Log database error
            echo "Error recording attendance: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Parent details not found for student_id: $student_id";
    }
} else {
    echo $status;
}


// Define the time windows for missed scans
$morning_window_end = '12:00:00';
$afternoon_window_end = '21:25:00'; 


$missed_sql = "SELECT s.student_id, p.parent_name, p.parent_mobile, 
                      CONCAT(s.student_firstname, ' ', s.student_lastname) AS student
               FROM student_section ss
               INNER JOIN student s ON ss.student_id = s.student_id
               INNER JOIN parent p ON s.parent_id = p.parent_id
               WHERE ss.school_year_id = (SELECT school_year_id FROM school_year ORDER BY school_year_id DESC LIMIT 1)
               AND s.student_id NOT IN (SELECT student_id FROM attendance WHERE date LIKE ?)";

$missed_stmt = $conn->prepare($missed_sql);
$attendance_date = date('Y-m-d') . '%';
$missed_stmt->bind_param("s",  $attendance_date);
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

    // Determine if it's morning or afternoon to customize the message
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
