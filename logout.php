<?php
    require_once("connect.php");
    if (isset($_POST["logout-btn"]) && $_SERVER["REQUEST_METHOD"] === "POST"){
        $token = $_COOKIE['token'];
        $query = mysqli_query($connection_response, "DELETE FROM token WHERE token = '{$token}';");
        setcookie("token", "", time() - 3600, "/");
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
?>