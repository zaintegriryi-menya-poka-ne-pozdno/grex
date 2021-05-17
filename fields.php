<?php
      require_once "settings.php";

      $qc_fields = $mysqli_req->query("SELECT * FROM quality_control");
      $categori = $mysqli_req->query("SELECT * FROM categori")->fetch_all(MYSQLI_ASSOC);
      $m_m = $mysqli_req->query("SELECT * FROM m_m")->fetch_all(MYSQLI_ASSOC);
      $qc_fields_array = $qc_fields->fetch_all(MYSQLI_ASSOC);


//      $categori->set_charset("utf8");
$noFilter = false;
$query = "";
if(isset($_POST['id_categori_forfilter'])){
    if ($_POST['id_categori_forfilter'] != 'vse') {
        $idcat = intval($_POST['id_categori_forfilter']);
        $query = "`id_categori` = $idcat";
        $m_m = $mysqli_req->query("SELECT * FROM `m_m` WHERE $query ORDER BY `id_categori`")->fetch_all(MYSQLI_ASSOC);
        $qc_fields_arrayyy = array();
        for ($i = 0; $i < count($m_m); $i++) {
            for ($j = 0; $j < count($qc_fields_array); $j++) {
                if ($m_m[$i]['id_quality'] == $qc_fields_array[$j]['id']) {
                    $qc_fields_arrayyy[] = $qc_fields_array[$j];
                }
            }
        }
        $qc_fields_array = $qc_fields_arrayyy;
    }

}
if(isset($_POST['offfilter'])){
    $qc_fields = $mysqli_req->query("SELECT * FROM quality_control");
    $categori = $mysqli_req->query("SELECT * FROM categori")->fetch_all(MYSQLI_ASSOC);
    $m_m = $mysqli_req->query("SELECT * FROM m_m")->fetch_all(MYSQLI_ASSOC);
    $qc_fields_array = $qc_fields->fetch_all(MYSQLI_ASSOC);
    $noFilter = true;
}
      if(isset($_POST['button_ok']))
        {   $id_cat = intval($_POST['id_categori']);
            $id_qua = intval($_POST['button_ok']);
//            var_dump($id_cat);
//            var_dump($id_qua);
//            for($i = 0; $i < count($categori);$i++) {
//                var_dump($categori[$i]['id']);
//                for ($j = 0; $j < count($m_m); $j++) {
//                    var_dump("pered if ");
//                    var_dump($m_m[$j]['id_categori']);
//                    var_dump($m_m[$j]['id_quality']);
//                    var_dump($id_qua);
//                    if ($categori[$i]['id'] == $m_m[$j]['id_categori'] && $m_m[$j]['id_quality'] == $id_qua) {
//                        var_dump("zawli v if ");
//                        var_dump($m_m[$j]['id_categori']);
//                        var_dump($m_m[$j]['id_quality']);
//                        var_dump($id_qua);
//                        $id_cat = intval($m_m[$j]['id_categori']);
//                        $id_m_m = intval($m_m[$j]['id']);
//                        break 2;
//                    }
//                }
//            }
//            var_dump($id_cat);
//            var_dump($id_qua);
//            var_dump($id_m_m);
            $mysqli_req->query("UPDATE `m_m` SET `id_categori` = '$id_cat' WHERE `id_quality` = $_POST[button_ok]");
            $mysqli_req->query("UPDATE `quality_control` SET `name` = '".$_POST['sins'.$_POST['button_ok']]."', `minus` = '".$_POST['penalty'.$_POST['button_ok']]."', `plus` = '".$_POST['bonus'.$_POST['button_ok']]."' WHERE `id` = $_POST[button_ok]");            unset($_POST);$_POST = array();
            $mysqli_req->close();
        header("Refresh: 0");}

      elseif(isset($_POST['button_x']))
            {
                $mysqli_req->query("DELETE FROM `m_m` WHERE `id_quality` = $_POST[button_x]");
                $mysqli_req->query("DELETE FROM `quality_control` WHERE `id` = $_POST[button_x]");
                $mysqli_req->query("ALTER TABLE `leads` DROP `$_POST[button_x]`");
              unset($_POST);$_POST = array(); $mysqli_req->close(); header("Refresh: 0");}
      elseif(isset($_POST['add_field'])){
          $forid = $mysqli_req->query("INSERT INTO `quality_control`(`name`, `minus`, `plus`) VALUES('$_POST[add_sin]', '$_POST[add_minus]', '$_POST[add_plus]')");
          $id = $mysqli_req->insert_id;
          $id_cat = intval($_POST['id_categori']);
          $mysqli_req->query("INSERT INTO `m_m`(`id_categori`, `id_quality`) VALUES($id_cat, $id)");
          $mysqli_req->query("ALTER TABLE `leads` ADD `$id` INT(1) NULL DEFAULT NULL");
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
          <ul><a class="menu_item active" href="fields.php">Редактирование полей</a></ul>
          <ul><a class="menu_item" href="statistics.php">Статистика</a></ul>
            <ul><a class="menu_item" href="categori.php">Категория</a></ul>
          <ul><a class="menu_item" href="fields.php">Штаб</a></ul>
          <ul><form method="post"><input class="menu_item exit_btn" type="submit" value="Выйти" name="exit"></form></ul>
        </li>
      </div>
    </header>
    <main>
    <?php
      $rows = '';
      foreach($qc_fields_array as $key => $val){
          $select = '<option value="vse"> </option>';
          for($i = 0; $i < count($categori);$i++) {
              for ($j = 0; $j < count($m_m); $j++) {
                  if ($categori[$i]['id'] == $m_m[$j]['id_categori'] && $m_m[$j]['id_quality'] == $qc_fields_array[$key]['id']) {
                      $select .= '<option selected="selected" value="' . $categori[$i]['id'] . '">' . $categori[$i]['name_categori'] . '</option>';
                      $i = $i + 1;
                      if ($i == count($categori)) {
                          break 2;
                      }
                      break 1;
                  } else {
                      $select .= '';
                  }
              }
              $select .= '<option value="' . $categori[$i]['id'] . '">' . $categori[$i]['name_categori'] . '</option>';
          }

//          <option value="'.$cat_id.'">'.$cat_name.'</option>
        $rows .= '<tr class="row_for_choose">
                    <td>
                        <input autocomplete="off" name="sins'.$qc_fields_array[$key]['id'].'" type="text" value="'.$qc_fields_array[$key]['name'].'">
                    </td>
                    <td class="green">
                        <input autocomplete="off" name="bonus'.$qc_fields_array[$key]['id'].'" type="number" value="'.$qc_fields_array[$key]['plus'].'">
                    </td>
                    <td class="red">
                        <input autocomplete="off" name="penalty'.$qc_fields_array[$key]['id'].'" type="number" value="'.$qc_fields_array[$key]['minus'].'">
                    </td>
                    <td>
                        <select valeu id="id_categori" >'.
                            $select.'
                        </select>
                    </td>
                    <td>
                        <button type="submit" class="ok_x_buttons" name="button_x" value="'.$qc_fields_array[$key]['id'].'">х</button>
                        <button type="submit" class="ok_x_buttons" name="button_ok" value="'.$qc_fields_array[$key]['id'].'">ок</button>
                    </td>
                  </td>';

      }
      echo '<div class="filtr">Подбор по категориям: <form action="" method="post">
        <select valeu name="id_categori_forfilter" id="id_categori_forfilter">'.
          $select.'
                        </select><button>Поиск</button>
                        <button name="offfilter" class="button">Сбросить</button>
      </form></div>
                        <div class="fields_form">
                <form action="" method="post">
                    <table id="modul_table">
                    <tr id="thead">
                        <td>Грех</td><td>Бонус</td><td>Штраф</td><td>Категория</td><td>Действие</td>
                     </tr>'.$rows.'
                     <tr class="bottom_row">
                        <td>
                            <input autocomplete="off" type="text" value="" placeholder="Введите название греха" class="add_row" name="add_sin">
                        </td>
                        <td>
                            <input autocomplete="off" type="number" value="" placeholder="Введите бонус" class="add_row" name="add_plus">
                        </td>
                        <td>
                            <input autocomplete="off" type="number" value="" placeholder="Введите штраф" class="add_row" name="add_minus">
                        </td>
                        <td>
                        <select id="id_categori" name="id_categori" >         
                            '.$select.'
                        </select>        
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
