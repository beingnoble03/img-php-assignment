<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iteration Info</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="./styles/iteration.css?v=3" rel="stylesheet">
</head>
<body>
<?php
session_start();
require_once("connect.php");
class Iteration {
    public $id;
    public $sid;
    public $rid;
    public $assno;
    public $assname;
    public $review;
    public $complete;
    public $link;

    function __construct($id, $sid, $rid, $assno, $assname, $review, $complete, $link){
        $this->id = $id;
        $this->sid = $sid;
        $this->rid = $rid;
        $this->assno = $assno;
        $this->assname = $assname;
        $this->review = $review;
        $this->complete = $complete;
        $this->link = $link;
    }

    function set_rid($rid, $connection_response){
        $this->rid = $rid;
        $query = mysqli_query($connection_response, "UPDATE iteration SET rid = {$rid} WHERE id = {$this->id}");
    }

    function set_review($review, $connection_response){
        $this->review = $review;
        $query = mysqli_query($connection_response, "UPDATE iteration SET review = {$review} WHERE id = {$this->id}");
    }

    function set_complete($complete, $connection_response){
        $this->complete = $complete;
        $query = mysqli_query($connection_response, "UPDATE iteration SET complete = {$complete} WHERE id = {$this->id}");
    }

    function set_link($link, $connection_response){
        $this->link = $link;
        $query = mysqli_query($connection_response, "UPDATE iteration SET link = '{$link}' WHERE id = {$this->id}");
    }
}

if (isset($_POST['comment'])){
    $message = $_POST['comment'];
    $message = htmlspecialchars($message);
    $email = $_SESSION['email'];
    $name = $_SESSION['name'];
    $iid = $_GET['iid'];
    $sql = "INSERT INTO comments VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection_response, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $iid, $email, $message, $name);
    mysqli_stmt_execute($stmt);
}

function email_to_rid($email, $connection_response){
    $query = mysqli_query($connection_response, "SELECT id FROM reviewer WHERE email = '{$email}'");
    if(mysqli_num_rows($query) === 0){
        return FALSE;
    } else {
        $row = mysqli_fetch_assoc($query);
        return $row['id'];
    }
}
$invalid_email = FALSE;
if (isset($_COOKIE['token'])){
    $token = $_COOKIE['token'];
    $query = mysqli_query($connection_response, "SELECT * FROM token WHERE token = '{$token}'");
    if (mysqli_num_rows($query) === 1){
      $row = mysqli_fetch_assoc($query);
      $email = $row["email"];
      $query = mysqli_query($connection_response, "SELECT * FROM student WHERE email = '{$email}'");
      if (mysqli_num_rows($query) === 1){
        $row = mysqli_fetch_assoc($query);
        $_SESSION["name"] = $row["name"];
        $_SESSION["sid"] = $row["id"];
        $_SESSION["email"] = $row["email"];
      } else {
        $query = mysqli_query($connection_response, "SELECT * FROM reviewer WHERE email = '{$email}'");
        if (mysqli_num_rows($query) === 1){
          $row = mysqli_fetch_assoc($query);
          $_SESSION["name"] = $row["name"];
          $_SESSION["rid"] = $row["id"];
          $_SESSION["email"] = $row["email"];
        }
      }
    }
  }
if(!isset($_GET["iid"]) || !isset($_SESSION["email"])){
    header("Location: index.php");
} else {
    $id = $_GET["iid"];
    $query = mysqli_query($connection_response, "SELECT * FROM iteration WHERE id = {$id}");
    $row = mysqli_fetch_assoc($query);
    $iteration = new Iteration($id, $row["sid"], $row["rid"], $row["assno"], $row["assname"], $row["review"], $row["complete"], $row["link"]);
    $student_query = mysqli_query($connection_response, "SELECT * FROM student WHERE id = {$iteration->sid}");
    $student = mysqli_fetch_assoc($student_query);
    $reviewer_query = mysqli_query($connection_response, "SELECT * FROM reviewer WHERE id = {$iteration->rid}");
    $reviewer = mysqli_fetch_assoc($reviewer_query);
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['update-link-btn'])){
        $iteration->set_link($_POST['inputLink'], $connection_response);
    }
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['review-btn'])){
        $iteration->set_review(1, $connection_response);
    }
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['unreview-btn']) && isset($_SESSION['rid'])){
        $iteration->set_review(0, $connection_response);
    }
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['complete-btn']) && isset($_SESSION['rid'])){
        $iteration->set_complete(1, $connection_response);
    }
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['incomplete-btn']) && isset($_SESSION['rid'])){
        $iteration->set_complete(0, $connection_response);
    }
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['reviewer-change-btn'])){
        $rid = email_to_rid($_POST['inputEmail'], $connection_response);
        if ($rid){
            $iteration->set_rid($rid, $connection_response);
        } else {
            $invalid_email = TRUE;
        }
    }
    require("navbar.php");

?>
<div class="main-container">
    <?php echo "<h3><span style='color: grey;'>#{$iteration->id} </span>{$iteration->assname}</h3>"; ?>
    <!-- Badges -->
    <?php
    $student_email = $student['email'];
    $reviewer_email = $reviewer['email'];
    echo "<h6 style = 'margin-bottom: 10px;'><span style = 'color:grey;'>Student: </span><a href= './profile.php?email={$student_email}&sid={$iteration->sid}'><span>{$student['name']} ({$student['email']})</a> </span> & <span style = 'color:grey;'>Reviewer: </span><span><a href= './profile.php?email={$reviewer_email}&rid={$iteration->rid}'>{$reviewer['name']} ({$reviewer['email']})</a></span></h6>";
    if ($iteration->link){
        echo "<h6><span style='color: grey;'>Github Repo: </span><a href='{$iteration->link}'>Project's Link</a></h6>";
    } else {
        echo "<h6><span style='color: grey;'>Github Repo: </span> Link not provided yet</h6>";
    }
    if ($iteration->complete) {
        echo '<span class="badge text-bg-success" style="margin-right: 10px;">Assignment Status: Completed</span>';
    } else {
        echo '<span class="badge text-bg-warning" style="margin-right: 10px;">Assignment Status: In Progress</span>';
    }
    if ($iteration->review) {
        echo '<span class="badge text-bg-primary">Review Status: Review Requested</span>';
    } else {
        echo '<span class="badge text-bg-danger">Review Status: Not marked for review</span>';
    }
    echo "<br>";
    ?>
    <!-- comments section -->
    <div class="comment-section">
        <h4 style = "margin-bottom: 20px">Comments</h4>
        <?php
        $comments = array();
        $comments_query = mysqli_query($connection_response, "SELECT * FROM comments WHERE iid = {$iteration->id}");
        while ($row = mysqli_fetch_assoc($comments_query)){
            $comments[] = $row; 
        }
        foreach ($comments as $comment){
            $comment_comment = $comment['comment'];
            echo "<div class='comment'><h6>{$comment['name']} <span style = 'color: grey;'>({$comment['email']})</h6>{$comment_comment}</div><hr>";
        }
        ?>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Write your comment" aria-label="comment" id="comment-input">
            <button class="btn btn-dark" type="button" id="comment-btn" onclick= "addComment();">Add Comment</button>
        </div>
    </div>
    <div>
    <?php
        if (isset($_SESSION["rid"]) && $_SESSION["rid"] === $iteration->rid){
            // for reviewer code will be here
            echo "<h5>For Reviewer:</h5><form method='post' style= 'margin-bottom: 20px;'>";
            if($iteration->complete == 1){
                echo "<button class='btn btn-warning btn-sm' type='submit' name='incomplete-btn'>Mark this as incomplete now</button>";
            } else {
                echo "<button class='btn btn-success btn-sm' type='submit' name='complete-btn'>Mark this as completed now</button>";
            }
            if($iteration->review == 1){
                echo "<button class='btn btn-danger btn-sm' type='submit' name='unreview-btn' style='margin-left: 10px;'>Ask student to address comments</button>";
            }
            echo "</form>";
        } else if (isset($_SESSION["sid"]) && $_SESSION["sid"] === $iteration->sid){
            // for student code will be here
            echo "<h5>For Student:</h5>";
    ?>
    <div class="row">
    <?php if($iteration->review == 0){ 
    ?>
    <div class="col-sm-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Send for review</h5>
        <p class="card-text">Completed/Addressed the comments?</p>
        <form method="post"><button type="submit" class="btn btn-primary" name="review-btn">Ask to Review</form></a>
      </div>
    </div>
  </div>
  <?php
    }
  ?>
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Update Github Repo Link</h5>
        <p class="card-text">
        <form class="row g-2" method="post">
  <div class="col-auto">
    <label for="inputLink" class="visually-hidden">Repo's URL</label>
    <input type="text" class="form-control" name="inputLink" placeholder="Repo's URL">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary mb-3" name="update-link-btn">Update URL</button>
  </div>
</form>
        </p>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Change the Reviewer</h5>
        <p class="card-text">
        <form class="row g-2" method="post">
  <div class="col-auto">
    <label for="inputEmail" class="visually-hidden">New Reviewer's Email</label>
    <input type="email" class="form-control" name="inputEmail" placeholder="New Reviewer's Email">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary mb-3" name="reviewer-change-btn">Change Reviewer</button>
  </div>
</form>
        <?php
        if ($invalid_email){
            echo "<span style='color: red'><b>Invalid Reviewer Email</b></span>";
        }
        ?>
        </p>
      </div>
    </div>
  </div>
</div>
    <?php
        } else if(isset($_SESSION["rid"])){
            echo "<span style='padding-bottom: 50px; margin-left:10px;'>Only reviewer assigned to this iteration can mark it as completed.</span>";
        } else {
            header("Location: index.php");
        }
    }
    ?>
    </div>
</div>
<script>
    const addComment = () => {
        let comment = document.getElementById('comment-input').value;
        $.ajax({
          url: window.location.href,
          method: 'POST',
          data: {
            comment: comment,
          },
          success: function( result ) {
              location.reload();
          }
        });
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>
</html>