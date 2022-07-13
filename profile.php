<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="./styles/profile.css" rel="stylesheet">
    <title>Profile</title>
</head>
<body>
<?php
session_start();
require("navbar.php");
if (isset($_GET['sid']) && isset($_GET['email'])){
    //student profile
    require_once("student.php");
    $student = new Student("{$_GET['email']}", $_GET['sid']);
?>
<div class="main-container">
    <h2><?php echo"{$student->name}" ?></h2><br>
    <h5><span style="color:grey;">Student ID: </span>#<?php echo"{$student->sid}" ?></h5>
    <h5><span style="color:grey;">Branch: </span> <?php echo"{$student->branch}" ?></h5>
    <h5><span style="color:grey;">Year: </span> <?php echo"{$student->year}" ?></h5>
    <h5><span style="color:grey;">Email: </span> <?php echo"{$student->email}" ?></h5>
    <h5>Completed Assignments</h5>
        <?php
        $count = 0;
        foreach ($student->completeIterations as $iteration) {
            $iid = $iteration["id"];
            $assno = $iteration["assno"];
            $assname = $iteration["assname"];
            $sidIteration = $iteration["sid"];
            echo "<a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname}</a><br>";
            $count++;
        }
        if (!$count){
            echo "Nothing to display here!";
        }
        ?>
    <h5 style="margin-top:10px;">Ongoing Assignments</h5>
        <?php
        $count =0;
        foreach ($student->ongoingIterations as $iteration) {
            $iid = $iteration["id"];
            $assno = $iteration["assno"];
            $assname = $iteration["assname"];
            echo "<a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname}</a><br>";
            $count++;
        }
        if (!$count){
            echo "Nothing to display here!";
        }
        ?>
</div>
<?php
} else if (isset($_GET['rid']) && isset($_GET['email'])){
    //reviewer profile
    require_once("reviewer.php");
    $reviewer = new Reviewer("{$_GET['email']}", $_GET['rid']);
?>
<div class="main-container">
    <h2><?php echo"{$reviewer->name}" ?></h2><br>
    <h5><span style="color:grey;">Reviewer ID: </span>#<?php echo"{$reviewer->rid}" ?></h5>
    <h5><span style="color:grey;">Branch: </span> <?php echo"{$reviewer->branch}" ?></h5>
    <h5><span style="color:grey;">Year: </span> <?php echo"{$reviewer->year}" ?></h5>
    <h5><span style="color:grey;">Email: </span> <?php echo"{$reviewer->email}" ?></h5>
    <h5>Review Requested Iterations</h5>
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
                echo "<a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname} (Student ID: {$sidIteration})</a><br>";
            }
        }
        if (!$count){
            echo "No iterations to show here!";
        }
        ?>
    <h5 style="margin-top:10px;">Incomplete Iterations Assigned</h5>
        <?php
        $count =0;
        foreach ($reviewer->iterationAssigned as $iteration) {
            $iid = $iteration["id"];
            $assname = $iteration["assname"];
            $assno = $iteration["assno"];
            $sidIteration = $iteration["sid"];
            $review = $iteration["review"];
            $complete = $iteration["complete"];
            if (!$review && !$complete){
                $count ++;
                echo "<a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname} (Student ID: {$sidIteration})</a><br>";
            }
        }
        if (!$count){
            echo "No iterations to show here!";
        }
        ?>
    <h5 style="margin-top:10px;">Iterations Marked As Completed</h5>
        <?php
        $count =0;
        foreach ($reviewer->iterationAssigned as $iteration) {
            $iid = $iteration["id"];
            $assname = $iteration["assname"];
            $assno = $iteration["assno"];
            $sidIteration = $iteration["sid"];
            $review = $iteration["review"];
            $complete = $iteration["complete"];
            if ($complete){
                $count ++;
                echo "<a href= './iteration.php?iid={$iid}'>[{$assno}] {$assname} (Student ID: {$sidIteration})</a><br>";
            }
        }
        if (!$count){
            echo "No iterations to show here!";
        }
        ?>
</div>

<?php
} else {
    header('Location: index.php');
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>
</html>