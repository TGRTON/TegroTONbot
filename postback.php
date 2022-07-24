<?php 
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
error_log( "Hello, errors!" );

include "config.php";

$tofile = '';
foreach($_POST AS $key => $value) {
    ${$key} = trim(filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS)); 
	$tofile .= $key.':'.$value.'
';
} // end FOREACH

if($file = fopen("response.txt", "w+")){
		fputs($file, $tofile);
		fclose($file);
} // end frite to file

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");

$data = $_POST;
ksort($data);
$str = http_build_query($data);
$sign2 = md5($str . $roskassa_secretkey);

$tofile = "
===========
".$str."
sign from roskassa: ".$sign."
sign from script: ".$sign2;
if($file = fopen("response.txt", "a+")){
		fputs($file, $tofile);
		fclose($file);
} // end frite to file

// check for pending order
$chat_id = $order_id;

$tofile = "
===========
chat_id: ".$chat_id."
===========";
if($file = fopen("response.txt", "a+")){
		fputs($file, $tofile);
		fclose($file);
} // end frite to file

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

include "acceptton.php";
acceptton($amount, "Tegro Money");

// check for pending order
		
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
				'reply_markup' => $encodedMarkup
			);
		}else{
			//Отправка сообщения
			$data = array(
				'chat_id'      => $id,
				'text'     => $message
			);
		}
       
        $out = sendit($data, 'sendMessage');       
        return $out;
}   
?>