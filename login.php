<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="./styles/login.css" rel = "stylesheet">
  </head>
  <body>
    <?php
        session_start();
        require("navbar.php");
    ?>
  <form class="row g-3 main-form" method="POST">
  <div class="col-auto">
    <label for="inputEmail" class="visually-hidden">Email</label>
    <input type="text" class="form-control" id="inputEmail" placeholder="Email" name="email">
  </div>
  <div class="col-auto">
    <label for="inputPassword" class="visually-hidden">Password</label>
    <input type="password" class="form-control" id="inputPassword" placeholder="Password" name="password">
  </div>
  <div class="col-auto">
    <button type="submit" name = "student-submit-btn" class="btn btn-primary mb-3">LogIn as Student</button>
    <button type="submit" name = "reviewer-submit-btn" class="btn btn-primary mb-3">LogIn as Reviewer</button>
</div>
</form>
<?php
require_once("connect.php");
if (isset($_SESSION["email"])){
    header("Location: index.php");
}
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
      header("Location: index.php");
    } else {
      $query = mysqli_query($connection_response, "SELECT * FROM reviewer WHERE email = '{$email}'");
      if (mysqli_num_rows($query) === 1){
        $row = mysqli_fetch_assoc($query);
        $_SESSION["name"] = $row["name"];
        $_SESSION["rid"] = $row["id"];
        $_SESSION["email"] = $row["email"];
        header("Location: index.php");
      }
    }
  }
}
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["student-submit-btn"])){
    $email = $_POST["email"];
    $password = $_POST["password"];
    $query = mysqli_query($connection_response, "SELECT * FROM student WHERE email = '{$email}'");
    if(mysqli_num_rows($query) === 1){
        $row = mysqli_fetch_assoc($query);
        if(password_verify($password, $row['password'])){
          $_SESSION["name"] = $row["name"];
          $_SESSION["sid"] = $row["id"];
          $_SESSION["email"] = $row["email"];
          $php_id = $_COOKIE['PHPSESSID'];
          $query = mysqli_query($connection_response, "INSERT INTO token VALUES ('{$php_id}', '{$email}');");
          setcookie("token", "{$php_id}", time() + (86400 * 30), "/");
          header("Location: index.php");
        } else {
          header("Refresh:0");
        }
        exit();
    }
}
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reviewer-submit-btn"])){
    $email = $_POST["email"];
    $password = $_POST["password"];
    $query = mysqli_query($connection_response, "SELECT * FROM reviewer WHERE email = '{$email}'");
    if(mysqli_num_rows($query) === 1){
      $row = mysqli_fetch_assoc($query);
      if(password_verify($password, $row['password'])){
        $_SESSION["name"] = $row["name"];
        $_SESSION["rid"] = $row["id"];
        $_SESSION["email"] = $row["email"];
        $php_id = $_COOKIE['PHPSESSID'];
        $query = mysqli_query($connection_response, "INSERT INTO token VALUES ('{$php_id}', '{$email}');");
        setcookie("token", "{$php_id}", time() + (86400 * 30), "/");
        header("Location: index.php");
      } else {
        header("Refresh:0;");
      }
        exit();
    }
}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  </body>
</html>