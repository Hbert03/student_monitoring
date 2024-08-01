<?php
include ('database.php');

if (isset($_POST['save'])) {
    $parentName = $_POST['parentname'];
    $mobileNumber = $_POST['mobilenumber'];
    $address = $_POST['address'];
    $firstName = $_POST['firstname'];
    $middleName = $_POST['middlename'];
    $lastName = $_POST['lastname'];
    $studentMobile = $_POST['studentmobile'];
    $studentAddress = $_POST['studentaddress'];
    $status = $_POST['status'];
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
        $query = "INSERT INTO parent (parent_name, parent_mobile, parent_address) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing query for parent: " . $conn->error);
        }
        $stmt->bind_param("sss", $parentName, $mobileNumber, $address);

        if ($stmt->execute()) {
            $parent_id = $stmt->insert_id;

            // Insert student details
            $query = "INSERT INTO student (student_firstname, student_middlename, student_lastname, student_mobile, student_address, student_status, parent_id, grade_level_id)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Error preparing query for student: " . $conn->error);
            }
            $stmt->bind_param("ssssssii", $firstName, $middleName, $lastName, $studentMobile, $studentAddress, $status, $parent_id, $gradelevel);

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

if(isset($_POST['save_teacher'])) {
    $fullname = $_POST['fullname'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile_num'];
    $status = $_POST['status'];

    $query = "INSERT INTO teacher(teacher_name, teacher_address, teacher_mobile, teacher_status) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if($stmt) {
        $stmt->bind_param("ssss", $fullname, $address, $mobile, $status);
        if($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
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

?>
