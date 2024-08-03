<?php
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

        $query1 = "SELECT *, CONCAT(student_firstname, ' ', COALESCE(SUBSTRING(student_middlename, 1, 1), ''), '. ', student_lastname) AS fullname 
                   FROM student 
                   WHERE 1=1";

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
                                <a href="' . $qrFileName . '" download class="btn btn-primary mt-2">Download QR</a>
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

        $sortableColumns = array('teacher_name');
        
        $orderBy = $sortableColumns[0];
        $orderDir = 'ASC';

        if (isset($_POST['order'][0]['column']) && isset($_POST['order'][0]['dir'])) {
            $columnIdx = intval($_POST['order'][0]['column']);
            $orderDir = $_POST['order'][0]['dir'];

            if (isset($sortableColumns[$columnIdx])) {
                $orderBy = $sortableColumns[$columnIdx];
            }
        }

        $query1 = "SELECT * FROM teacher WHERE 1=1";

        if (!empty($search)) {
            $escapedSearch = $conn->real_escape_string($search);
            $query1 .= " AND (teacher_name LIKE '%$escapedSearch%' OR teacher_address LIKE '%$escapedSearch%')";
        }
        

    
        $query1 .= " ORDER BY " . $orderBy . " " . $orderDir . " LIMIT " . intval($start) . ", " . intval($length);

        $result1 = $conn->query($query1);

        $totalQuery1 = "SELECT COUNT(*) AS total_count 
                        FROM teacher 
                        WHERE 1=1";

        if (!empty($search)) {
         $escapedSearch = $conn->real_escape_string($search);
        $totalQuery1 .= " AND (teacher_name LIKE '%$escapedSearch%' OR teacher_address LIKE '%$escapedSearch%')";
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
    $search = $_POST["search"]["value"];

    echo getDataTable($draw, $start, $length, $search);    
    exit();
}
?>
