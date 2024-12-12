<?php


class Junior_High_Male {
private $total2;

public function __construct() {
    include('database.php');

   $sql = "SELECT COUNT(*) as total FROM student WHERE grade_level_id IN (6,7,8,9,10) and gender = 1";

    
    $query = $conn->query($sql);
    $juniorDatam = $query->fetch_assoc();

    if ($juniorDatam) {
        $this->totalJuniorm = $juniorDatam['total'];
    }
}

public function getValue($part) {
    switch ($part) {
        case "totalJuniorm":
            return $this->totalJuniorm;
        default:
            return null; 
    }
}

}

class Junior_High_Female {
    private $total2;
    
    public function __construct() {
        include('database.php');
    
       $sql = "SELECT COUNT(*) as total FROM student WHERE grade_level_id IN (6,7,8,9,10) and gender = 2";
    
        
        $query = $conn->query($sql);
        $juniorDataf = $query->fetch_assoc();
    
        if ($juniorDataf) {
            $this->totalJuniorf = $juniorDataf['total'];
        }
    }
    
    public function getValue($part) {
        switch ($part) {
            case "totalJuniorf":
                return $this->totalJuniorf;
            default:
                return null; 
        }
    }
    
    }
    

class Senior_High_Male {
    private $total4;
    
    public function __construct() {
        include('database.php');
    
       $sql = "SELECT COUNT(*) as total FROM student WHERE grade_level_id IN (11, 12) and gender ='1'";
    
        
        $query = $conn->query($sql);
        $seniorDatam = $query->fetch_assoc();
    
        if ($seniorDatam) {
            $this->totalSeniorm = $seniorDatam['total'];
        }
    }
    
    public function getValue($part) {
        switch ($part) {
            case "totalSeniorm":
                return $this->totalSeniorm;
            default:
                return null; 
        }
    }
}    


class Senior_High_Female {
    private $total4;
    
    public function __construct() {
        include('database.php');
    
       $sql = "SELECT COUNT(*) as total FROM student WHERE grade_level_id IN (11, 12) and gender ='2'";
    
        
        $query = $conn->query($sql);
        $seniorDataf = $query->fetch_assoc();
    
        if ($seniorDataf) {
            $this->totalSeniorf = $seniorDataf['total'];
        }
    }
    
    public function getValue($part) {
        switch ($part) {
            case "totalSeniorf":
                return $this->totalSeniorf;
            default:
                return null; 
        }
    }
}   
?>