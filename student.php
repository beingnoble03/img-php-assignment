<?php
require_once("IMG_Member.php");
class Student extends IMG_Member {
    public $sid;
    public $ongoingIterations;
    public $completeIterations;

    function __construct($email, $sid){
        $this->email = $email;
        $this->sid = $sid;
        $connection_response = mysqli_connect("localhost", "root", "password", "assignment");
        $query = mysqli_query($connection_response, "SELECT * FROM details WHERE email = '{$email}'");
        $row = mysqli_fetch_assoc($query);
        $this->year = $row['year'];
        $this->branch = $row['branch'];
        $this->name = $row['name'];
        $this->completeIterations = array();
        $this->ongoingIterations = array();
        $query = mysqli_query($connection_response, "SELECT * FROM iteration WHERE sid = {$sid};");
        while ($row = mysqli_fetch_assoc($query)){
            if ($row['complete']){
                $this->completeIterations[] = $row; 
            } else {
                $this->ongoingIterations[] = $row; 
            }
        }
    }
}
?>