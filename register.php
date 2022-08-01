<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link href="./styles/register.css" rel = "stylesheet">
  </head>
  <body>
      <?php
      session_start();
      require("navbar.php");
      ?>
  <form class="row g-3 main-form" method="POST" action="register.php">
  <div class="col-auto">
    <label for="inputName" class="visually-hidden">Name</label>
    <input type="text" class="form-control" id="inputName" placeholder="Name" name="name">
  </div>
  <div class="col-auto">
    <label for="inputEmail" class="visually-hidden">Email</label>
    <input type="text" class="form-control" id="inputEmail" placeholder="Email" name="email">
  </div>
  <div class="col-auto">
    <label for="inputPassword" class="visually-hidden">Password</label>
    <input type="password" class="form-control" id="inputPassword" placeholder="Password" name="password">
  </div>
  <br>
  <div class="col-auto">
    <label for="inputBranch" class="visually-hidden">Branch</label>
    <input type="text" class="form-control" id="inputName" placeholder="Branch" name="branch">
  </div>
  <div class="col-auto">
    <label for="inputYear" class="visually-hidden">Year</label>
    <input type="number" class="form-control" id="inputYear" placeholder="Year" name="year">
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary mb-3" name="student-submit-btn">Register Student</button>
    <button type="submit" class="btn btn-primary mb-3" name="reviewer-submit-btn">Register Reviewer</button>
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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["student-submit-btn"])){
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $branch = htmlspecialchars($_POST["branch"]);
    $year = $_POST["year"];
    $query = mysqli_query($connection_response, "SELECT * FROM student where email = '{$email}'");
    if (mysqli_num_rows($query) !== 0){
        echo "This email has already been registered.";
    } else {
        $sql = "INSERT INTO student(name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connection_response, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
        mysqli_stmt_execute($stmt);
        $query = mysqli_query($connection_response, "SELECT * FROM student WHERE email = '{$email}';");
        $row = mysqli_fetch_assoc($query);
        $_SESSION["name"] = $name;
        $_SESSION["sid"] = $row["id"];
        $_SESSION["email"] = $row["email"];
        $sql = "INSERT INTO details VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection_response, $sql);
        mysqli_stmt_bind_param($stmt, "ssis", $email, $branch, $year, $name);
        mysqli_stmt_execute($stmt);
        $php_id = $_COOKIE['PHPSESSID'];
        $query = mysqli_query($connection_response, "INSERT INTO token VALUES ('{$php_id}', '{$email}');");
        setcookie("token", "{$php_id}", time() + (86400 * 30), "/");
        header("Location: index.php");
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reviewer-submit-btn"])){
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $branch = htmlspecialchars($_POST["branch"]);
    $year = $_POST["year"];
    $query = mysqli_query($connection_response, "SELECT * FROM reviewer where email = '{$email}'");
    if (mysqli_num_rows($query) !== 0){
        echo "This email has already been registered.";
    } else {
        $sql = "INSERT INTO reviewer(name, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connection_response, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
        mysqli_stmt_execute($stmt);
        $query = mysqli_query($connection_response, "SELECT * FROM reviewer WHERE email = '{$email}';");
        $row = mysqli_fetch_assoc($query);
        $_SESSION["name"] = $name;
        $_SESSION["rid"] = $row["id"];
        $_SESSION["email"] = $row["email"];
        $sql = "INSERT INTO details VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection_response, $sql);
        mysqli_stmt_bind_param($stmt, "ssis", $email, $branch, $year, $name);
        mysqli_stmt_execute($stmt);
        $php_id = $_COOKIE['PHPSESSID'];
        $query = mysqli_query($connection_response, "INSERT INTO token VALUES ('{$php_id}', '{$email}');");
        setcookie("token", "{$php_id}", time() + (86400 * 30), "/");
        header("Location: index.php");
        exit();
    }
}
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  </body>
</html>