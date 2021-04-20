<?php 
  require_once "settings.php";
$leads_array = $mysqli_req->query("SELECT `id`, `id_lead`, `expert_id`, `mng_id`,`mng_name`,`expert_name`, `notice`, `report`, `minus`, `plus`, `date`, `date_mng` FROM `leads` ORDER BY `date`, `date_mng` DESC")->fetch_all(MYSQLI_ASSOC);
 //ПОЛУЧЕНИЕ ЗАМЕЧАНИЙ
$all_ids = $mysqli_req->query("SELECT `id` FROM quality_control");
$item = $all_ids->fetch_all(MYSQLI_ASSOC);
$ids = '`';
$itemcount = count($item);
 for($i = 0; $i < $itemcount; $i++){
    if(($itemcount-1) !== $i){
      $ids = $ids . $item[$i]['id'] . "`, `";
      }
      else{
        $ids = $ids . $item[$i]['id'] . '`';
        }
 }
//ПОЛУЧИЛИ ЗАМЕЧАНИЯ
$noFilter = false;
if(isset($_GET['dateInterval1']) || isset($_GET['dateInterval2']) || isset($_GET['managerName'])){
    $query = "";
    if($_GET['dateInterval1'] && $_GET['dateInterval2']){
        $query .= "(`date` >= '$_GET[dateInterval1]' AND `date` <= '$_GET[dateInterval2]')";
    }elseif($_GET['dateInterval1']){
        $query .= "(`date` >= '$_GET[dateInterval1]')";
    }elseif($_GET['dateInterval2']){
        $query .= "(`date` <= '$_GET[dateInterval2]')";
    }
    if($_GET['managerName'] && ($_GET['dateInterval1'] || $_GET['dateInterval2'])){
        $query .= "AND (`mng_name` = '$_GET[managerName]')";
    }elseif($_GET['managerName']){
        $query .= "(`mng_name` LIKE '%$_GET[managerName]%')";
    }
    $leads_array = $mysqli_req->query("SELECT * FROM `leads` WHERE $query ORDER BY `date`, `date_mng` DESC")->fetch_all(MYSQLI_ASSOC);
//    $result = $mysqli->query("SELECT * FROM `leads` WHERE $query  ORDER BY `date_create` DESC");
}else {
    $leads_array = $mysqli_req->query("SELECT `id`, `id_lead`, `expert_id`, `mng_id`,`mng_name`,`expert_name`, `notice`, `report`, `minus`, `plus`, `date`, `date_mng` FROM `leads` ORDER BY `date`, `date_mng` DESC")->fetch_all(MYSQLI_ASSOC);
    $noFilter = true;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Просмотр статистики</title>
    <link rel="stylesheet" type="text/css" href="style/fields.css">
    <link rel="stylesheet" type="text/css" href="style/statistics.css">
    <link rel="stylesheet" type="text/css" href="style/main.css">
<!--      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">-->
<!--      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <script>
          function hideblock(id){
              if(document.getElementById(id).style.display == "block" || document.getElementById(id).style.display == " ")
                  document.getElementById(id).style.display = "none";
              else
                  document.getElementById(id).style.display = "block";
          }

          document.addEventListener("click", (event)=>{
              if(event.target.classList.contains('table_title')) {
                  if(event.target.classList.contains('active_tab')) event.target.classList.remove('active_tab');
                  else event.target.classList.add('active_tab');
              }
          });
      </script>
  </head>
  <body>
    <header>
      <a href="https://tema24.amocrm.ru" target="_blank"><img class="top_logo" src="https://www.drupal.org/files/project-images/logo_bill.png"></a>
      <div class="menu">
        <li>
          <ul><a class="menu_item" href="fields.php">Редактирование полей</a></ul>
          <ul><a class="menu_item active" href="statistics.php">Статистика</a></ul>
          <ul><a class="menu_item" href="fields.php">Штаб</a></ul>
          <ul><form method="post"><input class="menu_item exit_btn" type="submit" value="Выйти" name="exit"></form></ul>
        </li>
      </div>
    </header>
    <main>
        <form>
            <div class="filterInputs">
                <label for="dateInterval1">Дата от:</label>
                <input class="inputFilters" id="dateInterval1" type="date" name="dateInterval1">
                <label for="dateInterval2">Дата до:</label>
                <input class="inputFilters" id="dateInterval2" type="date" name="dateInterval2">
                <label for="managerName">Внутренний № менеджера:</label>
                <input class="inputFilters" id="managerName" type="text" name="managerName" autocomplete = "off">
                <!--            <select name="mng">-->
                <!--                    <option value="403">403</option>-->
                <!--                    <option value="409">409</option>-->
                <!--                    <option value="413">413</option>-->
                <!--                    <option value="454">454</option>-->
                <!--                    <option value="460">460</option>-->
                <!--                    <option value="405">405</option>-->
                <!--                    <option value="424">424</option>-->
                <!--            </select>-->
                <input class="button" type="submit" name="filter_btn" value="Найти">
                <input type="button" onclick="javascript:location.href=window.location.pathname + '?access=I8ju2wJkbSAsgQ9iHB9P'" class="button" value="Сбросить">
            </div>
        </form>
      <div class="left_side">
      <p class="table_title" onclick="hideblock('exp_list')">Список экспертов</p>
      <div  id="exp_list" style="display: none;">
        <table class="expert_table">
          <tr class="row_title">
            <td>Эксперт</td>
            <td>Проверенных сделок</td>
            <td>Заработано</td>
            <td>Проверенных сделок/месяц</td>
            <td>Заработано/месяц</td>
          </tr>
        </table>
      </div>
      </div>

      <div class="right_side">
      <p class="table_title" onclick="hideblock('mng_list')">Список менеджеров</p>
        <div  id="mng_list" style="display: none;">
          <table class="manager_table">
            <tr class="row_title">
              <td>Менеджер </td>
              <td>Неправильных сделок</td>
              <td>Вычтено</td>
              <td>Неправильных сделок/месяц</td>
              <td>Вычтено/месяц</td>
            </tr>
          </table>
        </div>
      </div>

      <div class="down_side">
        <p class="table_title" onclick="hideblock('leads_list')">Список замечаний</p>
        <div  id="leads_list" style="display: none;">
          <table class="leads_table">
            <tr class="row_title">
              <td>Дата </br> ответа КК</td>
              <td>Дата </br> ответа менеджера</td>
              <td>Сделка</td>
              <td>Эксперт</td>
              <td>Менеджер</td>
              <td>Замечание</td>
              <td>Протест</td>
              <td>Заработано</td>
              <td>Вычтено</td>
              <td>Замечания</td>
            </tr>
            <?php
            $leads_array = array_reverse($leads_array);
            var_dump($leads_array);
                foreach ($leads_array as $key => $val) {
//                    var_dump($leads_array[$key]['id_lead']);
//                    var_dump("ids\n");
//                    var_dump($ids);
////                    SELECT "`88`, `92`, `93`" FROM `leads` WHERE `id_lead` = 23766161
////                    $lead_ids = $mysqli_req->query("SELECT '".$ids."' FROM `leads` WHERE `id_lead` = '".$leads_array[$key]['id_lead']."'")->fetch_all(MYSQLI_ASSOC);
//                    var_dump('</p>');
//                    var_dump($lead_ids);
////                    $lead_ids_array = $lead_ids->fetchAll(PDO::FETCH_COLUMN);
//                    $lead_ids_array = $lead_ids;
//                    var_dump('</p>'.count($lead_ids_array));
                    $lead_ids = $mysqli_req->query("SELECT '".$ids."' FROM `leads` WHERE `id_lead` = '".$leads_array[$key]['id_lead']."'");
//                    $lead_ids_array = $lead_ids_array[0];
//                    for ($i = 0; $i<count($lead_ids_array);$i++){
//                        var_dump($lead_ids_array[$i]);
//                        if ($lead_ids_array[$i] != '' ||  $lead_ids_array[$i] != 'null' || $lead_ids_array[$i] != null) {
//                            $lead_ids_array = $lead_ids_array[0];
//                            var_dump('</p>зашли в иф');
//                            var_dump($lead_ids_array);
//                        }
//                    };
                    $lead_ids_array = $lead_ids->fetch_all(MYSQLI_ASSOC);
                    $lead_ids_array = $lead_ids_array[0];
                    $key_or = [];
                  $mistakes = " ";
                  $checker = 1;
                  foreach($lead_ids_array as $key1 => $val1){
                      var_dump('</p>зашли в фор');
                      var_dump($key1);
                      var_dump('</p>зашли в $val1');
                      var_dump($val1);
                    if($val1 == "1") {
                        var_dump('</p>зашли в иф');
                      $key_or[] = "`id` = '$key1'";
                      $key_or[] = "OR";
                      $checker = 1;
                    }
                  }
                  if($checker) {
                      array_pop($key_or);
                      $key_or = implode(" ", $key_or);
                      var_dump('</p>$checker');
                      var_dump($key_or);
                      $res = $mysqli_req->query("SELECT `name` FROM quality_control WHERE '".$key_or."'");
                      $get_key = $res->fetch_all(MYSQLI_ASSOC);
                      var_dump('</p>$get_key');
                      var_dump($get_key);
                      foreach ($get_key as $key2 => $value2) {
                          $mistakes .= '- ' . $get_key[$key2]['name'] . '<br>';
                      }
                  }
                   $lead_time = new DateTime($leads_array[$key]['date']);
                   $date = $lead_time->format("Y-m-d");
                   $time = $lead_time->format("H:i:s");

                   if($leads_array[$key]['date_mng'] !== '0000-00-00 00:00:00'){
                    $lead_time_mng = new DateTime($leads_array[$key]['date_mng']);
                    $date_mng = $lead_time_mng->format("Y-m-d");
                    $time_mng = $lead_time_mng->format("H:i:s");
                   }
                   else{
                     $date_mng = '-'; $time_mng = '-';
                   }


                    echo "<tr>
                    <td class='time_cell'><div class='datetime'>$date</div> <div class='vertical datetime'></div> <div class='datetime'>$time</div></td>
                    <td class='time_cell'><div class='datetime'>$date_mng</div> <div class='vertical datetime'></div> <div class='datetime'>$time_mng</div></td>
                    <td><a href='https://tema24.amocrm.ru/leads/detail/".$leads_array[$key]['id_lead']."' class='lead_href' target='_blank'>".$leads_array[$key]['id_lead']."</td>
                    <td>".$leads_array[$key]['expert_name']."</td>
                    <td>".$leads_array[$key]['mng_name']."</td>
                    <td>".$leads_array[$key]['notice']."</td>
                    <td>".$leads_array[$key]['report']."</td>
                    <td class='green'>".$leads_array[$key]['plus']."</td>
                    <td class='red'>".$leads_array[$key]['minus']."</td>
                    <td>".$mistakes."</td>
                    </tr>";

                }
              ?>
          </table>
        </div>
      </div>
    </main>
    <footer>
    </footer>


  </body>
</html>
