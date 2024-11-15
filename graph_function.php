<?php

include('database.php'); 

function fetchEnrolledData() {
    global $conn;

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $data = array('labels' => array(), 'values' => array());
    $currentYearLabel = date('Y'); 
    $gradeLevels = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    $gradeNames = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    foreach ($gradeLevels as $index => $gradeLevelId) {
        $sql = "SELECT COUNT(*) as total 
                FROM student 
                WHERE YEAR(date) = YEAR(CURDATE()) AND grade_level_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $gradeLevelId);
            $stmt->execute();
            $stmt->bind_result($total);
            while ($stmt->fetch()) {
                $data['labels'][] = $gradeNames[$index] . ' (' . $currentYearLabel . ')'; 
                $data['values'][] = $total;
            }
            $stmt->close();
        } else {
            echo "SQL preparation error: " . $conn->error;
        }
    }

    return $data;   
}

function fetchAbsentData() {
    global $conn;

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $data = array('labels' => array(), 'values' => array());
    $currentYearLabel = date('Y'); 
    $gradeLevels = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    $gradeNames = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    foreach ($gradeLevels as $index => $gradeLevelId) {
        $sql = "SELECT COUNT(*) AS absent_total
                    FROM student s
                    LEFT JOIN attendance a 
                        ON s.student_id = a.student_id 
                        AND a.status = 'IN' 
                        AND DATE(a.date) = CURDATE()
                    WHERE a.student_id IS NULL 
                    AND s.grade_level_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $gradeLevelId);
            $stmt->execute();
            $stmt->bind_result($absent_total);
            while ($stmt->fetch()) {
                $data['labels'][] = $gradeNames[$index] . ' (' . $currentYearLabel . ')';
                $data['values'][] = $absent_total;
            }
            $stmt->close();
        } else {
            echo "SQL preparation error: " . $conn->error;
        }
    }

    return $data;
}

if ($_GET['type'] === 'absent') {
    $data = fetchAbsentData();
} else {
    $data = fetchEnrolledData();
}

header('Content-Type: application/json');
echo json_encode($data);

// Ensure to close connection only after all functions
$conn->close();


?>
