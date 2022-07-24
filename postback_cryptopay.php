<?php 
$data_init = file_get_contents('php://input');
$data = json_decode($data_init, true);

include "config.php";

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

###########SAVE DATA############
$date_time = date("j-m-Y G:i");
$results = "

=========$date_time========
";
$results .= print_r($data, true);

if($file = fopen("debug.txt", "a+")){
		fputs($file, $results);
		fclose($file);
} // end frite to file
###########SAVE DATA############

// LANGUAGE
$str3select = "SELECT `lang` FROM `users` WHERE `chatid`='$chat_id'";
$result3 = mysqli_query($link, $str3select);
$row3 = @mysqli_fetch_object($result3);
if($row3->lang != ''){
	$langcode = $row3->lang;
}else{
	$langcode = 0;	
}
require "langs.php";
for ($i = 0; $i < count($text); $i++) {
	for ($k = 0; $k < count($text[$i]); $k++) {
		$text[$i][$k] = str_replace("&#13;&#10;", "
", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#9;", "", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#60;", "<", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#62;", ">", $text[$i][$k]);
		$text[$i][$k] = str_replace("&#39;", "'", $text[$i][$k]);
		$text[$i][$k] = str_replace("", "", $text[$i][$k]);						
	} // end FOR
} // end FOR	
// LANGUAGE

if($data['update_type'] == 'invoice_paid'){

	$chat_id = $data['payload']['payload'];
	$paidSum = $data['payload']['amount'];
	
include "acceptton.php";
acceptton($paidSum, "CryptoPayBot");	
				
}

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