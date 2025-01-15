<?php
include('database.php');
require('fpdf/fpdf.php');
require('phpqrcode/qrlib.php');

if (isset($_POST['students']) && !empty($_POST['students'])) {
    $students = $_POST['students'];


    $pdf = new FPDF();
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);


    foreach ($students as $student) {
        $studentId = $student['studentId'];
        $qrFileName = $student['qrFileName'];


        $query = "SELECT student_id, CONCAT(student_firstname, ' ', student_lastname) AS fullname FROM student WHERE student_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $studentDetails = $result->fetch_assoc();

        $pdf->Cell(0, 10, "Name: " . $studentDetails['fullname'], 0, 1);


        if (file_exists($qrFileName)) {
            $pdf->Image($qrFileName, 10, $pdf->GetY(), 30, 30);
        } else {
            $pdf->Cell(0, 10, "QR Code for this student is missing.", 0, 1);
        }

   
        $pdf->Ln(40);

  
        if (file_exists($qrFileName)) {
            unlink($qrFileName);  
        }
    }

    // Output the PDF to a string
    $pdfContent = $pdf->Output('S');  // 'S' will return PDF content as a string

    // Encode the PDF content to base64
    echo base64_encode($pdfContent);
}
?>
