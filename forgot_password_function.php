<?php
require 'database.php'; // Include your database configuration
require 'vendor/autoload.php'; // Include PHPMailer autoload if using Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); // "s" denotes a string parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $verification_code = random_int(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // Save the code and expiry in the database
        $query = "UPDATE users SET reset_code = ?, code_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $verification_code, $expiry, $email);
        $stmt->execute();

        // Send email with PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username = 'bonifacionhsa@gmail.com';
            $mail->Password = 'gccpharggeblqsmg'; 
            $mail->SMTPSecure = 'ssl'; 
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('bonifacionhsa@gmail.com', 'BNHSAdmin');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body    = "<p>Your verification code is <strong>$verification_code</strong>.</p><p>This code will expire in 15 minutes.</p>";

            $mail->send();
            echo "Verification code sent. Please check your email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found.";
    }
}
?>
