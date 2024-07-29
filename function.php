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
?>
