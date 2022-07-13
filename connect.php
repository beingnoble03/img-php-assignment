<?php
$sname = "localhost";
$uname = "root";
$password = "password";
$db_name = "assignment";
$connection_response = mysqli_connect($sname, $uname, $password, $db_name);
if (!$connection_response) {
    echo "Connection Failed.";
}
?>