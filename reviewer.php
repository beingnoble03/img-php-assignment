<?php
require_once("connect.php");
require_once("IMG_Member.php");
class Reviewer extends IMG_Member {
    public $rid;
    public $iterationAssigned;

    function __construct($email, $rid){
        $this->email = $email;
        $this->rid = $rid;
        $connection_response = $GLOBALS['connection_response'];
        $query = mysqli_query($connection_response, "SELECT * FROM details WHERE email = '{$email}'");
        $row = mysqli_fetch_assoc($query);
        $this->year = $row['year'];
        $this->branch = $row['branch'];
        $this->name = $row['name'];
        $this->iterationAssigned = array();
        $query = mysqli_query($connection_response, "SELECT * FROM iteration WHERE rid = {$rid};");
        while ($row = mysqli_fetch_assoc($query)){
            $this->iterationAssigned[] = $row; 
        }
    }
}
?>