<?php
    if (isset($_POST["logout-btn"]) && $_SERVER["REQUEST_METHOD"] === "POST"){
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
?>