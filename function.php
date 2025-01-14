<?php
session_start();
include ('database.php');
require_once('phpqrcode/qrlib.php');
if (isset($_POST['bulkEnrollment'])) {
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // require('phpqrcode/qrlib.php'); 
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileData = array_map('str_getcsv', file($fileTmpPath));
        $totalEnrolled = 0;
        $results = []; 

        require('fpdf/fpdf.php'); 
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);

        foreach ($fileData as $index => $row) {
            if ($index === 0) continue; 
            
            list($firstname, $middlename, $lastname, $studentMobile, $studentAddress, $status, $gender, $gradelevel, $parentName, $parentType, $email, $address) = $row;

            $query = "SELECT student_id FROM student WHERE student_firstname = ? AND student_middlename = ? AND student_lastname = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $firstname, $middlename, $lastname);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $results[] = ['name' => "$firstname $lastname", 'status' => 'exists'];
                continue; 
            }

            $stmt->close(); 

            $query = "INSERT INTO parent (parent_name, parent_type, email, parent_address) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $parentName, $parentType, $email, $address);
            if (!$stmt->execute()) {
                $results[] = ['name' => "$firstname $lastname", 'status' => 'parent_insertion_failed'];
                continue;
            }
            $parent_id = $stmt->insert_id;

            $stmt->close();

            $query = "INSERT INTO student (student_firstname, student_middlename, student_lastname, student_mobile, student_address, student_status, gender, parent_id, grade_level_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssiii", $firstname, $middlename, $lastname, $studentMobile, $studentAddress, $status, $gender, $parent_id, $gradelevel);
            if (!$stmt->execute()) {
                $results[] = ['name' => "$firstname $lastname", 'status' => 'student_insertion_failed'];
                continue;
            }
            $student_id = $stmt->insert_id;

            $stmt->close();


            $qrCodeData = json_encode(['id' => $student_id, 'name' => "$firstname $middlename $lastname"]);
            $qrCodeUrl = generateQRCodebulk($qrCodeData);
            
            if (!file_exists($qrCodeUrl)) {
                $results[] = ['name' => "$firstname $lastname", 'status' => 'qr_code_generation_failed'];
                continue;
            }
            
   
            $pdf->Image($qrCodeUrl, 10, $pdf->GetY(), 30, 30);  
            $pdf->SetY($pdf->GetY() + 30);  
            $pdf->Cell(30, 10, $lastname, 0, 1, 'C');  
            
 
            $results[] = ['name' => "$firstname $lastname", 'status' => 'enrolled', 'qrCode' => $qrCodeUrl];
            $totalEnrolled++;
            }
            

            $pdfFilePath = "qrcodes/Enrollment_QRCodes.pdf";
            $pdf->Output('F', $pdfFilePath); 
            

            echo json_encode([
                'success' => true,
                'totalEnrolled' => $totalEnrolled,
                'results' => $results,
                'pdf_url' => $pdfFilePath,
            ]);
            
        
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to read the file or no file provided']);
    }
}


function generateQRCodebulk($data) {
    $filePath = 'qrcodes/' . uniqid() . '.png';
    

    QRcode::png($data, $filePath, QR_ECLEVEL_L, 10); 
    
    if (!file_exists($filePath)) {
        throw new Exception("QR Code generation failed for: $data");
    }
    
    return $filePath;
}




function generateQRCode($data) {
    ob_start();
    QRcode::png($data);
    $imageString = ob_get_clean();
    return $imageString;
}


if (isset($_POST['save'])) {
    $parentName = $_POST['parentname'];
    $parent_type = $_POST['parent_type'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $firstName = $_POST['firstname'];
    $middleName = $_POST['middlename'];
    $lastName = $_POST['lastname'];
    $studentMobile = $_POST['studentmobile'];
    $studentAddress = $_POST['studentaddress'];
    $status = $_POST['status'];
    $gender = $_POST['gender'];
    $gradelevel = $_POST['grade'];


    // Check if the student already exists
    $query = "SELECT student_id FROM student WHERE student_firstname = ? AND student_middlename = ? AND student_lastname = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing query for checking existing student: " . $conn->error);
    }
    $stmt->bind_param("sss", $firstName, $middleName, $lastName);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {

        echo json_encode(['success' => false, 'error' => "Student already exists"]);
    } else {
        // Insert parent details
        $query = "INSERT INTO parent (parent_name, parent_type, email, parent_address) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing query for parent: " . $conn->error);
        }
        $stmt->bind_param("ssss", $parentName, $parent_type, $email, $address);

        if ($stmt->execute()) {
            $parent_id = $stmt->insert_id;

            // Insert student details
            $query = "INSERT INTO student (student_firstname, student_middlename, student_lastname, student_mobile, student_address, student_status, gender, parent_id, grade_level_id)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Error preparing query for student: " . $conn->error);
            }
            $stmt->bind_param("ssssssiii", $firstName, $middleName, $lastName, $studentMobile, $studentAddress, $status, $gender, $parent_id, $gradelevel);

            if ($stmt->execute()) {
                $student_id = $stmt->insert_id;
                $student_fullname = $firstName . ' ' . $middleName . ' ' . $lastName;
                echo json_encode(['success' => true, 'studentId' => $student_id, 'studentName' => $student_fullname]);
            } else {
                echo json_encode(['success' => false, 'error' => "Execute failed: " . $stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => "Execute failed: " . $stmt->error]);
        }
    }

    $stmt->close();
    $conn->close();
}

if (isset($_POST['save_teacher'])) {

    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile_num'];
    $status = $_POST['status'];

    $checkQuery = "SELECT * FROM teacher WHERE teacher_firstname = ? AND teacher_lastname = ? AND teacher_mobile = ?";
    $checkStmt = $conn->prepare($checkQuery);

    if ($checkStmt) {
        $checkStmt->bind_param("sss", $firstname, $lastname, $mobile);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Teacher already exists."]);
        } else {
            $query = "INSERT INTO teacher (teacher_firstname, teacher_middlename, teacher_lastname, teacher_address, teacher_mobile, teacher_status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param("ssssss", $firstname, $middlename, $lastname, $address, $mobile, $status);
                if ($stmt->execute()) {
                    $teacher_id = $stmt->insert_id;

                    // Remove spaces in the first name
                    $cleaned_firstname = strtolower(str_replace(' ', '', $firstname));
                    $email = $cleaned_firstname . '.' . strtolower($lastname) . '@bnhs.gov.ph';
                    
                    $password = 'password';  // Default password, you can change this
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $role = 1; 

                    $query = "INSERT INTO users (email, password, role, teacher_id) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);

                    if ($stmt) {
                        $stmt->bind_param("sssi", $email, $hashed_password, $role, $teacher_id);
                        if ($stmt->execute()) {
                            echo json_encode(["success" => true, "message" => "Teacher and user account created successfully."]);
                        } else {
                            echo json_encode(["success" => false, "error" => $stmt->error]);
                        }
                        $stmt->close();
                    } else {
                        echo json_encode(["success" => false, "error" => $conn->error]);
                    }
                } else {
                    echo json_encode(["success" => false, "error" => $stmt->error]);
                }
            }
        }
    }
}








if (isset($_POST['addschoolyear'])) {
    $schoolyear = $_POST['school_year'];

    $query = "INSERT INTO school_year (school_year_name) VALUES (?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $schoolyear);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "School year added successfully."]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
}


//add subject
if (isset($_POST['addsubject'])) {
    $subject = $_POST['subject'];

    $query = "INSERT INTO subject (subject_name) VALUES (?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $subject);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Subject added successfully."]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
}

 if(isset($_POST['addSection']))
 {
    $section = $_POST['section'];
    $gradelevel = $_POST['gradelevel'];

    $query = "INSERT INTO section(section_name, grade_level_id) VALUES (?,?)";

    $stmt = $conn->prepare($query);

    if($stmt)
    {
        $stmt->bind_param("ss", $section, $gradelevel);
        if($stmt->execute()){
            echo json_encode(["success" =>true]);
        }else{
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    }
 }


 if(isset($_POST['classSched']))
 {
    $subject = $_POST['subject'];
    $teacher = $_POST['teacher'];
    $section = $_POST['section'];
    $school_year = $_POST['school_year'];

    $query = "INSERT INTO class_schedule(subject_id, teacher_id, section_id, school_year_id)
    VALUES (?,?,?,?)";
    $stmt = $conn->prepare($query);

    if($stmt)
    {
        $stmt->bind_param("ssss", $subject, $teacher, $section, $school_year);
        if($stmt->execute()){
            echo json_encode(["success" => true]);
        }else{
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    }
 }

 if (isset($_POST['addSection1'])) {
    $section = $_POST['section'];
    $school_year = $_POST['school_year'];
    $teacher = $_SESSION['teacher_id'];
    $students = isset($_POST['student']) ? $_POST['student'] : []; 

 
    if (!empty($students)) {
     
        $conn->begin_transaction();

        try {
            $query = "INSERT INTO student_section (section_id, teacher_id, school_year_id, student_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                foreach ($students as $student) {
                    $stmt->bind_param("ssss", $section, $teacher, $school_year, $student);

                    if (!$stmt->execute()) {
                        throw new Exception($stmt->error);
                    }
                }
               
                $conn->commit();
                echo json_encode(["success" => true]);
            } else {
                throw new Exception("Failed to prepare the statement");
            }
        } catch (Exception $e) {
           
            $conn->rollback();
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "No students selected"]);
    }

    $conn->close();
}





?>
