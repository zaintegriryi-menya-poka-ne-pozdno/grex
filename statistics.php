<?php 
  require_once "settings.php";
$leads_array = $mysqli_req->query("SELECT `id`, `id_lead`, `expert_id`, `mng_id`,`mng_name`,`expert_name`, `notice`, `report`, `minus`, `plus`, `date`, `date_mng` FROM `leads` ORDER BY `date`, `date_mng` DESC")->fetch_all(MYSQLI_ASSOC);
 //ПОЛУЧЕНИЕ ЗАМЕЧАНИЙ
$all_ids = $mysqli_req->query("SELECT `id` FROM quality_control");
$categori = $mysqli_req->query("SELECT * FROM categori")->fetch_all(MYSQLI_ASSOC);
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
//ПОЛУЧИЛИ ЗАМЕЧА
$noFilter = false;
if(isset($_GET['dateInterval1']) || isset($_GET['dateInterval2']) || isset($_GET['managerName']) || isset($_GET['id_categori_forfilter'])){
    $query = "";
    $queryy = "";
    if($_GET['dateInterval1'] && $_GET['dateInterval2']){
        $query .= "(`date` >= '$_GET[dateInterval1]' AND `date` <= '$_GET[dateInterval2]')";
    }elseif($_GET['dateInterval1']){
        $query .= "(`date` >= '$_GET[dateInterval1]') ";
    }elseif($_GET['dateInterval2']){
        $query .= "(`date` <= '$_GET[dateInterval2]') ";
    }
    if($_GET['id_categori_forfilter']){
        if ($_GET['id_categori_forfilter'] !== 'vse') {
            $schet = 0;
            $idcat = intval($_GET['id_categori_forfilter']);
            $queryy = "`id_categori` = $idcat";
            $m_m = $mysqli_req->query("SELECT * FROM `m_m` WHERE $queryy ORDER BY `id_categori`")->fetch_all(MYSQLI_ASSOC);
            $qc_fields_arrayyy = array();
            $qc_id = array();
            $leads_array = array_reverse($leads_array);
            $idlead = array();
            for ($i = 0; $i < count($m_m); $i++) {
                foreach ($leads_array as $key => $val) {
                    $lead_ids = $mysqli_req->query("SELECT $ids FROM leads WHERE `id_lead` = '" . $leads_array[$key]['id_lead'] . "'");
                    $lead_ids_array = $lead_ids->fetch_all(MYSQLI_ASSOC);
//                  var_dump($leads_array);
                    $lead_ids_array = $lead_ids_array[0];
                    $key_or = [];
                    $mistakes = "";
                    $checker = 0;
                    foreach ($lead_ids_array as $key1 => $val1) {
                        if ($key1 == intval($m_m[$i]['id_quality'])) {
                            if($val1 == "1") {
                                $idlead = intval($leads_array[$key]['id_lead']);
                                if ($schet>=1){
                                    $query .= " OR `id_lead` = $idlead";
                                }
                                else{
                                    $query .= "`id_lead` = $idlead";
                                }
                                $schet = 1;
                            }
                        }
                    }
                }
            }
        }
    }
    if($_GET['managerName'] && ($_GET['dateInterval1'] || $_GET['dateInterval2'])){
        $query .= "AND (`mng_name` = '$_GET[managerName]') ";
    }elseif($_GET['managerName']){
        $query .= "(`mng_name` LIKE '%$_GET[managerName]%') ";
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
            <ul><a class="menu_item" href="categori.php">Категория</a></ul>
          <ul><a class="menu_item" href="fields.php">Штаб</a></ul>
          <ul><form method="post"><input class="menu_item exit_btn" type="submit" value="Выйти" name="exit"></form></ul>
        </li>
      </div>
    </header>
    <main>
        <?php
        $select = '<option value="vse">....</option>';
            for($i = 0; $i < count($categori);$i++) {
                $select .= '<option value="' . $categori[$i]['id'] . '">' . $categori[$i]['name_categori'] . '</option>';
            }
        echo '<form>
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
                <select valeu name="id_categori_forfilter" id="id_categori_forfilter">'.
                    $select.'
                </select>
                <input class="button" type="submit" name="filter_btn" value="Найти">
                <input type="button" onclick="javascript:location.href=window.location.pathname + \'?access=I8ju2wJkbSAsgQ9iHB9P\'" class="button" value="Сбросить">

            </div>
        </form>';
            ?>


      <p class="table_title" onclick="hideblock('exp_list')">Список экспертов</p>
      <div  id="exp_list" style="display: none;">
        <table class="expert_table">
          <tr class="row_title">
            <td>Эксперт</td>
<!--            <td>Проверенных сделок</td>-->
<!--            <td>Заработано</td>-->
            <td>Проверенных сделок/месяц</td>
            <td>Заработано/месяц</td>
          </tr>
            <?php
              $countt = 0;
              $allmen = array();
              $leads_array3 = array_reverse($leads_array);
              $leads_array4 = array_reverse($leads_array);
            $lastkolmen = 0;
            $lastkolsumm = 0;
            $tt[0] = $leads_array3[0];
            $kolsumm = $leads_array3[0]['plus'];
            $keys=array(); // Массив ключей, которые уже встречались
            foreach($leads_array3 as $k=>$val) {
                if(array_key_exists($val['expert_id'],$keys)) {
                    unset($leads_array3[$k]);
                } else {
                    $keys[$val['expert_id']]=1;
                }
            }
            for ($i = 0; $i < count($leads_array4); $i++) {
                foreach ($leads_array3 as $k => $val) {
                    if ($leads_array4[$i]['expert_id'] == $val['expert_id'] && $i > $k) {
                        $leads_array3[$k]['plus'] += intval($leads_array4[$i]['plus']);
                    }
                }
            }
            $col = array_count_values(array_column($leads_array4,'expert_id'));
            foreach ($leads_array3 as $k => $val) {
                foreach ($col as $kcol => $valcol) {
                        if ($leads_array3[$k]['expert_name'] !== '' && $leads_array3[$k]['expert_name'] !== null && $kcol == $leads_array3[$k]['expert_id']) {
                            echo "<tr>
                    <td>" . $leads_array3[$k]['expert_name'] . "</td>
                    <td class='green'>" . $valcol . "</td>
                    <td class='green'>" . $leads_array3[$k]['plus'] . "</td>
                    </tr>";
                        }
                    }
            }
            ?>
        </table>
      </div>
      </div>
      <div class="right_side">
      <p class="table_title" onclick="hideblock('mng_list')">Список менеджеров</p>
        <div  id="mng_list" style="display: none;">
          <table class="manager_table">
            <tr class="row_title">
              <td>Менеджер </td>
              <td>Неправильных сделок/месяц</td>
              <td>Вычтено/месяц</td>
            </tr>
              <?php
              for($i = 0; $i < count($categori);$i++) {
                  $select .= '<option value="' . $categori[$i]['id'] . '">' . $categori[$i]['name_categori'] . '</option>';
              }
              $countt = 0;
              $allmen = array();
              $leads_array1 = array_reverse($leads_array);
              $leads_array2 = array_reverse($leads_array);
              $keys=array(); // Массив ключей, которые уже встречались
              foreach($leads_array1 as $k=>$val) {
                  if(array_key_exists($val['mng_id'],$keys)) {
                      unset($leads_array1[$k]);
                  } else {
                      $keys[$val['mng_id']]=1;
                  }
              }
              for ($i = 0; $i < count($leads_array2); $i++) {
                  foreach ($leads_array1 as $k => $val) {
                      if ($leads_array2[$i]['mng_id'] == $val['mng_id'] && $i > $k) {
                          $leads_array1[$k]['minus'] += intval($leads_array2[$i]['minus']);
                      }
                  }
              }
              $col = array_count_values(array_column($leads_array2,'mng_id'));
              foreach ($leads_array1 as $k => $val) {
                  foreach ($col as $kcol => $valcol) {
                      if ($leads_array1[$k]['mng_name'] != '' && $leads_array1[$k]['mng_name'] != null && $kcol == $leads_array1[$k]['mng_id']) {
                          echo "<tr>
                    <td>" . $leads_array1[$k]['mng_name'] . "</td>
                    <td class='red'>$valcol</td>
                    <td class='red'>" . $leads_array1[$k]['minus'] . "</td>
                    </tr>";
                      }
                  }
              }

//              for ($i = 0;$i<count($leads_array1);$i++) {
//                  $kolmen = 1;
//                  $kolsumm = $leads_array1[$i]['minus'];
//                  if ($i+1 == count($leads_array1))
//                      $schet = $i;
//                  else
//                      $schet = $i+1;
//                  if (isset($leads_array1[$schet])) {
//                      for ($j = $schet; $j < count($leads_array1); $j++) {
//                          if ($leads_array1[$i]['mng_id'] == $leads_array2[$j]['mng_id'] && $i < $j) {
//                              $kolmen += 1;
//                              $kolsumm += $leads_array2[$j]['minus'];
//                              unset($leads_array2[$j]);
////                          unset($leads_array2[$i]);
//                          } else {
//                              continue;
//                          }
//                      }
//                  }
//                  if (isset($leads_array2[$i])) {
//                      if ($leads_array2[$i]['mng_name'] != '' && $leads_array2[$i]['mng_name'] != null) {
//                          echo "<tr>
//                    <td>" . $leads_array2[$i]['mng_name'] . "</td>
//                    <td class='red'>$kolmen</td>
//                    <td class='red'>$kolsumm</td>
//                    </tr>";
//                      }
//                  }
//
//              }

//
              ?>
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
            for($i = 0; $i < count($categori);$i++) {
                $select .= '<option value="' . $categori[$i]['id'] . '">' . $categori[$i]['name_categori'] . '</option>';
            }
            $countt = 0;
            $tt = 0;
            $leads_arrayz = array_reverse($leads_array);
            foreach ($leads_arrayz as $key => $val) {
                $lead_ids = $mysqli_req->query("SELECT $ids FROM leads WHERE `id_lead` = '".$leads_arrayz[$key]['id_lead']."' AND `mng_id` = '".$leads_arrayz[$key]['mng_id']."' AND `minus` = '".$leads_arrayz[$key]['minus']."'");
                $lead_ids_array = $lead_ids->fetch_all(MYSQLI_ASSOC);
                $lead_ids_array = array_reverse($lead_ids_array);
//                for ($i = 0;$i<count($leads_arrayz);$i++){
//                    if ($leads_arrayz[$key]['id_lead'] == $leads_arrayz[$i]['id_lead'] && $leads_arrayz[$key]['mng_id'] == $leads_arrayz[$i]['mng_id'] && $key < $i){
//
//                        var_dump('<br>');
//                        var_dump('это тт'.$tt);
//                        $tt +=1;
//                        var_dump('<br>');
//                    }
//                }
//                if (count($lead_ids_array)>=2){
//                    $lead_ids_array = $lead_ids_array[$countt];
//                    $countt += 1;
//                }else{
//                    $lead_ids_array = $lead_ids_array[0];
//                }
                $lead_ids_array = end($lead_ids_array);//$lead_ids_array[0];
//               for ($i = $key+1;$i < count($leads_arrayz);$i++){
//                    var_dump($i);
//                    var_dump($key);
//                    var_dump($leads_arrayz[$i]['id_lead']);
//                    var_dump($leads_arrayz[$key]['id_lead']);
//                    var_dump($leads_arrayz[$i]['mng_id']);
//                    var_dump($leads_arrayz[$key]['mng_id']);
//                    if ($leads_arrayz[$i]['id_lead'] == $leads_arrayz[$key]['id_lead'] && $leads_arrayz[$i]['mng_id'] == $leads_arrayz[$key]['mng_id'] && $i !== $key){
//                        $lead_ids_array = $lead_ids_array[$countt];
//                        break;
//                    }
//                }
                $key_or = [];
                $mistakes = "";
                $checker = 0;
                foreach($lead_ids_array as $key1 => $val1){
                    if($val1 == "1") {
                        $key_or[] = "`id` = '$key1'";
                        $key_or[] = "OR";
                        $checker = 1;
                    }
                }
                  if($checker) {
                      array_pop($key_or);
                      $key_or = implode(" ", $key_or);
                      $res = $mysqli_req->query("SELECT `name` FROM quality_control WHERE $key_or");
                      $get_key = $res->fetch_all(MYSQLI_ASSOC);
                      foreach ($get_key as $key2 => $value2) {
                          $mistakes .= '- ' . $get_key[$key2]['name'] . '<br>';
                      }
                  }
                   $lead_time = new DateTime($leads_arrayz[$key]['date']);
                   $date = $lead_time->format("Y-m-d");
                   $time = $lead_time->format("H:i:s");

                   if($leads_arrayz[$key]['date_mng'] !== '0000-00-00 00:00:00'){
                    $lead_time_mng = new DateTime($leads_array[$key]['date_mng']);

                    $date_mng = $lead_time_mng->format("Y-m-d");
                    $time_mng = $lead_time_mng->format("H:i:s");
                       $date_mng = $leads_arrayz[$key]['date_mng'];
                   }
                   else{
                     $date_mng = '-'; $time_mng = '-';
                   }


                    echo "<tr>
                    <td class='time_cell'><div class='datetime'>$date</div> <div class='vertical datetime'></div> <div class='datetime'>$time</div></td>
                    <td class='time_cell'><div class='datetime'>$date_mng</div> <div class='vertical datetime'></div> <div class='datetime'></div></td>
                    <td><a href='https://tema24.amocrm.ru/leads/detail/".$leads_arrayz[$key]['id_lead']."' class='lead_href' target='_blank'>".$leads_arrayz[$key]['id_lead']."</td>
                    <td>".$leads_arrayz[$key]['expert_name']."</td>
                    <td>".$leads_arrayz[$key]['mng_name']."</td>
                    <td>".$leads_arrayz[$key]['notice']."</td>
                    <td>".$leads_arrayz[$key]['report']."</td>
                    <td class='green'>".$leads_arrayz[$key]['plus']."</td>
                    <td class='red'>".$leads_arrayz[$key]['minus']."</td>
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
