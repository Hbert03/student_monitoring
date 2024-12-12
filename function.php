<?php
include ('database.php');

if (isset($_POST['save'])) {
    $parentName = $_POST['parentname'];
    $parent_type = $_POST['parent_type'];
    $mobileNumber = $_POST['mobilenumber'];
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
        $query = "INSERT INTO parent (parent_name, parent_type, parent_mobile, email, parent_address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing query for parent: " . $conn->error);
        }
        $stmt->bind_param("sssss", $parentName, $parent_type, $mobileNumber, $email, $address);

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
    $students = isset($_POST['student']) ? $_POST['student'] : []; 

 
    if (!empty($students)) {
     
        $conn->begin_transaction();

        try {
            $query = "INSERT INTO student_section (section_id, school_year_id, student_id) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                foreach ($students as $student) {
                    $stmt->bind_param("sss", $section, $school_year, $student);

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
