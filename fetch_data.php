<?php
session_start();
include('database.php');
require 'phpqrcode/qrlib.php';


if (isset($_POST['fetch'])) {
    function getDataTable($draw, $start, $length, $search, $gradeLevel) {
        global $conn;

        $sortableColumns = array('student_firstname', 'student_lastname');
        
        $orderBy = $sortableColumns[0];
        $orderDir = 'ASC';

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

        $query1 = "SELECT s.*, CONCAT(s.student_firstname, ' ', COALESCE(SUBSTRING(s.student_middlename, 1, 1), ''), '. ', s.student_lastname) AS fullname , g.grade_level_name
                   FROM student s INNER JOIN grade_level g ON g.grade_level = s.grade_level_id
                   WHERE s.grade_level_id='$gradeLevel'";

        if (!empty($search)) {
            $query1 .= " AND (CONCAT(student_firstname, ' ', student_middlename, ' ', student_lastname) LIKE '%" . $conn->real_escape_string($search) . "%')";
        }

        if (!empty($gradeLevel)) {
            $query1 .= " AND grade_level_id = " . intval($gradeLevel);
        }

        $query1 .= " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . intval($start) . ", " . intval($length);

        $result1 = $conn->query($query1);

        $totalQuery1 = "SELECT COUNT(*) AS total_count 
                        FROM student 
                        WHERE 1=1";

        if (!empty($search)) {
            $totalQuery1 .= " AND (CONCAT(student_firstname, ' ', student_middlename, ' ', student_lastname) LIKE '%" . $conn->real_escape_string($search) . "%')";
        }

        if (!empty($gradeLevel)) {
            $totalQuery1 .= " AND grade_level_id = " . intval($gradeLevel);
        }

        $totalResult1 = $conn->query($totalQuery1);
        $totalRow1 = $totalResult1->fetch_assoc();
        $totalRecords1 = $totalRow1['total_count'];

        $data = array();
        while ($row = $result1->fetch_assoc()) {
            $studentId = $row['student_id'];  
            $studentName = $row['fullname'];
            $qrFileName = 'qrcodes/' . $row['student_firstname'] . '.png';  
            
           
            if (!file_exists($qrFileName)) {
                $qrText = "Student ID: " . $studentId . "\nName: " . $studentName;
                QRcode::png($qrText, $qrFileName);
            }

            $row['qrcode'] = '<div style="text-align: center;">
                                <img src="' . $qrFileName . '" style="max-width: 100px; display: block; margin: 0 auto;" alt="QR Code">
                                <a href="' . $qrFileName . '" download class="btn btn-primary btn-sm mt-1">Download QR</a>
                              </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords1),
            "recordsFiltered" => intval($totalRecords1),
            "data" => $data
        );

        return json_encode($output);
    }

    $draw = $_POST["draw"];
    $start = $_POST["start"];
    $length = $_POST["length"];
    $search = $_POST["search"]["value"];
    $gradeLevel = isset($_POST['grade_level']) ? $_POST['grade_level'] : '';

    echo getDataTable($draw, $start, $length, $search, $gradeLevel);    
    exit();
}



if (isset($_POST['fetch_teacher'])) {

    function getDataTable($draw, $start, $length, $search) {
        global $conn;

        $sortableColumns = array('teacher_firstname', 'teacher_lastname'); 
        
        $orderBy = $sortableColumns[0];
        $orderDir = 'ASC';

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

        $query1 = "SELECT t1.*, CONCAT(t1.teacher_firstname, ' ', COALESCE(SUBSTRING(t1.teacher_middlename, 1, 1), ''), '. ', t1.teacher_lastname) AS fullname, t.email FROM teacher t1 INNER JOIN users t ON t.teacher_id = t1.teacher_id WHERE 1=1";

        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $query1 .= " AND (teacher_firstname LIKE '%$escapedSearch%' OR teacher_lastname LIKE '%$escapedSearch%' OR teacher_address LIKE '%$escapedSearch%')";
        }

        $query1 .= " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . intval($start) . ", " . intval($length);

        $result1 = $conn->query($query1);

        if (!$result1) {
            die("Query failed: " . $conn->error); 
        }

        $totalQuery1 = "SELECT COUNT(*) AS total_count FROM teacher WHERE 1=1";

        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $totalQuery1 .= " AND (teacher_firstname LIKE '%$escapedSearch%' OR teacher_lastname LIKE '%$escapedSearch%' OR teacher_address LIKE '%$escapedSearch%')";
        }

        $totalResult1 = $conn->query($totalQuery1);
        if (!$totalResult1) {
            die("Total count query failed: " . $conn->error);
        }

        $totalRow1 = $totalResult1->fetch_assoc();
        $totalRecords1 = $totalRow1['total_count'];

        $data = array();
        while ($row = $result1->fetch_assoc()) {
            $data[] = $row;
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords1),
            "recordsFiltered" => intval($totalRecords1), 
            "data" => $data
        );

        return json_encode($output);
    }

    $draw = $_POST["draw"];
    $start = $_POST["start"];
    $length = $_POST["length"];
    $search = $_POST["search"]["value"];

    echo getDataTable($draw, $start, $length, $search);
    exit();
}



if (isset($_POST['fetch_classSched'])) {

    function getDataTable($draw, $start, $length, $search, $schoolYear, $subject) {
        global $conn;

        $sortableColumns = array('subject_name', 'fullname', 'section_name', 'school_year_name');
        
        $orderBy = $sortableColumns[0];
        $orderDir = 'ASC';

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

      
        $query1 = "SELECT cs.class_schedule_id, cs.teacher_id, su.subject_name, te.teacher_firstname, te.teacher_middlename, te.teacher_lastname, 
                          CONCAT(te.teacher_firstname, ' ', COALESCE(SUBSTRING(te.teacher_middlename, 1, 1), ''), '. ', te.teacher_lastname) AS fullname, 
                          se.section_name, sc.school_year_name 
                   FROM class_schedule cs 
                   INNER JOIN subject su ON cs.subject_id = su.subject_id 
                   INNER JOIN teacher te ON cs.teacher_id = te.teacher_id 
                   INNER JOIN section se ON cs.section_id = se.section_id 
                   INNER JOIN school_year sc ON cs.school_year_id = sc.school_year_id 
                   WHERE 1=1";


        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $query1 .= " AND (su.subject_name LIKE '%$escapedSearch%' OR 
                              CONCAT(te.teacher_firstname, ' ', COALESCE(SUBSTRING(te.teacher_middlename, 1, 1), ''), '. ', te.teacher_lastname) LIKE '%$escapedSearch%' OR 
                              se.section_name LIKE '%$escapedSearch%' OR 
                              sc.school_year_name LIKE '%$escapedSearch%')";
        }


        if (!empty($schoolYear)) {
            $escapedSchoolYear = $conn->real_escape_string($schoolYear);
            $query1 .= " AND sc.school_year_id = '$escapedSchoolYear'";
        }

       
        if (!empty($subject)) {
            $escapedSubject = $conn->real_escape_string($subject);
            $query1 .= " AND su.subject_id = '$escapedSubject'";
        }

      
        $query1 .= " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . intval($start) . ", " . intval($length);

        $result1 = $conn->query($query1);


        $totalQuery1 = "SELECT COUNT(*) AS total_count 
                        FROM class_schedule cs 
                        INNER JOIN subject su ON cs.subject_id = su.subject_id 
                        INNER JOIN teacher te ON cs.teacher_id = te.teacher_id 
                        INNER JOIN section se ON cs.section_id = se.section_id 
                        INNER JOIN school_year sc ON cs.school_year_id = sc.school_year_id 
                        WHERE 1=1";

     
        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $totalQuery1 .= " AND (su.subject_name LIKE '%$escapedSearch%' OR 
                                  CONCAT(te.teacher_firstname, ' ', COALESCE(SUBSTRING(te.teacher_middlename, 1, 1), ''), '. ', te.teacher_lastname) LIKE '%$escapedSearch%' OR 
                                  se.section_name LIKE '%$escapedSearch%' OR 
                                  sc.school_year_name LIKE '%$escapedSearch%')";
        }


        if (!empty($schoolYear)) {
            $escapedSchoolYear = $conn->real_escape_string($schoolYear);
            $totalQuery1 .= " AND sc.school_year_id = '$escapedSchoolYear'";
        }

    
        if (!empty($subject)) {
            $escapedSubject = $conn->real_escape_string($subject);
            $totalQuery1 .= " AND su.subject_id = '$escapedSubject'";
        }

        $totalResult1 = $conn->query($totalQuery1);
        $totalRow1 = $totalResult1->fetch_assoc();
        $totalRecords1 = $totalRow1['total_count'];

        $data = array();
        while ($row = $result1->fetch_assoc()) {
            $data[] = $row;
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords1),
            "recordsFiltered" => intval($totalRecords1),
            "data" => $data
        );

        return json_encode($output);
    }


    $draw = $_POST["draw"];
    $start = $_POST["start"];
    $length = $_POST["length"];
    $search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : '';
    $schoolYear = isset($_POST['school_year']) ? $_POST['school_year'] : '';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';

    echo getDataTable($draw, $start, $length, $search, $schoolYear, $subject);
    exit();
}




if (isset($_POST['getdata'])) {
    $id = $_POST['class_schedule_id'];
    $query = "SELECT cs.class_schedule_id, su.subject_name, te.teacher_firstname, te.teacher_middlename, te.teacher_lastname, 
            CONCAT(te.teacher_firstname, ' ', COALESCE(SUBSTRING(te.teacher_middlename, 1, 1), ''), '. ', te.teacher_lastname) AS fullname, se.section_name, sc.school_year_name 
        FROM class_schedule cs 
        INNER JOIN subject su ON cs.subject_id = su.subject_id 
        INNER JOIN teacher te ON cs.teacher_id = te.teacher_id 
        INNER JOIN section se ON cs.section_id = se.section_id 
        INNER JOIN school_year sc ON cs.school_year_id = sc.school_year_id 
        WHERE cs.class_schedule_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo "Error executing query: " . $conn->error;
    }
    exit();
}



if (isset($_POST['update'])) {
    $class_schedule_id = $_POST['class_schedule_id'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];
    $section_id = $_POST['section_id'];
    $school_year_id = $_POST['school_year_id'];

  
    $query = "SELECT subject_id, teacher_id, section_id, school_year_id FROM class_schedule WHERE class_schedule_id = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $class_schedule_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $current_subject_id, $current_teacher_id, $current_section_id, $current_school_year_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Failed to prepare the SQL statement.";
        error_log("SQL Error: " . mysqli_error($conn));
        exit;
    }

  
    $subject_id = !empty($subject_id) ? $subject_id : $current_subject_id;
    $teacher_id = !empty($teacher_id) ? $teacher_id : $current_teacher_id;
    $section_id = !empty($section_id) ? $section_id : $current_section_id;
    $school_year_id = !empty($school_year_id) ? $school_year_id : $current_school_year_id;

    
    $query = "UPDATE class_schedule
              SET subject_id = ?, teacher_id = ?, section_id = ?, school_year_id = ?
              WHERE class_schedule_id = ?";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "iiiii", $subject_id, $teacher_id, $section_id, $school_year_id, $class_schedule_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "Updated Successfully";
        } else {
            echo "Failed to update file in the database.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Failed to prepare the SQL statement.";
        error_log("SQL Error: " . mysqli_error($conn));
    }
}

if (isset($_POST['delete'])) { 
    $class_schedule_id = $_POST['class_schedule_id'];
    $query = "DELETE FROM class_schedule WHERE class_schedule_id = '$class_schedule_id'";
    if (mysqli_query($conn, $query)) {
        echo "Your data has been deleted."; 
    } else {
        echo "Failed to delete data."; 
    }
    exit();
}


if (isset($_POST['getdatastudent'])) {
    $student_id = $_POST['student_id'];
    $query = "SELECT * FROM student WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo "Error executing query: " . $conn->error;
    }
    exit();
}


// PHP
if (isset($_POST['deletestudent'])) {
    $student_id = $_POST['student_id'];

    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

    $stmt1 = $conn->prepare("DELETE FROM student_section WHERE student_id = ?");
    $stmt1->bind_param("s", $student_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("
        DELETE s
        FROM student s
        INNER JOIN parent t ON s.parent_id = t.parent_id
        WHERE s.student_id = ?");
    $stmt2->bind_param("s", $student_id);
    $stmt2->execute();


    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    if ($stmt1->affected_rows > 0 || $stmt2->affected_rows > 0) {
        echo "Your data has been deleted.";
    } else {
        echo "Failed to delete data.";
    }


    $stmt1->close();
    $stmt2->close();
    $conn->close();
    exit();
}




if (isset($_POST['updatestudent'])) {
    $student_id = $_POST['student_id'];
    $value1 = $_POST['student_firstname'];
    $value2 = $_POST['student_middlename'];
    $value3 = $_POST['student_lastname'];
    $value4 = $_POST['student_address'];
    $value5 = $_POST['student_status'];
    $grade_level_id = $_POST['grade_level_id'];
   

    $query = "UPDATE student
              SET student_firstname = '$value1', student_middlename = '$value2', student_lastname = '$value3', student_address = '$value4', student_status = '$value5'
              , grade_level_id = '$grade_level_id'
              WHERE student_id = '$student_id'";
    if (mysqli_query($conn, $query)) {
        echo "Updated Successfully";
    } else {
        echo "Failed to update file in the database.";
    }
}



if (isset($_POST['getdatateacher'])) {
    $teacher_id = $_POST['teacher_id'];
    $query = "SELECT * FROM teacher WHERE teacher_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $teacher_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo "Error executing query: " . $conn->error;
    }
    exit();
}
if (isset($_POST['deleteteacher'])) { 
    $teacher_id = $_POST['teacher_id'];

 
    if (empty($teacher_id) || !is_numeric($teacher_id)) {
        echo "Invalid teacher ID.";
        exit();
    }

    $teacher_id = intval($teacher_id); 


    $conn->begin_transaction();

    try {
     
        $query1 = "SELECT * FROM teacher WHERE teacher_id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("i", $teacher_id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        
        if ($result1->num_rows > 0) {
            $email = $result1->fetch_assoc()['teacher_id'];

            $query2 = "DELETE FROM users WHERE teacher_id = ?";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $stmt2->close();

 
            $query3 = "DELETE FROM teacher WHERE teacher_id = ?";
            $stmt3 = $conn->prepare($query3);
            $stmt3->bind_param("i", $teacher_id);
            $stmt3->execute();
            $stmt3->close();

        
            $conn->commit();

            echo "Your data has been deleted.";
        } else {
            echo "Teacher not found.";
        }

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "Failed to delete data: " . $e->getMessage();
    }
}




if (isset($_POST['updateteacher'])) {
    $teacher_id = $_POST['teacher_id'];
    $value1 = $_POST['teacher_firstname'];
    $value2 = $_POST['teacher_middlename'];
    $value3 = $_POST['teacher_lastname'];
    $value4 = $_POST['teacher_address'];
    $value5 = $_POST['teacher_mobile'];
    $value6 = $_POST['teacher_status'];
   

    $query = "UPDATE teacher
              SET teacher_firstname = '$value1', teacher_middlename = '$value2', teacher_lastname = '$value3', teacher_address = '$value4',
               teacher_mobile = '$value5',  teacher_status = '$value6'
              WHERE teacher_id = '$teacher_id'";
    if (mysqli_query($conn, $query)) {
        echo "Updated Successfully";
    } else {
        echo "Failed to update file in the database.";
    }
}






if (isset($_POST['viewstudent'])) {

    function getDataTable($draw, $start, $length, $search, $teacher_id) {
        global $conn;

        $sortableColumns = array('student_firstname');
        
        $orderBy = $sortableColumns[0];
        $orderDir = 'ASC';

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

        $query1 = "SELECT gr.grade_level_name, st.*, CONCAT(student_firstname, ' ', COALESCE(SUBSTRING(student_middlename, 1, 1), ''), '. ', student_lastname) AS fullname FROM student st INNER JOIN student_section sts ON st.student_id = sts.student_id
         INNER JOIN section se ON sts.section_id = se.section_id INNER JOIN class_schedule cs ON se.section_id = cs.section_id INNER JOIN grade_level gr ON gr.grade_level = se.section_id INNER JOIN teacher te ON cs.teacher_id = te.teacher_id WHERE te.teacher_id = ?";

        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $query1 .= " AND (st.student_firstname LIKE '%$escapedSearch%')";
        }

        $query1 .= " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . intval($start) . ", " . intval($length);

        $stmt = $conn->prepare($query1);
        $stmt->bind_param('i', $teacher_id);
        $stmt->execute();
        $result1 = $stmt->get_result();

        $totalQuery1 = "SELECT COUNT(*) AS total_count 
                        FROM student st 
                        INNER JOIN student_section sts ON st.student_id = sts.student_id 
                        INNER JOIN section se ON sts.section_id = se.section_id 
                        INNER JOIN class_schedule cs ON se.section_id = cs.section_id 
                        INNER JOIN teacher te ON cs.teacher_id = te.teacher_id 
                        WHERE te.teacher_id = ?";

        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $totalQuery1 .= " AND (st.student_firstname LIKE '%$escapedSearch%')";
        }

        $stmtTotal = $conn->prepare($totalQuery1);
        $stmtTotal->bind_param('i', $teacher_id);
        $stmtTotal->execute();
        $totalResult1 = $stmtTotal->get_result();
        $totalRow1 = $totalResult1->fetch_assoc();
        $totalRecords1 = $totalRow1['total_count'];

        $data = array();
        while ($row = $result1->fetch_assoc()) {
            $data[] = $row;
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords1),
            "recordsFiltered" => intval($totalRecords1),
            "data" => $data
        );

        return json_encode($output);
    }

    $draw = $_POST["draw"];
    $start = $_POST["start"];
    $length = $_POST["length"];
    $search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : '';
    $teacher_id = $_POST["teacher_id"];

    echo getDataTable($draw, $start, $length, $search, $teacher_id);    
    exit();
}





if (isset($_POST['attendance'])) {

    function getAttendanceData($draw, $start, $length, $search, $teacher_id, $conn) {
        $sortableColumns = array('fullname', 'date', 'in_out_status'); 
        $orderBy = $sortableColumns[0];
        $orderDir = 'ASC';

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

        $query = "WITH RankedAttendance AS (
            SELECT 
                a.student_id,
                CONCAT(st.student_firstname, ' ', COALESCE(SUBSTRING(st.student_middlename, 1, 1), ''), '. ', st.student_lastname) AS fullname,
                CASE
                    WHEN TIME(a.date) BETWEEN '07:00:00' AND '08:00:00' THEN 'IN'
                    WHEN TIME(a.date) > '08:00:00' AND TIME(a.date) < '12:00:00' THEN 'LATE'
                    WHEN TIME(a.date) BETWEEN '12:00:00' AND '12:59:59' THEN 'OUT'
                    WHEN TIME(a.date) BETWEEN '13:00:00' AND '14:00:00' THEN 'IN'
                    WHEN TIME(a.date) > '14:00:00' AND TIME(a.date) < '16:00:00' THEN 'LATE'
                    WHEN TIME(a.date) BETWEEN '16:00:00' AND '18:00:00' THEN 'OUT'
                    ELSE 'INVALID'
                END AS status,
                a.date,
                ROW_NUMBER() OVER (PARTITION BY a.student_id, 
                   CASE 
                       WHEN TIME(a.date) BETWEEN '07:00:00' AND '08:00:00' THEN 'morning_in'
                       WHEN TIME(a.date) BETWEEN '12:00:00' AND '12:59:59' THEN 'morning_out'
                       WHEN TIME(a.date) BETWEEN '13:00:00' AND '14:00:00' THEN 'afternoon_in'
                       WHEN TIME(a.date) BETWEEN '16:00:00' AND '18:00:00' THEN 'afternoon_out'
                   END
                   ORDER BY a.date ASC) AS row_num
            FROM attendance a
            INNER JOIN student st ON a.student_id = st.student_id
            INNER JOIN student_section ss ON a.student_id = ss.student_id
            INNER JOIN teacher te ON te.teacher_id = ss.teacher_id
            WHERE te.teacher_id = ? 
            AND DATE(a.date) = CURDATE()";

        if (!empty($search)) {
            $query .= " AND (st.student_firstname LIKE ? OR st.student_lastname LIKE ?)";
        }

        $query .= ") 
                   SELECT student_id, fullname, status, date
                    FROM RankedAttendance
                    WHERE row_num = 1
                    AND status IN ('IN', 'LATE', 'OUT')
                    ORDER BY date ASC
                    LIMIT ?, ?";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Query preparation failed: " . $conn->error);
        }

        if (!empty($search)) {
            $escapedSearch = '%' . $search . '%';
            $stmt->bind_param("issii", $teacher_id, $escapedSearch, $escapedSearch, $start, $length);
        } else {
            $stmt->bind_param("iii", $teacher_id, $start, $length);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $totalQuery = "WITH RankedAttendance AS (
            SELECT 
                a.student_id,
                CONCAT(st.student_firstname, ' ', COALESCE(SUBSTRING(st.student_middlename, 1, 1), ''), '. ', st.student_lastname) AS fullname,
                CASE
                    WHEN TIME(a.date) BETWEEN '07:00:00' AND '08:00:00' THEN 'IN'
                    WHEN TIME(a.date) > '08:00:00' AND TIME(a.date) < '12:00:00' THEN 'LATE'
                    WHEN TIME(a.date) BETWEEN '12:00:00' AND '12:59:59' THEN 'OUT'
                    WHEN TIME(a.date) BETWEEN '13:00:00' AND '14:00:00' THEN 'IN'
                    WHEN TIME(a.date) > '14:00:00' AND TIME(a.date) < '16:00:00' THEN 'LATE'
                    WHEN TIME(a.date) BETWEEN '16:00:00' AND '18:00:00' THEN 'OUT'
                    ELSE 'INVALID'
                END AS status,
                ROW_NUMBER() OVER (PARTITION BY a.student_id, 
                   CASE 
                       WHEN TIME(a.date) BETWEEN '07:00:00' AND '08:00:00' THEN 'morning_in'
                       WHEN TIME(a.date) BETWEEN '12:00:00' AND '12:59:59' THEN 'morning_out'
                       WHEN TIME(a.date) BETWEEN '13:00:00' AND '14:00:00' THEN 'afternoon_in'
                       WHEN TIME(a.date) BETWEEN '16:00:00' AND '18:00:00' THEN 'afternoon_out'
                   END
                   ORDER BY a.date ASC) AS row_num
            FROM attendance a
            INNER JOIN student st ON a.student_id = st.student_id
            INNER JOIN student_section ss ON a.student_id = ss.student_id
            INNER JOIN teacher te ON te.teacher_id = ss.teacher_id
            WHERE te.teacher_id = ? 
            AND DATE(a.date) = CURDATE()";

            if (!empty($search)) {
                $totalQuery .= " AND (st.student_firstname LIKE ? OR st.student_lastname LIKE ?)";
            }

            $totalQuery .= ")
            SELECT COUNT(*) AS total_count
            FROM RankedAttendance
            WHERE row_num = 1";


                $stmtTotal = $conn->prepare($totalQuery);
                if (!$stmtTotal) {
                die("Total query preparation failed: " . $conn->error);
                }

                // Bind parameters
                if (!empty($search)) {
                $escapedSearch = '%' . $search . '%';
                $stmtTotal->bind_param('iss', $teacher_id, $escapedSearch, $escapedSearch);
                } else {
                $stmtTotal->bind_param('i', $teacher_id);
                }

                // Execute query
                $stmtTotal->execute();
                $totalResult = $stmtTotal->get_result();
                $totalRow = $totalResult->fetch_assoc();
                $totalRecords = $totalRow['total_count'];


        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return json_encode([
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data" => $data
        ]);
    }

    $draw = $_POST["draw"];
    $start = $_POST["start"];
    $length = $_POST["length"];
    $search = $_POST["search"]["value"] ?? '';
    $teacher_id = $_SESSION["teacher_id"];

    echo getAttendanceData($draw, $start, $length, $search, $teacher_id, $conn);
    exit();
}





if (isset($_POST['classSec'])) {

    function getDataTable($draw, $start, $length, $search) {
        global $conn;

        $sortableColumns = array('fullname', 'section_name', 'school_year_name');
        
        $orderBy = $sortableColumns[0];
        $orderDir = 'ASC';

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

        $query1 = "SELECT sec.section_name , sc.school_year_name,
                          CONCAT(st.student_firstname, ' ', COALESCE(SUBSTRING(st.student_middlename, 1, 1), ''), '. ', st.student_lastname) AS fullname
                   FROM student st
                   INNER JOIN student_section se ON st.student_id = se.student_id 
                   INNER JOIN section sec ON se.section_id = sec.section_id 
                   INNER JOIN school_year sc ON sc.school_year_id = se.school_year_id";

        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $query1 .= " AND (sec.section_name LIKE '%$escapedSearch%' OR 
                               CONCAT(st.student_firstname, ' ', COALESCE(SUBSTRING(st.student_middlename, 1, 1), ''), '. ', st.student_lastname)  LIKE '%$escapedSearch%' OR 
                              sc.school_year_name LIKE '%$escapedSearch%')";
        }

        $query1 .= " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . intval($start) . ", " . intval($length);

        $result1 = $conn->query($query1);

        $totalQuery1 = "SELECT COUNT(*) AS total_count 
                         FROM student st
                   INNER JOIN student_section se ON st.student_id = se.student_id 
                   INNER JOIN section sec ON se.section_id = sec.section_id 
                   INNER JOIN school_year sc ON sc.school_year_id = se.school_year_id";

        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $totalQuery1 .= " AND (sec.section_name LIKE '%$escapedSearch%' OR 
                               CONCAT(st.student_firstname, ' ', COALESCE(SUBSTRING(st.student_middlename, 1, 1), ''), '. ', st.student_lastname)  LIKE '%$escapedSearch%' OR 
                              sc.school_year_name LIKE '%$escapedSearch%')";
        }

        $totalResult1 = $conn->query($totalQuery1);
        $totalRow1 = $totalResult1->fetch_assoc();
        $totalRecords1 = $totalRow1['total_count'];

        $data = array();
        while ($row = $result1->fetch_assoc()) {
            $data[] = $row;
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords1),
            "recordsFiltered" => intval($totalRecords1),
            "data" => $data
        );

        return json_encode($output);
    }

    $draw = $_POST["draw"];
    $start = $_POST["start"];
    $length = $_POST["length"];
    $search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : '';

    echo getDataTable($draw, $start, $length, $search);    
    exit();
}


if (isset($_POST['mysubject'])) {

    function getsubjectList($draw, $start, $length, $search, $teacher_id) {
        global $conn;

        $sortableColumns = array('section_name', 'grade_level_name');
        
        $orderBy = $sortableColumns[0]; // Default order
        $orderDir = 'ASC'; // Default direction

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            // Ensure order column exists in the array
            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

        // Main query with teacher and subject filters
        $query1 = "SELECT se.section_name, gr.grade_level_name, CONCAT(st.student_firstname, ' ', COALESCE(SUBSTRING(st.student_middlename, 1, 1), ''), '. ', st.student_lastname) AS fullname
                   FROM section se 
                   INNER JOIN grade_level gr ON gr.grade_level = se.grade_level_id 
                   INNER JOIN student_section sec ON se.section_id = sec.section_id
                   INNER JOIN student st ON st.student_id = sec.student_id
                   INNER JOIN teacher te ON te.teacher_id = sec.teacher_id
                   WHERE te.teacher_id = ?";

        // Add search filter
        if (!empty($search)) {
            $query1 .= " AND (se.section_name LIKE ?)";
        }

        $query1 .= " ORDER BY $orderBy $orderDir LIMIT ?, ?";
        $stmt = $conn->prepare($query1);

        if (!empty($search)) {
            $likeSearch = '%' . $search . '%';
            $stmt = $conn->prepare($query1);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("siii", $likeSearch, $teacher_id, $start, $length);
        } else {
            $stmt = $conn->prepare($query1);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iii", $teacher_id, $start, $length);
        }
        

        $stmt->execute();
        $result1 = $stmt->get_result();

        // Count query for total records
        $totalQuery1 = "SELECT COUNT(*) AS total_count
                   FROM section se 
                   INNER JOIN grade_level gr ON gr.grade_level = se.grade_level_id 
                   INNER JOIN student_section sec ON se.section_id = sec.section_id
                   INNER JOIN student st ON st.student_id = sec.student_id
                   INNER JOIN teacher te ON te.teacher_id = sec.teacher_id
                   WHERE te.teacher_id = ?";

        $stmtTotal = $conn->prepare($totalQuery1);
        $stmtTotal->bind_param("i", $teacher_id);
        $stmtTotal->execute();
        $totalResult1 = $stmtTotal->get_result();
        $totalRow1 = $totalResult1->fetch_assoc();
        $totalRecords1 = $totalRow1['total_count'];

        $data = array();
        while ($row = $result1->fetch_assoc()) {
            $data[] = $row;
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalRecords1),
            "recordsFiltered" => intval($totalRecords1),
            "data" => $data
        );

        return json_encode($output);
    }

    $draw = $_POST["draw"];
    $start = $_POST["start"];
    $length = $_POST["length"];
    $search = isset($_POST["search"]["value"]) ? $_POST["search"]["value"] : '';
    $teacher_id = $_SESSION["teacher_id"];
 
    echo getsubjectList($draw, $start, $length, $search, $teacher_id);
    exit();
}


if (isset($_POST['bulkUpdate'])) {
    $student_ids = $_POST['student_ids'];
    $grade_level_id = intval($_POST['grade_level_id']);

    if (!empty($student_ids)) {
        $placeholders = implode(',', array_fill(0, count($student_ids), '?'));
        $stmt = $conn->prepare("UPDATE student SET grade_level_id = ? WHERE student_id IN ($placeholders)");
        $types = str_repeat('i', count($student_ids) + 1);
        $params = array_merge([$grade_level_id], $student_ids);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo "Updated Successfully";
        } else {
            echo "Update Failed";
        }
        $stmt->close();
    } else {
        echo "No Students Selected";
    }
}



if (isset($_POST['absent'])) {
    function getAbsentData($conn, $from_date, $to_date) {
        $from_date = $conn->real_escape_string($from_date);
        $to_date = $conn->real_escape_string($to_date);
        $teacher_id = $_SESSION['teacher_id']; 


        $query = "SELECT 
    st.student_id, 
    CONCAT(
        st.student_firstname, 
        ' ', 
        COALESCE(SUBSTRING(st.student_middlename, 1, 1), ''), 
        '. ', 
        st.student_lastname
    ) AS fullname, 
    COUNT(DISTINCT CASE WHEN a.status = 'ABSENT' THEN a.date END) AS absent_days, 
    GROUP_CONCAT(DISTINCT CASE WHEN a.status = 'ABSENT' THEN a.date END ORDER BY a.date ASC) AS absent_dates
            FROM 
                student st
            LEFT JOIN 
                attendance a 
            ON 
                st.student_id = a.student_id 
                AND a.date BETWEEN ? AND ?
            INNER JOIN 
                student_section ss 
            ON 
                st.student_id = ss.student_id 
            WHERE 
                ss.teacher_id = ?
            GROUP BY 
                st.student_id 
            ORDER BY 
                absent_days DESC";


        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $from_date, $to_date, $teacher_id); 
        

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return json_encode([
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        ]);
    }


    $from_date = $_POST['from'];
    $to_date = $_POST['to'];


    echo getAbsentData($conn, $from_date, $to_date);
    exit();
}

