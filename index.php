<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="./styles/index.css?v=2" rel="stylesheet">
</head>
<body>
    <?php
    require("navbar.php");
    if(!isset($_SESSION["email"])){
        header("Location: login.php");
    } else {
        $email = $_SESSION["email"];
        require_once("connect.php");
        if(isset($_SESSION["sid"])){
            // User is a student
            require_once("student.php");
            $student = new Student($email, $_SESSION["sid"]);
    ?>
    <div class="main-container">
        <h3>These are the ongoing iterations linked with you.</h3>
        <ul>
            <?php
            $count = 0;
            foreach ($student->ongoingIterations as $iteration) {
                $iid = $iteration["id"];
                $assno = $iteration["assno"];
                $assname = $iteration["assname"];
                echo "<li><a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname}</a></li>";
                $count++;
            }
            if (!$count){
                echo "Nothing to display here!";
            }
            ?>
        </ul>
        <h3>These are the completed iterations linked with you.</h3>
        <ul>
            <?php
            $count = 0;
            foreach ($student->completeIterations as $iteration) {
                $iid = $iteration["id"];
                $assno = $iteration["assno"];
                $assname = $iteration["assname"];
                $sidIteration = $iteration["sid"];
                echo "<li><a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname}</a></li>";
                $count++;
            }
            if (!$count){
                echo "Nothing to display here!";
            }
            ?>
        </ul>
    </div>
    <?php
        } else {
            // User is a reviewer
            require_once("reviewer.php");
            $reviewer = new Reviewer($email, $_SESSION["rid"]);
            $invalid_email = FALSE;
            if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add-iteration-button'])){
                $sql = "INSERT INTO iteration(sid,rid,assno,assname,review,complete,link) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connection_response, $sql);
                mysqli_stmt_bind_param($stmt, "iiisiis", $student_id, $reviewer_id, $assno, $assname, $review, $complete, $link);
                $assname = $_POST['assignment-name-input'];
                $assno =  $_POST['assignment-number-input'];
                if(!isset($_POST['everyone-option'])){
                    $student_email = $_POST['email-input'];
                    $email_query = mysqli_query($connection_response, "SELECT id FROM student WHERE email = '{$student_email}'");
                    if(mysqli_num_rows($email_query) == 0){
                        $invalid_email = TRUE;
                    } else {
                        $row = mysqli_fetch_assoc($email_query);
                        $student_id = $row['id'];
                        $reviewer_id = $reviewer->rid;
                        $review = 0;
                        $complete = 0;
                        $link = NULL;
                        mysqli_stmt_execute($stmt);
                        header("Refresh:0");
                    }
                } else {
                    $students_query = mysqli_query($connection_response, "SELECT id FROM student");
                    while ($row = mysqli_fetch_assoc($students_query)){
                        $student_id = $row['id'];
                        $reviewer_id = $reviewer->rid;
                        $review = 0;
                        $complete = 0;
                        $link = NULL;
                        mysqli_stmt_execute($stmt);
                    }
                    header("Refresh:0");
                }
            }
    ?>
    <div class="main-container">
        <div class="iteration-container">
            <h5>Assign Assignments to Students</h5>
            <form method="post" class="row g-2">
                <div class="form-floating">
                <input type="text" class="form-control" id="floatingInput" name="assignment-name-input" placeholder="Assignment Name">
                <label for="floatingInput">Enter Assignment Name</label>
                </div>
                <div class="form-floating mb-2">
                    <input type="number" class="form-control" id="floatingInput" name="assignment-number-input" placeholder="Assignment Number">
                    <label for="floatingInput">Enter Assignment No.</label>
                </div>
                <h6>Whom to Assign</h6>
                <div>
                    <input type="checkbox" id="everyone-option" name="everyone-option" value="Everyone">
                    <label for="everyone-option">Every Student</label>
                </div>
                <h6>OR</h6>
                <div class="form-floating mb-2">
                    <input type="email" class="form-control" id="floatingInput" name="email-input" placeholder="student@example.com">
                    <label for="floatingInput">Student's Email address</label>
                </div>
                <?php
                  if ($invalid_email){
                ?>
                <span style="color:red;">Invalid student email.</span>
                <?php
                  }
                ?>
                <button type="submit" class="btn btn-success" name="add-iteration-button">
                    Assign Assignment Now!
                </button>
            </form>
        </div>
        <h3>Review requested for these iterations.</h3>
        <ul>
            <?php
            $count = 0;
            foreach ($reviewer->iterationAssigned as $iteration) {
                $iid = $iteration["id"];
                $assname = $iteration["assname"];
                $assno = $iteration["assno"];
                $sidIteration = $iteration["sid"];
                $review = $iteration["review"];
                $complete = $iteration["complete"];
                if ($review && !$complete){
                    $count ++;
                    echo "<li><a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname} (Student ID: {$sidIteration})</a></li>";
                }
            }
            if (!$count){
                echo "No iterations to show here!";
            }
            ?>
        </ul>
        <h3>Incomplete status for these iterations.</h3>
        <ul>
            <?php
            $count = 0;
            foreach ($reviewer->iterationAssigned as $iteration) {
                $iid = $iteration["id"];
                $assname = $iteration["assname"];
                $assno = $iteration["assno"];
                $sidIteration = $iteration["sid"];
                $review = $iteration["review"];
                $complete = $iteration["complete"];
                if (!$review && !$complete){
                    $count ++;
                    echo "<li><a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname} (Student ID: {$sidIteration})</a></li>";
                }
            }
            if (!$count){
                echo "No iterations to show here!";
            }
            ?>
        </ul>
        <h3>Iterations you marked as complete.</h3>
        <ul>
            <?php
            $count = 0;
            foreach ($reviewer->iterationAssigned as $iteration) {
                $iid = $iteration["id"];
                $assname = $iteration["assname"];
                $assno = $iteration["assno"];
                $sidIteration = $iteration["sid"];
                $review = $iteration["review"];
                $complete = $iteration["complete"];
                if ($complete){
                    $count ++;
                    echo "<li><a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname} (Student ID: {$sidIteration})</a></li>";
                }
            }
            if (!$count){
                echo "No iterations to show here!";
            }
            ?>
        </ul>
        <h3>Registered Students</h3>
        <ul>
            <?php
            $students_query = mysqli_query($connection_response, "SELECT id, email, name FROM student");
            while ($row = mysqli_fetch_assoc($students_query)){
                $student_id = $row['id'];
                $student_email = $row['email'];
                $student_name = $row['name'];
                echo "<li><a href='./profile.php?email={$student_email}&sid={$student_id}'>{$student_name} ($student_email)</a></li>";
            }
            ?>
        </ul>
    </div>
    <?php
        }
    }
    ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>
</html>