    <?php
    include ('database.php');

    if (isset($_GET['task']) && $_GET['task'] === 'markAbsent') {

        // Get the list of all students
        $sql_get_students = "SELECT student_id FROM student";  
        $stmt_get_students = $conn->prepare($sql_get_students);
        $stmt_get_students->execute();
        $result = $stmt_get_students->get_result();
        
        // Loop through each student and check attendance
        while ($row = $result->fetch_assoc()) {
            $student_id = $row['student_id'];
            markAbsentIfNoScan($student_id, $conn);
        }
    }


    function markAbsentIfNoScan($student_id, $conn) {
        $current_date = date('Y-m-d');
    
        // Morning attendance check (12:00:00)
        $sql_check_morning = "
            SELECT * 
            FROM attendance 
            WHERE student_id = ? 
            AND DATE(date) = ? 
            AND TIME(date) = '12:00:00'
        ";
        $stmt_check_morning = $conn->prepare($sql_check_morning);
        $stmt_check_morning->bind_param("is", $student_id, $current_date);
        $stmt_check_morning->execute();
        $result_morning = $stmt_check_morning->get_result();
    
        if ($result_morning->num_rows == 0) {
            $sql_absent_morning = "INSERT INTO attendance (student_id, status, date) VALUES (?, 'ABSENT', NOW())";
            $stmt_absent_morning = $conn->prepare($sql_absent_morning);
            $stmt_absent_morning->bind_param("i", $student_id);
            $stmt_absent_morning->execute();
            $stmt_absent_morning->close();
        }
    
        $stmt_check_morning->close();
        $sql_check_afternoon = "
            SELECT * 
            FROM attendance 
            WHERE student_id = ? 
            AND DATE(date) = ? 
            AND TIME(date) = '17:00:00'
        ";
        $stmt_check_afternoon = $conn->prepare($sql_check_afternoon);
        $stmt_check_afternoon->bind_param("is", $student_id, $current_date);
        $stmt_check_afternoon->execute();
        $result_afternoon = $stmt_check_afternoon->get_result();
    
        if ($result_afternoon->num_rows == 0) {
            $sql_absent_afternoon = "INSERT INTO attendance (student_id, status, date) VALUES (?, 'ABSENT', NOW())";
            $stmt_absent_afternoon = $conn->prepare($sql_absent_afternoon);
            $stmt_absent_afternoon->bind_param("i", $student_id);
            $stmt_absent_afternoon->execute();
            $stmt_absent_afternoon->close();
        }
    
        $stmt_check_afternoon->close();
    }
    
    ?>
