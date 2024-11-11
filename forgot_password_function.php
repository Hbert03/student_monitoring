<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'database.php'; 
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = $_POST['phone_number'];


    $query = "SELECT * FROM users WHERE phone_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $phone_number); 
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {

        $verification_code = random_int(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // Store the verification code and expiry time in the database
        $query = "UPDATE users SET reset_code = ?, code_expiry = ? WHERE phone_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $verification_code, $expiry, $phone_number);
        $stmt->execute();

        // Send SMS with Semaphore
        $api_key = 'f770208e20af697387421fcf32ba90da'; 
        $sender_name = 'BNHSAdmin';
        $message = "Your verification code is $verification_code. This code will expire in 15 minutes.";

        $url = 'https://api.semaphore.co/api/v4/messages';
        $data = [
            'apikey' => $api_key,
            'number' => $phone_number,
            'message' => $message,
            'sendername' => $sender_name
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            echo "Failed to send the verification code. Please try again.";
        } else {
        
            $_SESSION['phone_number'] = $phone_number;

        
            header("Location: enter_code.php"); 
            exit();
        }
    } else {
       
        echo "Phone number not found.";
    }
}
?>
