<?php
if(!(@$_GET['access'] === 'I8ju2wJkbSAsgQ9iHB9P')) exit('Доступ запрещен!');
setcookie("admin", "", time() - 3600); 
//setcookie('admin', 'kabosubeay');
$host_db= 'localhost';
$user_db = 'u0605_morja';
$password_db = '123QWE';
$db = 'u0605727_morja';
$mysqli = new mysqli($host_db, $user_db, $password_db, $db);
if($mysqli->connect_errno){
  file_put_contents('db_errors.txt', "(".$mysqli->connect_errno.") ".$mysqli->connect_error);
  echo "(".$mysqli->connect_errno.") ".$mysqli->connect_error;
}
$mysqli->query("SET NAMES 'utf8'");

$monthStart = date('Y-m-01');
$monthEnd = date("Y-m-t");
$day = date('w');
$week_start = date('Y-m-d', strtotime('-'.($day-1).' days'));
$week_end = date('Y-m-d', strtotime('+'.(7-$day).' days'));

$noFilter = false;
if($_GET['dateInterval1'] || $_GET['dateInterval2'] || $_GET['search'] || $_GET['managerName']){
	$query = "";
	if($_GET['dateInterval1'] && $_GET['dateInterval2']){
		$query .= "(`date_create` >= '$_GET[dateInterval1]' AND `date_create` <= '$_GET[dateInterval2]')";
	}elseif($_GET['dateInterval1']){
		$query .= "(`date_create` >= '$_GET[dateInterval1]')";
	}elseif($_GET['dateInterval2']){
		$query .= "(`date_create` <= '$_GET[dateInterval2]')";
	}
	if($_GET['managerName'] && ($_GET['dateInterval1'] || $_GET['dateInterval2'])){
		$query .= "AND (`manager` = '$_GET[managerName]')"; 
	}elseif($_GET['managerName']){
		$query .= "(`manager` LIKE '%$_GET[managerName]%')"; 
	}
	if($_GET['search'] && (($_GET['dateInterval1'] || $_GET['dateInterval2']) || $_GET['managerName'])){
		$query .= "AND (`id_lead` LIKE '%$_GET[search]%' OR `source` LIKE '%$_GET[search]%' OR `client_tel` LIKE '%$_GET[search]%' OR `oneC_number` LIKE '%$_GET[search]%' OR `oneC_title` LIKE '%$_GET[search]%' OR `provider_number` LIKE '%$_GET[search]%' OR `provider_title` LIKE '%$_GET[search]%' OR `price` LIKE '%$_GET[search]%' OR `cost_price` LIKE '%$_GET[search]%' OR `pay_form` LIKE '%$_GET[search]%' OR `morja` LIKE '%$_GET[search]%')";
	}elseif($_GET['search']){
		$query .= "(`id_lead` LIKE '%$_GET[search]%' OR `source` LIKE '%$_GET[search]%' OR `client_tel` LIKE '%$_GET[search]%' OR `oneC_number` LIKE '%$_GET[search]%' OR `oneC_title` LIKE '%$_GET[search]%' OR `provider_number` LIKE '%$_GET[search]%' OR `provider_title` LIKE '%$_GET[search]%' OR `price` LIKE '%$_GET[search]%' OR `cost_price` LIKE '%$_GET[search]%' OR `pay_form` LIKE '%$_GET[search]%' OR `morja` LIKE '%$_GET[search]%')";
	}
	$result = $mysqli->query("SELECT * FROM `leads` WHERE $query  ORDER BY `date_create` DESC");

	//$result = $mysqli->query("SELECT * FROM `leads` WHERE (`date_create` >= '$_GET[dateInterval1]' AND `date_create` <= '$_GET[dateInterval2]') AND (`manager` = '$_GET[managerName]') AND (`id_lead` LIKE '$_GET[search]' OR `date_shipment` LIKE '$_GET[search]' OR `source` LIKE '$_GET[search]' OR `client_tel` LIKE '$_GET[search]' OR `oneC_number` LIKE '$_GET[search]' OR `oneC_title` LIKE '$_GET[search]' OR `provider_number` LIKE '$_GET[search]' OR `provider_title` LIKE '$_GET[search]' OR `price` LIKE '$_GET[search]' OR `cost_price` LIKE '$_GET[search]' OR `pay_form` LIKE '$_GET[search]' OR `morja` LIKE '$_GET[search]')");

}else {
	$result = $mysqli->query("SELECT * FROM `leads` WHERE `date_create` >= '$monthStart' AND `date_create` <= '$monthEnd' ORDER BY `date_create` DESC");
	$noFilter = true;
}


if($mysqli->errno){
  die("<p style='text-align: center; color: red; font-size: 20px;'>Ошибка обращения к базе данных! Обратитесь к Серёже за помощью: 89659104734 или dev@2810101.ru <br>Ошибка: $mysqli->errno: $mysqli->error</p>");
}

$queryArray = [];
$leadsForMonth = "";
$morjaArray = array();
$priceArray = array();
$dayMorjaArray = array();
$dayPriceArray = array();
$weekMorjaArray = array();
$weekPriceArray = array();
$monthMorjaArray = array();
$monthPriceArray = array();

if($_COOKIE['admin'] == 'kabosubeay') {
	$leadsForMonth = "
		<tr style='background: burlywood;'>
		<td colspan='2'><input type='button' value='Сохранить' id='new_record'></td>
		<td><input type='text' id='id_lead'></td>
		<td><input type='date' id='date_create'></td>
		<td><input type='date' id='date_shipment'></td>
		<td><input type='text' id='source'></td>
		<td><input type='text' id='client_tel'></td>
		<td><input type='text' id='oneC_number'></td>
		<td><input type='text' id='oneC_title'></td>
		<td><input type='text' id='provider_title'></td>
		<td><input type='text' id='price'></td>
		<td><input type='text' id='cost_price'></td>
		<td><input type='text' id='morja'></td>
		<td><input type='text' id='pay_form'></td>
		<td><input type='text' id='manager'></td>
		</tr>
	";
}

while($resultElement = $result->fetch_assoc()){
	if(!$noFilter){
		if(array_key_exists($resultElement['manager'], $morjaArray)){
			$morjaArray[$resultElement['manager']] += $resultElement['morja']?$resultElement['morja']:0;
		}else{
			$morjaArray[$resultElement['manager']] = $resultElement['morja']?$resultElement['morja']:0;
		}
        if(array_key_exists($resultElement['manager'], $priceArray)){
            $priceArray[$resultElement['manager']] += $resultElement['price']?$resultElement['price']:0;
        }else{
            $priceArray[$resultElement['manager']] = $resultElement['price']?$resultElement['price']:0;
        }
	}else{
		if(array_key_exists($resultElement['manager'], $monthMorjaArray)){
			$monthMorjaArray[$resultElement['manager']] += $resultElement['morja']?$resultElement['morja']:0;
		}else{
			$monthMorjaArray[$resultElement['manager']] = $resultElement['morja']?$resultElement['morja']:0;
		}
        if(array_key_exists($resultElement['manager'], $monthPriceArray)){
            $monthPriceArray[$resultElement['manager']] += $resultElement['price']?$resultElement['price']:0;
        }else{
            $monthPriceArray[$resultElement['manager']] = $resultElement['price']?$resultElement['price']:0;
        }

		if($resultElement['date_create'] == date('Y-m-d')){
//			if(array_key_exists($resultElement['manager'], $dayMorjaArray)){
//				$dayMorjaArray[$resultElement['manager']] += [$resultElement['morja'],$resultElement['price']];
//			}else{
//				$dayMorjaArray[$resultElement['manager']] = [$resultElement['morja'],$resultElement['price']];
//			}
            if(array_key_exists($resultElement['manager'], $dayMorjaArray)){
				$dayMorjaArray[$resultElement['manager']] += $resultElement['morja']?$resultElement['morja']:0;
			}else{
				$dayMorjaArray[$resultElement['manager']] = $resultElement['morja']?$resultElement['morja']:0;
			}
            if(array_key_exists($resultElement['manager'], $dayPriceArray)){
                $dayPriceArray[$resultElement['manager']] += $resultElement['price']?$resultElement['price']:0;
            }else{
                $dayPriceArray[$resultElement['manager']] = $resultElement['price']?$resultElement['price']:0;
            }

		}elseif($resultElement['date_create'] >= $week_start && $resultElement['date_create'] <= $week_end){
			if(array_key_exists($resultElement['manager'], $weekMorjaArray)){
				$weekMorjaArray[$resultElement['manager']] += $resultElement['morja']?$resultElement['morja']:0;
			}else{
				$weekMorjaArray[$resultElement['manager']] = $resultElement['morja']?$resultElement['morja']:0;
			}
            if(array_key_exists($resultElement['manager'], $weekPriceArray)){
                $weekPriceArray[$resultElement['manager']] += $resultElement['price']?$resultElement['price']:0;
            }else{
                $weekPriceArray[$resultElement['manager']] = $resultElement['price']?$resultElement['price']:0;
            }
		}
	}

	array_push($queryArray, $resultElement);
	
	if($_COOKIE['admin'] == 'kabosubeay') {
		$leadsForMonth .= "
		<tr>
		<td style='cursor: pointer; background: lightslategrey; color: red; border: 1px solid black;' class='delete' data-id='$resultElement[id]'>rm</td>
		<td style='cursor: pointer; background: lightslategrey; color: lightgreen; border: 1px solid black;' class='save' data-id='$resultElement[id_lead]'>ok</td>
		<td>$resultElement[id_lead]</td>
		<td><input type='date' id='date_create{$resultElement['id_lead']}' value='$resultElement[date_create]'></td>
		<td><input type='date' id='date_shipment{$resultElement['id_lead']}' value='$resultElement[date_shipment]'></td>
		<td><input type='text' id='source{$resultElement['id_lead']}' value='$resultElement[source]'></td>
		<td><input type='text' id='client_tel{$resultElement['id_lead']}' value='$resultElement[client_tel]'></td>
		<td><input type='text' id='oneC_number{$resultElement['id_lead']}' value='$resultElement[oneC_number]'></td>
		<td><input type='text' id='oneC_title{$resultElement['id_lead']}' value='$resultElement[oneC_title]'></td>
		<td><input type='text' id='provider_title{$resultElement['id_lead']}' value='$resultElement[provider_title]'></td>
		<td><input type='text' id='price{$resultElement['id_lead']}' value='$resultElement[price]'></td>
		<td><input type='text' id='cost_price{$resultElement['id_lead']}' value='$resultElement[cost_price]'></td>
		<td><input type='text' id='morja{$resultElement['id_lead']}' value='$resultElement[morja]'></td>
		<td><input type='text' id='pay_form{$resultElement['id_lead']}' value='$resultElement[pay_form]'></td>
		<td><input type='text' id='manager{$resultElement['id_lead']}' value='$resultElement[manager]'></td>
		</tr>
		";	
	} else {
		$leadsForMonth .= "
		<tr>
		<td><a href='https://tema24.amocrm.ru/leads/detail/$resultElement[id_lead]' target='_blank'>$resultElement[id_lead]</a></td>
		<td>$resultElement[date_create]</td>
		<td>$resultElement[date_shipment]</td>
		<td>$resultElement[source]</td>
		<td>$resultElement[client_tel]</td>
		<td>$resultElement[oneC_number]</td>
		<td>$resultElement[oneC_title]</td>
		<td>$resultElement[provider_title]</td>
		<td>$resultElement[price]</td>
		<td>$resultElement[cost_price]</td>
		<td>$resultElement[morja]</td>
		<td>$resultElement[pay_form]</td>
		<td>$resultElement[manager]</td>
		</tr>
		";
	}
}



?>

<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="favicon.ico">
	<title>Список сделок [МТ]</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, intial scale=1.0">
	<!-- <link id="theme" rel="stylesheet" type="text/css" href="stylefortableLight.css"> -->
	<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script> 
	<script src="scriptfortable.js" ></script>
	<style>
	.managerBox > div {
		overflow: auto !important;
	}
	</style>
</head>
<body>
	<form>
	<input type="text" name="access" value="<?php echo @$_GET['access']; ?>" hidden>
<div class="top">
	<div class="search">
		<input class="searchForm" type="search" name="search" placeholder="Поиск:" autocomplete = "off">
	</div>
	<div class="divTurn">
	<p onclick="turnOn()" class="turn">Theme</p>
	</div>
</div>
<div class="filterInputs">
		<label for="dateInterval1">Дата от:</label>
		<input class="inputFilters" id="dateInterval1" type="date" name="dateInterval1">
		<label for="dateInterval2">Дата до:</label>
		<input class="inputFilters" id="dateInterval2" type="date" name="dateInterval2">
		<label for="managerName">Внутренний № менеджера:</label>
		<input class="inputFilters" id="managerName" type="text" name="managerName" autocomplete = "off">
		<input class="button" type="submit" name="filter_btn" value="Найти">
		<input type="button" onclick="javascript:location.href=window.location.pathname + '?access=I8ju2wJkbSAsgQ9iHB9P'" class="button" value="Сбросить">
</div>
	</form>
<?php if($_GET['dateInterval1'] || $_GET['dateInterval2'] || $_GET['search'] || $_GET['managerName']) {
echo "
<div class='managerBox2'>
	<div class='selectBoxDate'>
		<table>
	Маржа по фильтру:
	<tr class='table_head'>		
        <td>Менеджер</td>
        <td>Цена</td>
        <td>Маржа</td>

    </tr>
			";
    $summ = 0;
    $sump = 0;
    foreach ($morjaArray as $key=>$value) {
        foreach ($priceArray as $key=>$value) {
            $summ += $morjaArray[$key];
            $sump += $priceArray[$key];
            echo "<tr><td>$key</td><td>$priceArray[$key]</td><td>$morjaArray[$key]</td></tr>";
        }
        break;
    }
			echo "
			<tr style='border: 1px solid white;'>
			<td><i>Итого</i></td>
			<td>$sump</td>
	<td>$summ</td>
			</tr>
		</table>
	</div>
</div>
";} else {
	echo "
<div class='managerBox'>
	<div class='managerBoxDayNow'>
	<table>
	Маржа за текущий день:
	<tr class='table_head'>		
        <td>Менеджер</td>
        <td>Цена</td>
        <td>Маржа</td>

    </tr>
	";
	$summ = 0;
    $sump = 0;
    foreach ($dayMorjaArray as $key=>$value) {
        foreach ($dayPriceArray as $key=>$value) {
            $summ += $dayMorjaArray[$key];
            $sump += $dayPriceArray[$key];
            echo "<tr><td>$key</td><td>$dayPriceArray[$key]</td><td>$dayMorjaArray[$key]</td></tr>";
        }
        break;
    }
//	foreach ($dayMorjaArray as $key=>$value) {
//        $summ += $value;
//        echo "<tr>
//						<td>$key</td><td>$value</td><td>$value</td>
//					</tr>";
//    }
////    foreach ($dayPriceArray as $key=>$value) {
////        $sump += $value;
////        echo "<td>$value</td>";
////    }
	echo "
	<tr style='border: 1px solid white;'>
	<td><i>Итого</i></td>	
	<td>$sump</td>
	<td>$summ</td>
	</tr>
	</table>
	</div>
	<div class='managerBoxWeekNow'>
			<table>
	Маржа за текущую неделю:
	<tr class='table_head'>		
        <td>Менеджер</td>
        <td>Цена</td>
        <td>Маржа</td>

    </tr>
		";
    $summ = 0;
    $sump = 0;
    foreach ($weekMorjaArray as $key=>$value) {
        foreach ($weekPriceArray as $key=>$value) {
            $summ += $weekMorjaArray[$key];
            $sump += $weekPriceArray[$key];
            echo "<tr><td>$key</td><td>$weekPriceArray[$key]</td><td>$weekMorjaArray[$key]</td></tr>";
        }
        break;
    }
	echo "
	<tr style='border: 1px solid white;'>
	<td><i>Итого</i></td>
	<td>$sump</td>
	<td>$summ</td>
	</tr>
		</table>
	</div>
	<div class='managerBoxMonthNow'>
					<table>
	Маржа за текущий месяц:
	<tr class='table_head'>		
        <td>Менеджер</td>
        <td>Цена</td>
        <td>Маржа</td>

    </tr>
		";
    $summ = 0;
    $sump = 0;
    foreach ($monthMorjaArray as $key=>$value) {
        foreach ($monthPriceArray as $key=>$value) {
            $summ += $monthMorjaArray[$key];
            $sump += $monthPriceArray[$key];
            echo "<tr><td>$key</td><td>$monthPriceArray[$key]</td><td>$monthMorjaArray[$key]</td></tr>";
        }
        break;
    }
	echo "
	<tr style='border: 1px solid white;'>
	<td><i>Итого</i></td>
	<td>$sump</td>
	<td>$summ</td>
	</tr>
		</table>
	</div>
</div> "; 
}?>
	
	<div class="leadsForMonth">
		<table>
            <tr class="table_head">
                <?php if($_COOKIE['admin'] == 'kabosubeay') : ?>
                    <td></td>
                    <td></td>
                <?php endif; ?>
                <td>id сделки</td>
                <td>Создано</td>
                <td>Отгружено</td>
                <td>Источник</td>
                <td>Тел. клиента</td>
                <td>№ 1с</td>
                <td>Куда</td>
                <td>Поставщики</td>
                <td>Цена</td>
                <td>Себестоимость</td>
                <td>Маржа</td>
                <td>Форма оплаты</td>
                <td>Менеджер</td>
            </tr>
			<?php
				echo $leadsForMonth;
			?>

		</table>
	</div>
<!-- 	<div class="">
		Perehod po stranicam
	</div> -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
$('#new_record').click(function () {
	$.ajax({
		method: 'POST',
		url: 'admin_table.php',
		data: {
			action: 'insert',
			id_lead: $('#id_lead').val(),
			date_create: $('#date_create').val(),
			date_shipment: $('#date_shipment').val(),
			source: $('#source').val(),
			client_tel: $('#client_tel').val(),
			oneC_number: $('#oneC_number').val(),
			oneC_title: $('#oneC_title').val(),
			provider_title: $('#provider_title').val(),
			price: $('#price').val(),
			cost_price: $('#cost_price').val(),
			morja: $('#morja').val(),
			pay_form: $('#pay_form').val(),
			manager: $('#manager').val(),
		},
		success: function() {
				window.location.reload();
		}
	});
});

$('.delete').click(function () {
	$.ajax({
		method: 'POST',
		url: 'admin_table.php',
		data: {
			action: 'delete',
			id: $(this).data('id')
		},
		success: function() {
				window.location.reload();
		}
	});
});

$('.save').click(function () {
	$.ajax({
		method: 'POST',
		url: 'admin_table.php',
		data: {
			action: 'update',
			id_lead: $(this).data('id'),
			date_create: $('#date_create'+$(this).data('id')).val(),
			date_shipment: $('#date_shipment'+$(this).data('id')).val(),
			source: $('#source'+$(this).data('id')).val(),
			client_tel: $('#client_tel'+$(this).data('id')).val(),
			oneC_number: $('#oneC_number'+$(this).data('id')).val(),
			oneC_title: $('#oneC_title'+$(this).data('id')).val(),
			provider_title: $('#provider_title'+$(this).data('id')).val(),
			price: $('#price'+$(this).data('id')).val(),
			cost_price: $('#cost_price'+$(this).data('id')).val(),
			morja: $('#morja'+$(this).data('id')).val(),
			pay_form: $('#pay_form'+$(this).data('id')).val(),
			manager: $('#manager'+$(this).data('id')).val(),
		},
		success: function() {
				window.location.reload();
		}
	});
});
</script>
</body>
</html>
