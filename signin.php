<?php

require_once "settings.php";

if(@$_SESSION['auth'] == 1){header('Location: fields.php');}

/*
if(isset($_POST['sbm_btn'])){
  if($res = $mysqli_req->query("SELECT * FROM users WHERE `login` = '$_POST[login]' AND `password` = '$_POST[password]'")->fetch_assoc()) {
    $_SESSION['auth'] = 1; header('Location: fields.php'); $_SESSION['rights'] = $res['rights'];
  }

}*/

if(isset($_POST['sbm_btn'])){
  if($_POST['login'] == 'alex' && $_POST['password'] == 'kurenkov') {
    $_SESSION['auth'] = 1; header('Location: fields.php'); $_SESSION['rights'] = $res['rights'];
  }

}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Вход</title>
    <link rel="stylesheet" type="text/css" href="style/signin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <main>
      <div class="signin">
         <form method="POST">
          <p>Вход в управляющую панель</p>
          <input type="text" placeholder="Логин" name="login" autocomplete="off" required><br>
          <input type="password" placeholder="Пароль" name="password" required><br>
          <button type="submit" name="sbm_btn">Войти</button>
        </form>
      </div>
    </main>
  </body>
</html>
