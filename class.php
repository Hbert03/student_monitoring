<?php
class Elementary {
    private $total1;

    public function __construct() {
        include('database.php');

       $sql = "SELECT COUNT(*) as total FROM student WHERE grade_level_id IN (1, 2, 3, 4, 5, 6)";

        
        $query = $conn->query($sql);
        $elemsData = $query->fetch_assoc();

        if ($elemsData) {
            $this->totalElems = $elemsData['total'];
        }
    }

    public function getValue($part) {
        switch ($part) {
            case "totalElems":
                return $this->totalElems;
            default:
                return null; 
        }
    }
}

?>