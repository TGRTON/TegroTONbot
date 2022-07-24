<?php 
include "config.php";

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

$str16select = "SELECT * FROM `dailyrate` WHERE `rowid`='1'";
$result16 = mysqli_query($link, $str16select);
$row16 = @mysqli_fetch_object($result16);
		
$old = $row16->rate; 		
			
//GET USDT RATE
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getCoinPrice");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);
curl_close ($ch);
$res = json_decode($server_output, true);

$new = round($res["result"], 2);
//GET USDT RATE	

if ($old === $new) {
    $percent = 0;
} elseif ($old < $new) {
    $diff = $new - $old;
    $percent = $diff / $old * 100;
} else {
    $diff = $old - $new;
    $percent = $diff / $old * 100;
}

if($percent >= 10){
	
	$percent = round($percent, 2);
	
	$c = 0;	
	$str2select = "SELECT * FROM `users` WHERE `subcr`='1'";
	#$result = mysql_query($str2select);
	$result = mysqli_query($link, $str2select);
	while($row = @mysqli_fetch_object($result)){

		if($c > 28)	{
			sleep(1);
			$c = 0;
		}
	
		if($row->lang == 0){
			$message = 	"📌 ATTENTION! The fluctuation of the Toncoin exchange rate amounted to $percent%.
	Yesterday's course: $old
	Today's course: $new
	
	To unsubscribe from notifications, run the /stop command";
		} else {
			$message = 	"📌 ВНИМАНИЕ! Колебание курса Toncoin составило $percent%. 
	Вчерашний курс: $old
	Сегодняшний курс: $new
	
	Для отписки от уведомлений выполните команду /stop";	
		}
		
		$response = array(
			'chat_id' => $row->chatid, 
			'text' => $message,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
	
		$c++;
	}  // end WHILE MySQL	
}

$str2upd = "UPDATE `dailyrate` SET `rate`='$new' WHERE `rowid`='1'";
mysqli_query($link, $str2upd);

function sendit($response, $restype){
	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/'.$restype);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_exec($ch);
	curl_close($ch);	
}

function send($id, $message, $keyboard) {   
		
		//Удаление клавы
		if($keyboard == "DEL"){		
			$keyboard = array(
				'remove_keyboard' => true
			);
		}
		if($keyboard){
			//Отправка клавиатуры
			$encodedMarkup = json_encode($keyboard);
			
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'reply_markup' => $encodedMarkup,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True
			);
		}else{
			//Отправка сообщения
			$data = array(
				'chat_id'      => $id,
				'text'     => $message,
				'parse_mode' => 'HTML',
				'disable_web_page_preview' => True				
			);
		}
       
        $out = sendit($data, 'sendMessage');       
        return $out;
}    
?>