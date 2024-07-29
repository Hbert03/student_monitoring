<?php

function fetchData() {
    global $conn; 
    include('database.php'); 
    $data = array('labels' => array(), 'values' => array());

    $currentMonthLabel = date('Y'); 
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
                $data['labels'][] = $gradeNames[$index] . ' in ' . $currentMonthLabel; 
                $data['values'][] = $total;
            }
            $stmt->close();
        } else {
            // Handle SQL preparation error
            echo "SQL preparation error: " . $conn->error;
        }
    }

    $conn->close();
    return $data;   
}

$data = fetchData();

header('Content-Type: application/json');
echo json_encode($data);

?>
