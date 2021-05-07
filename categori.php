<?php
require_once "settings.php";
$categori = $mysqli_req->query("SELECT * FROM `categori` WHERE 1")->fetch_all(MYSQLI_ASSOC);
if(isset($_POST['button_ok']))
{
    $mysqli_req->query("UPDATE `categori` SET `name_categori` = '".$_POST['sins'.$_POST['button_ok']]."' WHERE `id` = $_POST[button_ok]");
     unset($_POST);$_POST = array();
    $mysqli_req->close();
    header("Refresh: 0");
}elseif(isset($_POST['button_x']))
{    $mysqli_req->query("DELETE FROM `categori` WHERE `id` = $_POST[button_x]");
    unset($_POST);$_POST = array(); $mysqli_req->close(); header("Refresh: 0");}
elseif(isset($_POST['add_field'])){
    $forid = $mysqli_req->query("INSERT INTO `categori`(`name_categori`) VALUES ('$_POST[add_sin]')");
    unset($_POST);$_POST = array(); $mysqli_req->close();header("Refresh: 0");}
?>
<!DOCTYPE html>
<html>
<head>
    <!--    <meta charset="utf-8">-->
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Редактирование полей</title>
    <link rel="stylesheet" type="text/css" href="style/fields.css">
    <link rel="stylesheet" type="text/css" href="style/main.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<header>
    <a href="https://tema24.amocrm.ru" target="_blank"><img class="top_logo" src="https://www.drupal.org/files/project-images/logo_bill.png"></a>
    <div class="menu">
        <li>
            <ul><a class="menu_item" href="fields.php">Редактирование полей</a></ul>
            <ul><a class="menu_item" href="statistics.php">Статистика</a></ul>
            <ul><a class="menu_item active" href="categori.php">Категория</a></ul>
            <ul><a class="menu_item" href="fields.php">Штаб</a></ul>
            <ul><form method="post"><input class="menu_item exit_btn" type="submit" value="Выйти" name="exit"></form></ul>
        </li>
    </div>
</header>
<main>
    <?php
    $rows = '';
    for($i = 0; $i < count($categori);$i++) {
        $rows .= '<tr class="row_for_choose">
                    <td>
                        <input autocomplete="off" name="sins'.$categori[$i]['id'].'" type="text" value="'.$categori[$i]['name_categori'].'">
                    </td>
                    <td>
                        <button type="submit" class="ok_x_buttons" name="button_x" value="'.$categori[$i]['id'].'">х</button>
                        <button type="submit" class="ok_x_buttons" name="button_ok" value="'.$categori[$i]['id'].'">ок</button>
                    </td>
                  </td>';

    }
    echo '<div class="fields_form">
                <form action="" method="post">
                    <table id="modul_table">
                    <tr id="thead">
                        <td>Категории</td>
                     </tr>'.$rows.'
                     <tr class="bottom_row">
                        <td>
                            <input autocomplete="off" type="text" value="" placeholder="Введите название категории" class="add_row" name="add_sin">
                        </td>
                        <td>
                            <button type="submit" name="add_field" value="addit" class="add_button">Добавить
                            </button>
                         </td>
                      </tr>
                    </table>
                  </form>
                  </div>';

    ?>
</main>
<footer>
</footer>
</body>
</html>
