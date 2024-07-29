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

class Junior_High {
private $total2;

public function __construct() {
    include('database.php');

   $sql = "SELECT COUNT(*) as total FROM student WHERE grade_level_id IN (6,7,8,9,10)";

    
    $query = $conn->query($sql);
    $juniorData = $query->fetch_assoc();

    if ($juniorData) {
        $this->totalJunior = $juniorData['total'];
    }
}

public function getValue($part) {
    switch ($part) {
        case "totalJunior":
            return $this->totalJunior;
        default:
            return null; 
    }
}

}

class Senior_High {
    private $total3;
    
    public function __construct() {
        include('database.php');
    
       $sql = "SELECT COUNT(*) as total FROM student WHERE grade_level_id IN (11, 12)";
    
        
        $query = $conn->query($sql);
        $seniorData = $query->fetch_assoc();
    
        if ($seniorData) {
            $this->totalSenior = $seniorData['total'];
        }
    }
    
    public function getValue($part) {
        switch ($part) {
            case "totalSenior":
                return $this->totalSenior;
            default:
                return null; 
        }
    }
}    
?>