<?php
require 'phpqrcode/qrlib.php';

if (isset($_POST['studentId']) && isset($_POST['studentName'])) {
    $studentId = $_POST['studentId'];
    $studentName = $_POST['studentName'];
    $qrText = "Student ID: " . $studentId . "\nName: " . $studentName;

    $filePath = 'qrcodes/' . $studentId . '.png';

    // Ensure the directory exists
    if (!file_exists('qrcodes')) {
        mkdir('qrcodes', 0777, true);
    }

    QRcode::png($qrText, $filePath);

    // Check if the file was created successfully
    if (file_exists($filePath)) {
        echo json_encode(['success' => true, 'qrCodeUrl' => $filePath]);
    } else {
        echo json_encode(['success' => false, 'error' => 'QR code generation failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}
?>
