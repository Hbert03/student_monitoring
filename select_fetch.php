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
    $district = array();

    while ($row = $result->fetch_assoc()) {
        $district[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['results' => $district]);
}



?>