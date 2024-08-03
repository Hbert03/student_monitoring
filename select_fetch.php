<?php
include('database.php');

if (isset($_POST['gradelevel'])) {
    $query = "SELECT * FROM grade_level WHERE 1=1";

    $terms = (isset($_POST['term']) && !empty($_POST['term'])) ? $_POST['term'] : null;

    if ($terms) {
        $query .= " AND grade_level_name LIKE ?";
    } else {
        $query .= " LIMIT 12";
    }

    $stmt = $conn->prepare($query);

    if ($terms) {
        $like_terms = "%" . $terms . "%";
        $stmt->bind_param("s", $like_terms);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $school = array();

    while ($row = $result->fetch_assoc()) {
        $school[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['results' => $school]);
}


if (isset($_POST['subject'])) {
    $query = "SELECT * FROM subject WHERE 1=1";

    $terms = (isset($_POST['term']) && !empty($_POST['term'])) ? $_POST['term'] : null;

    if ($terms) {
        $query .= " AND subject_name LIKE ?";
    } else {
        $query .= " LIMIT 25";
    }

    $stmt = $conn->prepare($query);

    if ($terms) {
        $like_terms = "%" . $terms . "%";
        $stmt->bind_param("s", $like_terms);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $school = array();

    while ($row = $result->fetch_assoc()) {
        $school[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['results' => $school]);
}



if (isset($_POST['teacher'])) {
    $query = "SELECT * FROM teacher WHERE 1=1";

    $terms = (isset($_POST['term']) && !empty($_POST['term'])) ? $_POST['term'] : null;

    if ($terms) {
        $query .= " AND teacher_name LIKE ?";
    } else {
        $query .= " LIMIT 25";
    }

    $stmt = $conn->prepare($query);

    if ($terms) {
        $like_terms = "%" . $terms . "%";
        $stmt->bind_param("s", $like_terms);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $school = array();

    while ($row = $result->fetch_assoc()) {
        $school[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['results' => $school]);
}



if (isset($_POST['section'])) {
    $query = "SELECT * FROM section WHERE 1=1";

    $terms = (isset($_POST['term']) && !empty($_POST['term'])) ? $_POST['term'] : null;

    if ($terms) {
        $query .= " AND section_name LIKE ?";
    } else {
        $query .= " LIMIT 25";
    }

    $stmt = $conn->prepare($query);

    if ($terms) {
        $like_terms = "%" . $terms . "%";
        $stmt->bind_param("s", $like_terms);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $school = array();

    while ($row = $result->fetch_assoc()) {
        $school[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['results' => $school]);
}



if (isset($_POST['school_year'])) {
    $query = "SELECT * FROM school_year WHERE 1=1";

    $terms = (isset($_POST['term']) && !empty($_POST['term'])) ? $_POST['term'] : null;

    if ($terms) {
        $query .= " AND school_year_name LIKE ?";
    } else {
        $query .= " LIMIT 25";
    }

    $stmt = $conn->prepare($query);

    if ($terms) {
        $like_terms = "%" . $terms . "%";
        $stmt->bind_param("s", $like_terms);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $school = array();

    while ($row = $result->fetch_assoc()) {
        $school[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['results' => $school]);
}


if(isset($_POST['addStudent1'])){
    $empquery = "SELECT *, CONCAT(student_firstname, ' ', COALESCE(SUBSTRING(student_middlename, 1, 1), ''), '. ', student_lastname) AS fullname FROM student WHERE 1=1";

    $terms = (isset($_POST['term']) && !empty($_POST['term'])) ? $_POST['term'] : null;

    if($terms){
        $empquery .= " AND (student_firstname LIKE '%" . $terms . "%'";
        $empquery .= " OR student_middlename LIKE '%" . $terms . "%'";
        $empquery .= " OR student_lastname LIKE '%" . $terms . "%')";
    } else {
        $empquery .= " LIMIT 10";
    }

    $query = $conn->query($empquery);
    $school = array();

    while ($row = $query->fetch_assoc()) {
        $school[] = $row;
    }

    $conn->close();

    echo json_encode(['results' => $school]);
};
?>