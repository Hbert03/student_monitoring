<?php
require 'phpqrcode/qrlib.php';

if (isset($_POST['studentId']) && isset($_POST['studentName'])) {
    $studentId = $_POST['studentId'];
    $studentName = $_POST['studentName'];
    
   
    $qrText = "Student ID: " . $studentId . " Name: " . $studentName;

    
    $nameParts = explode(' ', $studentName);
    $firstName = $nameParts[0];
    $lastName = end($nameParts); 
    $fileName = $firstName . '_' . $lastName . '.png';
    $filePath = 'qrcodes/' . $fileName;

    if (!file_exists('qrcodes')) {
        mkdir('qrcodes', 0777, true);
    }

    QRcode::png($qrText, $filePath);

    if (file_exists($filePath)) {
        echo json_encode(['success' => true, 'qrCodeUrl' => $filePath, 'fileName' => $fileName]);
    } else {
        echo json_encode(['success' => false, 'error' => 'QR code generation failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
?>
