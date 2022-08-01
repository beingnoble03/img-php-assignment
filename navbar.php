<nav class="navbar bg-dark navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="./index.php">Assignment Review System</a>
    <?php
      if (isset($_SESSION['name'])){
    ?>
    <form class="d-flex" role="logout" method = "post">
        <span style = "color: white; margin-right: 10px; margin-top: 3px;"> <?php echo "{$_SESSION['name']}"; ?> </span>
      <button class="btn btn-outline-warning btn-sm" type="submit" name = "logout-btn">Logout</button>
    </form>
    <?php
      } else {
    ?>
    <div class="d-flex">
      <button class="btn btn-outline-warning btn-sm" type="submit" name = "login-btn" onclick="window.location.replace('./login.php')">Login</button>
      <button class="btn btn-outline-warning btn-sm" type="submit" name = "register-btn" style="margin-left: 10px;" onclick="window.location.replace('./register.php')">Register</button>
      </div>
    <?php
      }
    ?>
  </div>
</nav>
<?php
require("logout.php");
?>