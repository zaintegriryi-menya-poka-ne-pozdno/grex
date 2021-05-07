<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

header('Content-Security-Policy: upgrade-insecure-requests');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');

$host_db= 'localhost';
$user_db = 'u0605_dev';
$password_db = '1234567890Qwe';
$db = 'u0605727_mt_qc';
$mysqli_req = new mysqli($host_db, $user_db, $password_db, $db);
$mysqli_req->set_charset("utf8mb4");
if($mysqli_req -> connect_error) die("error");
date_default_timezone_set("Asia/Krasnoyarsk");

if(isset($_POST)){
  $prep_date = date("Y-m-d H:i:s");
  switch(@$_POST['action']){
    case 'get_qc_fields': {
      $res = $mysqli_req->query("SELECT * FROM quality_control");
      $cat = $mysqli_req->query("SELECT * FROM categori");
      $m_m = $mysqli_req->query("SELECT * FROM m_m");
      $item['res'] = $res->fetch_all(MYSQLI_ASSOC);
      $item['cat'] = $cat->fetch_all(MYSQLI_ASSOC);
      $item['m_m'] = $m_m->fetch_all(MYSQLI_ASSOC);
      $nota = 0;
      $repa = 0;
      if($notice = $mysqli_req->query("SELECT `notice` FROM leads WHERE `id_lead` = '$_POST[card_id]'"))
        {
        $nota = 1;
        $notice_array = $notice->fetch_all(MYSQLI_ASSOC); 
        @$notice_array = $notice_array[0]['notice'];
        }
      if($report = $mysqli_req->query("SELECT `report` FROM leads WHERE `id_lead` = '$_POST[card_id]'"))
        {
        $repa = 1;
        $report_array = $report->fetch_all(MYSQLI_ASSOC); 
        @$report_array = $report_array[0]['report'];
        }
      if($nota || $repa) {
          $notice_push = (object)array('notice' => $notice_array != null ? $notice_array : "", 'report' => $report_array != null ? $report_array : "");
          $item['custom'] = $notice_push;
      }
      echo json_encode($item, JSON_UNESCAPED_UNICODE);
      $mysqli_req->close();
     };
     break;
    case 'insert_warning':{
      $checkbox_name = $_POST['data']['checkbox_name'];
      $checkbox_val = $_POST['data']['checkbox_val'];
      $where_insert = "";
      $what_insert = "";
      $else_insert = "`";
      $arr_len = count($checkbox_name);
      for($i = 0; $i < $arr_len; $i++){
        if(($arr_len-1) !== $i){
          $where_insert = $where_insert . $checkbox_name[$i] . "`, `";
          $what_insert = $what_insert . $checkbox_val[$i] . "', '";
          }
          else{
            $where_insert = $where_insert . $checkbox_name[$i];
            $what_insert = $what_insert . $checkbox_val[$i];
            }
      }
      $prep_card_id = $_POST['data']['card_id'];
      $prep_comment = $_POST['data']['comment'];
      $prep_expert_id = $_POST['data']['expert_id'];
      $prep_expert_name = $_POST['data']['expert_name'];
      $prep_mng_id = $_POST['data']['mng_id'];
      $prep_mng_name = $_POST['data']['mng_name'];
      $prep_minus = $_POST['data']['minus'];
      $prep_plus = $_POST['data']['plus'];
      if(!$mysqli_req->query("SELECT `id` FROM leads WHERE id_lead = '$prep_card_id'")->fetch_all(MYSQLI_ASSOC)){
          $res = $mysqli_req->query("INSERT INTO `leads`(`id_lead`, `notice`, `expert_id`, `expert_name`, `mng_id`, `mng_name`, `date`, `minus`, `plus`,`$where_insert`) VALUES ('$prep_card_id', '$prep_comment', '$prep_expert_id', '$prep_expert_name', '$prep_mng_id', '$prep_mng_name', '$prep_date', '$prep_minus', '$prep_plus', '$what_insert')");
          $mysqli_req->close();
      } else{
          $result = 'зашли в элсе';
          for($i = 0; $i < $arr_len; $i++) {
              if (($arr_len - 1) !== $i) {
                  $else_insert = $else_insert . $checkbox_name[$i] . "` = '" . $checkbox_val[$i] . "', `";
              } else {
                  $else_insert = $else_insert . $checkbox_name[$i] . "` = '" . $checkbox_val[$i] . "'";
              }
          }
        //$res = $mysqli_req->query("UPDATE leads SET `notice` = '$prep_comment', `expert_id` = '$prep_expert_id', `expert_name` = '$prep_expert_name', `mng_name` = '$prep_mng_name', `readed_by_mng` = '0', `date` = '$prep_date', `minus` = '$prep_minus', `plus` = '$prep_plus', $else_insert  WHERE `id_lead` = '".$_POST['data']['card_id']."'");
          $res = $mysqli_req->query("INSERT INTO `leads`(`id_lead`, `notice`, `expert_id`, `expert_name`, `mng_id`, `mng_name`, `date`, `minus`, `plus`,`$where_insert`) VALUES ('$prep_card_id', '$prep_comment', '$prep_expert_id', '$prep_expert_name', '$prep_mng_id', '$prep_mng_name', '$prep_date', '$prep_minus', '$prep_plus', '$what_insert')");
          $mysqli_req->close();
       }
    };
      echo $where_insert;
//        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    break;
    
    case 'get_filled_fields':{
      $res = $mysqli_req->query("SELECT * FROM leads WHERE id_lead = '$_POST[card_id]'");
      $item = $res->fetch_all(MYSQLI_ASSOC);
      echo json_encode($item, JSON_UNESCAPED_UNICODE);
      $mysqli_req->close();
    };
    break;
    
    case 'get_filled_fields_for_manager':{
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
      $lead_ids = $mysqli_req->query("SELECT $ids FROM leads WHERE `id_lead` = '$_POST[card_id]'");
      $lead_ids_array = $lead_ids->fetch_all(MYSQLI_ASSOC);       
      $lead_ids_array = $lead_ids_array[0];
      $key_or = [];
      foreach($lead_ids_array as $key => $val){
        if($val == "1") {
          $key_or[] = "`id` = '$key'";
          $key_or[] = "OR";
        }
      }
      array_pop($key_or);
      $key_or = implode(" ", $key_or);
      $notice = $mysqli_req->query("SELECT `notice` FROM leads WHERE `id_lead` = '$_POST[card_id]'");
      $notice_array = $notice->fetch_all(MYSQLI_ASSOC); 
      $notice_array = $notice_array[0]['notice'];
      $report = $mysqli_req->query("SELECT `report` FROM leads WHERE `id_lead` = '$_POST[card_id]'");
      $report_array = $report->fetch_all(MYSQLI_ASSOC); 
      $report_array = $report_array[0]['report'];
      $res = $mysqli_req->query("SELECT * FROM quality_control WHERE $key_or");
      $get_key = $res->fetch_all(MYSQLI_ASSOC);
      $notice_push = (object) array('notice'=>$notice_array, 'report'=>$report_array);
      $get_key['custom'] =  $notice_push;
      echo json_encode($get_key, JSON_UNESCAPED_UNICODE);
      $mysqli_req->close();
    };
    break;
    
    case 'insert_report':{
      $res = $mysqli_req->query("UPDATE leads SET `report` = '$_POST[report]', `readed_by_qc` = '0', `date_mng` = '$prep_date' WHERE `id_lead` = '$_POST[card_id]'");
       $mysqli_req->close();
    };
    break;
    
    case 'get_counter':{
      $_POST['right'] == 'manager' ? @$res = $mysqli_req->query("SELECT `id_lead` FROM leads WHERE `mng_id` = '$_POST[user]' AND `readed_by_mng` = '0'"):@$res = $mysqli_req->query("SELECT `id_lead` FROM leads WHERE `expert_id` = '$_POST[user]' AND `readed_by_qc` = '0'");
      //$get_key = $res->fetch_all(MYSQLI_ASSOC);
      echo json_encode(array('count'=>$res->num_rows, 'leads'=>$res->fetch_all(MYSQLI_ASSOC)), JSON_UNESCAPED_UNICODE);
      $mysqli_req->close();
    };
    break;
    case 'delete warning':{ // Удалить и в CRM и в БД
      $mysqli_req->query("DELETE FROM `leads` WHERE `id_lead` = '".$_POST['data']['card_id']."'");
      $mysqli_req->close();
    };
    break;

    case 'unshow':{
      if($_POST['rights'] == 'manager') {$mysqli_req->query("UPDATE leads SET `readed_by_mng` = '1'  WHERE `id_lead` = '$_POST[notification_id]'");} 
      else {
        $mysqli_req->query("UPDATE leads SET `readed_by_qc` = '1'  WHERE `id_lead` = '$_POST[notification_id]'");
      }
    };
    break;
  }
}
exit();
?>