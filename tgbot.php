<?php 
include "config.php";

$data = file_get_contents('php://input');
$data = json_decode($data, true);

include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

#################################

if (isset($data['message']['chat']['id']))
{
	$chat_id = $data['message']['chat']['id'];
}
elseif(isset($data['callback_query']['message']['chat']['id']))
{
	$chat_id = $data['callback_query']['message']['chat']['id'];
}
elseif(isset($data['inline_query']['from']['id']))
{
	$chat_id = $data['inline_query']['from']['id'];
}

// Register new user in DB
if(isset($data['callback_query']['message']['chat']['username']) && $data['callback_query']['message']['chat']['username'] != ''){
	$fname = $data['callback_query']['message']['chat']['first_name'];
	$lname = $data['callback_query']['message']['chat']['last_name'];
	$uname = $data['callback_query']['message']['chat']['username'];
}else{
	$fname = $data['message']['from']['first_name'];
	$lname = $data['message']['from']['last_name'];
	$uname = $data['message']['from']['username'];	
}
$time = time();
if($chat_id != ''){
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	if(mysqli_num_rows($result) == 0){
		$str2ins = "INSERT INTO `users` (`chatid`,`fname`,`lname`,`username`) VALUES ('$chat_id','".addslashes($fname)."','".addslashes($lname)."','$uname')";
		mysqli_query($link, $str2ins);	
		$result = mysqli_query($link, $str2select);
	}
	$row = @mysqli_fetch_object($result);	
}
// Register new user in DB

// LANGUAGE
$str3select = "SELECT `lang` FROM `users` WHERE `chatid`='$chat_id'";
$result3 = mysqli_query($link, $str3select);
$row3 = @mysqli_fetch_object($result3);
	
if($row3->lang != ''){
	$langcode = $row3->lang;
}else{
	$langcode = 1;	
}
###################
$langcode = langCode($langcode);
###################
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

checkInlineQuery();

############### START ###############
if( preg_match("/\/start/i", $data['message']['text'] )){

//register subscriber
$newrecord = $chat_id."|".addslashes($data['message']['from']['first_name'])." ".addslashes($data['message']['from']['last_name'])."|".addslashes($data['message']['from']['username']);
if(file_exists('subscribers.php')) include 'subscribers.php';
if(isset($user) && count($user) > 0){
	if(!in_array($newrecord, $user)){
		$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
		
	}
}else{
	$towrite = "\$user[] = '".addslashes($newrecord)."';\n";
} // end IF-ELSE count($user) > 0

if(isset($towrite) && $towrite != ''){
	if($file = fopen("subscribers.php", "a+")){
		fputs($file,$towrite);
		fclose($file);
	} // end frite to file
}
//register subscriber

// record referral
$ref = trim(str_replace("/start", "", $data['message']['text']));
if($ref != ''){
	if($ref != $chat_id){
		$str2select = "SELECT `ref` FROM `users` WHERE `chatid`='$chat_id'";
		$result = mysqli_query($link, $str2select);
		$row = @mysqli_fetch_object($result);
		if($row->ref < 10){
			$str2upd = "UPDATE `users` SET `ref`='$ref' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			$response = array(
					'chat_id' => $ref,
					'text' => hex2bin('F09F92B0').' '.$data['message']['from']['first_name'].' '.$data['message']['from']['last_name'].' зарегистрировался по вашей партнерской ссылке.
			
Используйте эту ссылку для приглашения пользователей:
t.me/tegrotonbot?start='.$ref);
			sendit($response, 'sendMessage');			
		}
	}
}
// record referral

#mainmenu("Привет! Я Ваш виртуальный помощник в мире TON Coin. ");
setLangiage();


}
elseif( preg_match("/\/help/i", $data['message']['text'] )){
############### HELP ###############

}
elseif( preg_match("/stop/i", $data['message']['text'] )){
############### STOP ###############

}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][2])."/", $data['message']['text'])){
	//Wallet
	$str2select = "SELECT * FROM `wallets` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	if(mysqli_num_rows($result) == 0){
	
		$arInfo["inline_keyboard"][0][0]["callback_data"] = 1;
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][14];
		send($chat_id, $text[$langcode][15], $arInfo); 
	
	}else{
		walletslist("");
	}


}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][3])."/", $data['message']['text'])){
			// buy TON
			$response = array(
				'chat_id' => $chat_id, 
				'text' => $text[$langcode][53],
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');	
				
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][4])."/", $data['message']['text'])){
	//Giveaway
	$str12select = "SELECT * FROM `users` WHERE `ref`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$numOfReferals = mysqli_num_rows($result12);

	$str14select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result14 = mysqli_query($link, $str14select);
	$row14 = @mysqli_fetch_object($result14);
		
	$refbalance = ($row14->refbalance > 0) ? $row14->refbalance : "0.00";
		
	$respmess = $text[$langcode][16];
	$respmess = str_replace("%refpercent%", $refpercent, $respmess);
	$respmess = str_replace("%numOfReferals%", $numOfReferals, $respmess);
	$respmess = str_replace("%refbalance%", $refbalance, $respmess);
	$respmess = str_replace("%chat_id%", $chat_id, $respmess);			
	$response = array(
		'chat_id' => $chat_id,
		'text' => hex2bin('F09F92B0').$respmess);
	sendit($response, 'sendMessage');	
	
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][5])."/", $data['message']['text'])){
	//Balance
	$total = 0;
	
	$str2select = "SELECT * FROM `wallets` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	while($row = @mysqli_fetch_object($result)){
		
		$walletno = $row->wallet;
		
/*		//parsing
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getAddressInformation");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,
					"address=".$walletno);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
		
		$dat = array(
			'address' => $walletno
		);
		
		//parsing
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));		
		
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$server_output = curl_exec ($ch);
		curl_close ($ch);
		$res = json_decode($server_output, true);

		$total = $total + $res["result"]["balance"];

	}  // end WHILE MySQL		

	$xvost = substr($total, -9);
	$nachalo = str_replace($xvost, "", $total);
	$totalsum = $nachalo.".".$xvost;
	
	$message = str_replace("%Balance%", $totalsum, $text[$langcode][17]);

	$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result16 = mysqli_query($link, $str16select);
	$row16 = @mysqli_fetch_object($result16);
				
	$nftbalance = ($row16->nft_balance != '') ? $row16->nft_balance : 0;
	$message = str_replace("%NFTBalance%", $nftbalance, $message);	
	$message = str_replace("%chat_id%", $chat_id, $message);
	
	$nftcat = ($row16->cat != '') ? $row16->cat : 0;
	$message = str_replace("%NFTCat%", $nftcat, $message);	
	$nftdog = ($row16->dog != '') ? $row16->dog : 0;
	$message = str_replace("%NFTDog%", $nftdog, $message);	
	$nftcust = ($row16->cust != '') ? $row16->cust : 0;
	$message = str_replace("%NFTCust%", $nftcust, $message);	
	
	$tegroton = ($row16->tegroton != '') ? $row16->tegroton : 0;
	$message = str_replace("%TegroToken%", $tegroton, $message);			

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

	$sumInUSD = $totalsum * $res["result"];
	$message = str_replace("%BalanceUSD%", $sumInUSD, $message);		
	//GET USDT RATE	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $message,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');
	
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][6])."/", $data['message']['text'])){
	//Team
	$response = array(
		'chat_id' => $chat_id, 
		'text' => "<b>Наше сообщество:</b>
▸ <a href='https://t.me/tonnetworkchat'>Ton Network Community 🇷🇺 </a>
▸ <a href='https://t.me/tonnetworkchat_en'>Ton Network Community 🇬🇧</a> 

<b>Каналы:</b>
▸ <a href='https://t.me/tonblockchain'>The Open Network </a>
▸ <a href='https://t.me/toncoin_help'>The Open Network Helper</a> 
▸ <a href='https://t.me/ruton'>TON News</a> 
▸ <a href='https://t.me/toncoin_rus'>TON Community RU</a> 
▸ <a href='https://t.me/toncoin'>TON Community EN</a> 
▸ <a href='https://t.me/ton_zh'>TON Community ZH</a> 
▸ <a href='https://t.me/tonbase'>TON Base</a> 
▸ <a href='https://t.me/givemetonru'>Дайте TON!</a> 

<b>Чаты:</b>
▸ <a href='https://t.me/toncoin_rus_chat'>TON Community Chat RU</a> 
▸ <a href='https://t.me/toncoin_chat'>TON Community Chat EN</a> 
▸ <a href='https://t.me/toncoin_zh'>TON Community Chat ZH</a> 
▸ <a href='https://t.me/tonbasechat'>TON Base Chat</a> 

<b>Разработка:</b>
▸ <a href='https://t.me/tondev'>TON Developers Chat</a> ",
		'parse_mode' => 'HTML',
		'disable_web_page_preview' => True);	
	#sendit($response, 'sendMessage');
	
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][7])."/", $data['message']['text'])){
	//Donate
	$url2 = 'ton://transfer/'.$verifyrecepient;
	$arInfo["inline_keyboard"][0][0]["text"] = "Открыть keeper";
	$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url2);	
	$arInfo["inline_keyboard"][0][1]["callback_data"] = 2;
	$arInfo["inline_keyboard"][0][1]["text"] = "Показать кошелек";		
	send($chat_id, "Приветствуем тебя, $fname

Мы будем очень рады, если ты нас поддержишь 🤗

Пожертвования помогают проекту развиваться еще быстрее, а авторам поднимают настроение и мотивацию делать для вас еще больше интересного функционала.

Спасибо большое!", $arInfo); 	

}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][9])."/", $data['message']['text'])){
	// Langiage
	setLangiage();
		
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][8])."/", $data['message']['text'])){
	//Mining
	#miningMenu("");

}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][11])."/", $data['message']['text'])){
			
		$arInfo["inline_keyboard"][0][0]["callback_data"] = 10;
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][18];
		$arInfo["inline_keyboard"][1][0]["callback_data"] = 11;
		$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][19];
		$arInfo["inline_keyboard"][2][0]["callback_data"] = 12;
		$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][20];
		$arInfo["inline_keyboard"][3][0]["callback_data"] = 13;
		$arInfo["inline_keyboard"][3][0]["text"] = $text[$langcode][21];						
		send($chat_id, $text[$langcode][22], $arInfo); 			
	
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][12])."/", $data['message']['text'])){
	
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	$respmess = $text[$langcode][23];
	$respmess = str_replace("%balance%", $row->balance, $respmess);	
	$respmess = str_replace("%refbalance%", $row->refbalance, $respmess);		
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $respmess,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	
	
	$str3select = "SELECT * FROM `deposits` WHERE `chatid`='$chat_id' ORDER BY `rowid`";
	$result3 = mysqli_query($link, $str3select);
	if(mysqli_num_rows($result3) == 0){
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][24],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');		
		
	}else{
	
	$tomessage = "";
	while($row3 = @mysqli_fetch_object($result3)){
		$timeend = date("d/m/Y", $row3->timeend);
		$income = $row3->sum * $row3->percent / 100;
		
		$respmess = $text[$langcode][25];
		$respmess = str_replace("%sum%", $row3->sum, $respmess);	
		$respmess = str_replace("%timeend%", $timeend, $respmess);	
		$respmess = str_replace("%income%", $income, $respmess);	
		
		$tomessage .= $respmess."
		
";
	}  // end WHILE MySQL
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][26]."
".$tomessage,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');	
	
	}
		
	
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][13])."|Back/", $data['message']['text'])){

	mainmenu("");
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][54])."/", $data['message']['text'])){
				
	settingsMenu("");

}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][55])."/", $data['message']['text'])){
	//Buy NFT
		clean_temp_sess();
/*		$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','walletfor_nft')";
		mysqli_query($link, $str4ins);		

		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][56],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');*/
		NFTMenu("");
}
elseif( preg_match("/".str_replace("-", "\-", "Pre-sale Token")."/", $data['message']['text'])){

	TONMenu();
	
}
elseif( preg_match("/".str_replace("(", "\(", str_replace(")", "\)", "Tegro (TON)"))."/", $data['message']['text'])){
	
	processWallet();
	
}
elseif( preg_match("/".str_replace("(", "\(", str_replace(")", "\)", "Tegro (BEP20)"))."/", $data['message']['text'])){

	$btnurl[0] = 'https://telegra.ph/Kak-kupit-Tegro-Token-TGR-BEP20-04-13';
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][93];
	$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($btnurl[0]);
	$btnurl[1] = 'https://tegro.money/lk/payout/';
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][94];
	$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($btnurl[1]);
	$btnurl[2] = 'https://pancakeswap.finance/swap?outputCurrency=0xd9780513292477C4039dFdA1cfCD89Ff111e9DA5';
	$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][95];
	$arInfo["inline_keyboard"][2][0]["url"] = rawurldecode($btnurl[2]);		
	send($chat_id, "Текст перед кнопками", $arInfo);
	
}
elseif( preg_match("/The Token/", $data['message']['text'])){			

	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][96],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');
}
elseif( preg_match("/Pre-sale TGR/", $data['message']['text'])){	

	processWallet();

}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][106])."/", $data['message']['text'])){
	
	instmenu();
	
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][66])."/", $data['message']['text'])){
	//Buy NFT
		clean_temp_sess();
		$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','wal4cat_nft')";
		mysqli_query($link, $str4ins);		

		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][68],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][67])."/", $data['message']['text'])){
	//Buy NFT
		clean_temp_sess();
		$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','wal4dog_nft')";
		mysqli_query($link, $str4ins);		

		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][68],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
		
}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][78])."/", $data['message']['text'])){
	//Buy NFT
		clean_temp_sess();
		$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','wal4cust_nft')";
		mysqli_query($link, $str4ins);		

		$response = array(
			'chat_id' => $chat_id, 
			'text' => $text[$langcode][68],
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');								

}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][63])."/", $data['message']['text'])){
		
	//Subscribe
	$str2upd = "UPDATE `users` SET `subcr`='1' WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2upd);
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][64],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	

}
elseif( preg_match("/".str_replace("?", "", $text[$langcode][71])."/", $data['message']['text'])){

	$str12select = "SELECT * FROM `users` WHERE `ref`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$numOfReferals = mysqli_num_rows($result12);

	$mes = str_replace("%numOfReferals%", $numOfReferals, $text[$langcode][75]);
	$mes = str_replace("%chat_id%", $chat_id, $mes);

	if($row->verified == 1){
		
		$response = array(
			'chat_id' => $chat_id, 
			'text' => $mes,
			'parse_mode' => 'HTML');	
		sendit($response, 'sendMessage');
		
	}else{

		$url = 'https://t.me/tegrocatnft';
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][72];
		$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);	
		$url2 = 'https://t.me/gusevself';
		$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][73];
		$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($url2);	
		$arInfo["inline_keyboard"][2][0]["callback_data"] = "checksubscr";
		$arInfo["inline_keyboard"][2][0]["text"] = $text[$langcode][74]." ✅";		
		send($chat_id, $mes, $arInfo); 
	}
	
		send2('sendMessage',
			[
				'chat_id' => $chat_id,
				'text' => $text[$langcode][108],
				'reply_markup' =>
				[
					'inline_keyboard' =>
					[
						[
							[
								'text' => $text[$langcode][109],
								'switch_inline_query' => ''
							]
						]
					]
				]
			]);
	
}
elseif( preg_match("/\/stop/", $data['message']['text'])){
		
	//Subscribe
	$str2upd = "UPDATE `users` SET `subcr`='0' WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2upd);
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][65],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');		
		
}
else{
	
	if(isset($data['callback_query']['data']) && $data['callback_query']['data'] != ''){
		
		if( $data['callback_query']['data'] == 1 ){
			
			clean_temp_sess();
			$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','addwallet')";
			mysqli_query($link, $str4ins);			
			
			$response = array(
				'chat_id' => $chat_id, 
				'text' => $text[$langcode][27],
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');				
			
		}
		elseif( $data['callback_query']['data'] == 2 ){
			
			$response = array(
				'chat_id' => $chat_id, 
				'text' => "<code>$verifyrecepient</code>",
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');
			
		}
		elseif( preg_match("/payonsite/", $data['callback_query']['data'])){
			
			$sum = str_replace("payonsite", "", $data['callback_query']['data']);
			
			$paylink = makelink($sum);
			
			$tomessage = str_replace("%sumtopay%", $sum, $text[$langcode][97]);				
			$url = $paylink;
			$arInfo["inline_keyboard"][0][0]["text"] = hex2bin('F09F92B3').$tomessage;
			$arInfo["inline_keyboard"][0][0]["url"] = rawurldecode($url);
			send($chat_id, $text[$langcode][98], $arInfo);	
				
		}
		elseif( preg_match("/paybyton/", $data['callback_query']['data'])){						

			$sum = str_replace("paybyton", "", $data['callback_query']['data']);
			messageIfPayByTON($sum);
			
		}
		elseif( $data['callback_query']['data']  == 3){
		
			sendMeSumTON();
			
		}
		elseif( $data['callback_query']['data']  == 4){			
		
			processWallet2();													

		}
		elseif( $data['callback_query']['data'] > 9 && $data['callback_query']['data'] < 14 ){

			$depotype = $data['callback_query']['data'];

			$depocode = rand_string(25);
			$str2upd = "UPDATE `users` SET `depocode`='$depocode' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			clean_temp_sess();
			$str4ins = "INSERT INTO `temp_sess` (`chatid`,`action`) VALUES ('$chat_id','$depotype')";
			#mysqli_query($link, $str4ins);			
			
			$respmess = $text[$langcode][28];
			$respmess = str_replace("%verifyrecepient%", $verifyrecepient, $respmess);			
			
			$response = array(
				'chat_id' => $chat_id, 
				'text' => $respmess,
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');		

			$respmess2 = $text[$langcode][29];
			$respmess2 = str_replace("%depocode%", $depocode, $respmess2);	
			
			$response = array(
				'chat_id' => $chat_id, 
				'text' => $text[$langcode][29],
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');	
			
			$response = array(
				'chat_id' => $chat_id, 
				'text' => "<code>".$depocode."</code>",
				'parse_mode' => 'HTML');	
			sendit($response, 'sendMessage');				
		
			$arInfo["inline_keyboard"][0][0]["callback_data"] = "checkdeposit".$depotype;
			$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][30];		
			$url = 'ton://transfer/'.$verifyrecepient.'?&text='.$depocode;
			$arInfo["inline_keyboard"][0][1]["text"] = "Go to Tonkeeper";
			$arInfo["inline_keyboard"][0][1]["url"] = rawurldecode($url);	
			send($chat_id, $text[$langcode][31], $arInfo); 				

		}
		elseif( $data['callback_query']['data'] == 100  || $data['callback_query']['data'] == 101){
			
			$langcode = $data['callback_query']['data'] - 100;
		
			$str2upd = "UPDATE `users` SET `lang`='".$langcode."' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str2upd);
			
			###################
			$langcode = langCode($langcode);
			###################
			
			mainmenu($text[$langcode][1]);

		}
		elseif($data['callback_query']['data'] == 251){
			
			mainmenu($text[$langcode][1]);			
									
		}
		elseif( preg_match("/walletnum/", $data['callback_query']['data']) ){

			$walletid = str_replace("walletnum", "", $data['callback_query']['data']);
		
			walletactions($walletid);
			
		}
		elseif( preg_match("/goverify/", $data['callback_query']['data']) ){

			$walletid = str_replace("goverify", "", $data['callback_query']['data']);			
			verify($walletid);

		}
		elseif( preg_match("/chckver/", $data['callback_query']['data']) ){	

			$walletid = str_replace("chckver", "", $data['callback_query']['data']);
			$walletno = getwallet($walletid);	

			$str2select = "SELECT * FROM `wallets` WHERE `rowid`='$walletid'";
			$result = mysqli_query($link, $str2select);
			$row = @mysqli_fetch_object($result);			

/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getTransactions");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$walletno);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$walletno);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$verified = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $verifyrecepient && $res["result"][$i]["out_msgs"][0]["message"] == $row->verifcode){
						$verified = 1;
				}
			} // end FOR			
			
			if($verified == 1){
				
				$str2upd = "UPDATE `wallets` SET `verified`='1' WHERE `rowid`='$walletid'";
				mysqli_query($link, $str2upd);
				walletslist($text[$langcode][32]);
				
				########## REF FEE ##########
				$str12select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
				$result12 = mysqli_query($link, $str12select);
				$row12 = @mysqli_fetch_object($result12);	

				if($row12->ref > 1){
					$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$verifRefFee WHERE `chatid`='".$row12->ref."'";
					mysqli_query($link, $str10upd);	
				}
				########## REF FEE ##########				
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][33],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}

		}
		elseif( preg_match("/chkp/", $data['callback_query']['data']) ){	

			// Check payment for NFT
			$senderid = str_replace("chkp", "", $data['callback_query']['data']);
			$parts = explode("|", $senderid);
			$senderid = $parts[0];
			$sum = $parts[1];

			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$senderid);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$str17select = "SELECT `nftcode` FROM `users` WHERE `chatid`='$chat_id'";
			$result17 = mysqli_query($link, $str17select);
			$row17 = @mysqli_fetch_object($result17);
			
			$verified = 0;
			$paidSumForNFT = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $NFTwalletTON && $res["result"][$i]["out_msgs"][0]["message"] == $row17->nftcode){
						$verified = 1;
						$nanosum = $res["result"][$i]["out_msgs"][0]["value"];
						$xvostNFT = substr($nanosum, -9);
						$nachaloNFT = str_replace($xvostNFT, "", $nanosum);
						$paidSumForNFT = $nachaloNFT.".".$xvostNFT;	
						$nftcodeORIG = $row17->nftcode;						
				}
			} // end FOR
			
			if($verified == 1){
				
				// sum validation
				
				if($sum != (int)$paidSumForNFT){
				
					$response = array(
						'chat_id' => $chat_id, 
						'text' => $text[$langcode][99],
						'parse_mode' => 'HTML');	
					sendit($response, 'sendMessage');

				}else{
				
					#clean_temp_sess();
					delMessage("", $data['callback_query']['message']['message_id']);
					
					$nftcode = rand_string(20);
					$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
					mysqli_query($link, $str2upd);				
					
					$gotTON = (int)$paidSumForNFT;
					
					include "acceptton.php";
					acceptton($gotTON, $senderid);
				}
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][100],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');	
			}	

		}
		elseif( preg_match("/checkdeposit/", $data['callback_query']['data']) ){	

			$depotype = str_replace("checkdeposit", "", $data['callback_query']['data']);
			
			$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
			$result = mysqli_query($link, $str2select);
			$row = @mysqli_fetch_object($result);			
			
/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getTransactions");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$verifyrecepient);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$verifyrecepient);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);
			
			$depofound = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($depofound == 1) continue;
				if($res["result"][$i]["in_msg"]["message"] == $row->depocode){
						$depofound = 1;
						$nanosum = $res["result"][$i]["in_msg"]["value"];
				}
			} // end FOR			
			
			if($depofound == 1){
				
				$depocode2 = rand_string(25);
				$str2upd = "UPDATE `users` SET `depocode`='$depocode2' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);				
				
				$xvost = substr($nanosum, -9);
				$nachalo = str_replace($xvost, "", $nanosum);
				if($nachalo == "")$nachalo = 0;
				$deposum = $nachalo.".".$xvost;	
				
				if($nachalo < 10){
					$response = array(
						'chat_id' => $chat_id, 
						'text' => $text[$langcode][34],
						'parse_mode' => 'HTML');	
					sendit($response, 'sendMessage');					
				}else{
				
					switch ($depotype) {
						case 10:
						$perctotal = 10/12;
						$perc = number_format((float)$perctotal, 2, '.', '');
						$period = 86400*30;
						$refperc = 2;
						break;
						case 11:
						$perctotal = 12/4;
						$perc = number_format((float)$perctotal, 2, '.', '');
						$period = 86400*90;	
						$refperc = 3;				
						break;
						case 12:
						$perctotal = 15/2;
						$perc = number_format((float)$perctotal, 2, '.', '');
						$period = 86400*180;
						$refperc = 4;					
						break;
						case 13:
						$perc = 19.00;
						$period = 86400*365;	
						$refperc = 5;				
						break;															
					}		
					
					$timeend = time() + $period;
					
					$str2ins = "INSERT INTO `deposits` (`chatid`,`timeend`,`sum`,`percent`) VALUES ('$chat_id','$timeend','$deposum','$perc')";
					mysqli_query($link, $str2ins);
					$response = array(
						'chat_id' => $chat_id, 
						'text' => $text[$langcode][35],
						'parse_mode' => 'HTML');	
					sendit($response, 'sendMessage');				
					
					########## REF FEE ##########
					$str12select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
					$result12 = mysqli_query($link, $str12select);
					$row12 = @mysqli_fetch_object($result12);	
	
					#$earn = $deposum / 100 * $depopercent;
					$earn = $deposum / 100 * $refperc;
	
					if($row12->ref > 1){
						$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earn WHERE `chatid`='".$row12->ref."'";
						mysqli_query($link, $str10upd);	
					}
					########## REF FEE ##########				
				}
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][33],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}

		}
		elseif( preg_match("/checkpay4nft/", $data['callback_query']['data']) ){	
			
			// Check payment for NFT
			$senderid = str_replace("checkpay4nft", "", $data['callback_query']['data']);
			
/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getTransactions");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$senderid);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?address=".$senderid);
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			$str17select = "SELECT `nftcode` FROM `users` WHERE `chatid`='$chat_id'";
			$result17 = mysqli_query($link, $str17select);
			$row17 = @mysqli_fetch_object($result17);
			
			if(preg_match("/blogger/", $row17->nftcode)){
				$recepientwallet = "EQCeV3nQd6cHE4DgMsUb4xc5GRITAbxGe1jJsL1c66e7b9c4";
			}else{
				$recepientwallet = $verifyrecepient;
			}
			
			$verified = 0;
			$paidSumForNFT = 0;
			for ($i = 0; $i < count($res["result"]); $i++) {
				if($verified == 1) continue;
				if($res["result"][$i]["out_msgs"][0]["destination"] == $recepientwallet && $res["result"][$i]["out_msgs"][0]["message"] == $row17->nftcode){
						$verified = 1;
						$nanosum = $res["result"][$i]["out_msgs"][0]["value"];
						$xvostNFT = substr($nanosum, -9);
						$nachaloNFT = str_replace($xvostNFT, "", $nanosum);
						$paidSumForNFT = $nachaloNFT.".".$xvostNFT;	
						$nftcodeORIG = $row17->nftcode;
				}
			} // end FOR			
			
			if($verified == 1){
				
				clean_temp_sess();
				delMessage("", $data['callback_query']['message']['message_id']);
				
				$nftcode = rand_string(25)."ZZ";
				$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);				
				
				$dividend = 0;
				$codeparts = explode(";", $nftcodeORIG);
				if($codeparts[0] == "cat"){
					$dividend = $nftCatRate;
				}elseif($codeparts[0] == "dog"){
					$dividend = $nftDogRate;	
				}elseif($codeparts[0] == "blogger"){
					$dividend = $nftCustRate;									
				}
				
				$ssum = $paidSumForNFT/$dividend;
				$gotNFT = number_format($ssum, 2, '.', ''); 
				
				$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result16 = mysqli_query($link, $str16select);
				if(mysqli_num_rows($result16) == 0){
					$str2ins = "INSERT INTO `nft` (`chatid`,`".$codeparts[0]."`) VALUES ('$chat_id','$gotNFT')";
					mysqli_query($link, $str2ins);
				}else{
					$row16 = @mysqli_fetch_object($result16);
					if($codeparts[0] == "cat"){
						$oldsum = $row16->cat;
					}elseif($codeparts[0] == "dog"){
						$oldsum = $row16->dog;	
					}elseif($codeparts[0] == "blogger"){
						$oldsum = $row16->cust;	
						$codeparts[0] = "cust";				
					}
					$newsum = $oldsum + $gotNFT;
					$str11upd = "UPDATE `nft` SET `".$codeparts[0]."`='$newsum' WHERE `chatid`='$chat_id'";
					mysqli_query($link, $str11upd);					
				}
				
				########## REF FEE ##########
				$str12select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
				$result12 = mysqli_query($link, $str12select);
				$row12 = @mysqli_fetch_object($result12);	
				
				$earnRefNFT = $gotNFT / 100 * $NFTRefPercent * $dividend;
				
				if($row12->ref > 1){
					$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earnRefNFT WHERE `chatid`='".$row12->ref."'";
					mysqli_query($link, $str10upd);	
				}
				########## REF FEE ##########		
				
				$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result16 = mysqli_query($link, $str16select);
				$row16 = @mysqli_fetch_object($result16);
				
				$confirmmessage = str_replace("%NFTAdded%", $gotNFT, $text[$langcode][61]);
				if($codeparts[0] == "cat"){
					$confirmmessage = str_replace("%NFTBalance%", $row16->cat, $confirmmessage);
					$confirmmessage = str_replace("%cointype%", "Cat", $confirmmessage);					
				}elseif($codeparts[0] == "dog"){
					$confirmmessage = str_replace("%NFTBalance%", $row16->dog, $confirmmessage);	
					$confirmmessage = str_replace("%cointype%", "Dog", $confirmmessage);	
				}elseif($codeparts[0] == "cust"){
					$confirmmessage = str_replace("%NFTBalance%", $row16->cust, $confirmmessage);	
					$confirmmessage = str_replace("%cointype%", "Custom Anime", $confirmmessage);														
				}			
				
				######## SAVE TRANSACTION ###########
				if($codeparts[0] == "cat"){
					$cat = $gotNFT;
					$dog = 0;
					$cust = 0;
				}elseif($codeparts[0] == "dog"){
					$cat = 0;
					$dog = $gotNFT;	
					$cust = 0;					
				}elseif($codeparts[0] == "cust"){
					$cat = 0;
					$dog = 0;										
					$cust = $gotNFT;					
				}
				$date_time = date("j-m-Y G:i");
				$str2ins = "INSERT INTO `transactions` (`chatid`,`sender`,`date_time`,`nftcat`,`nftdog`,`nftcust`) VALUES ('$chat_id','$senderid','$date_time','$cat','$dog','$cust')";
				mysqli_query($link, $str2ins);
				######## SAVE TRANSACTION ###########						

				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $confirmmessage,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');							
				
			}else{
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "❌ ".$text[$langcode][62],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			}		
			// Check payment for NFT					
		}
		elseif( preg_match("/showbalance/", $data['callback_query']['data']) ){
			
			$walletid = str_replace("showbalance", "", $data['callback_query']['data']);			
			$walletno = getwallet($walletid);			

/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getAddressInformation");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$walletno);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);
			
			if($res["result"]["balance"] == ''){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][36],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
			
			}else{
				
				$xvost = substr($res["result"]["balance"], -9);
				$nachalo = str_replace($xvost, "", $res["result"]["balance"]);
				$newsum = $nachalo.".".$xvost;
				
				$respmess = $text[$langcode][37];
				$respmess = str_replace("%newsum%", $newsum, $respmess);				
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $respmess,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
			}
							
		}
		elseif( preg_match("/showtransactions/", $data['callback_query']['data']) ){
			
			$walletid = str_replace("showtransactions", "", $data['callback_query']['data']);			
			$walletno = getwallet($walletid);
			
/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getTransactions");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$walletno);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getTransactions?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);
			
			$tomessage = "";
			for ($i = 0; $i < count($res["result"]); $i++) {
				$time = date("d/m/Y G:i:s", $res["result"][$i]["utime"]);
				if($res["result"][$i]["out_msgs"][0]["value"] != 0){
					$xvost = substr($res["result"][$i]["out_msgs"][0]["value"], -9);
					$nachalo = str_replace($xvost, "", $res["result"][$i]["out_msgs"][0]["value"]);
					if($nachalo == "")$nachalo = 0;
					$newsum = $nachalo.".".$xvost;	
					
					$respmess = $text[$langcode][38];
					$respmess = str_replace("%newsum%", $newsum, $respmess);
					$respmess = str_replace("%to%", $res["result"][$i]["out_msgs"][0]["destination"], $respmess);					
									
					$tomessage .= $time." ".$respmess."

";
				}
				elseif($res["result"][$i]["in_msg"]["value"] != 0){
					$xvost = substr($res["result"][$i]["in_msg"]["value"], -9);
					$nachalo = str_replace($xvost, "", $res["result"][$i]["in_msg"]["value"]);
					if($nachalo == "")$nachalo = 0;					
					$newsum = $nachalo.".".$xvost;	
					
					$respmess = $text[$langcode][39];
					$respmess = str_replace("%newsum%", $newsum, $respmess);
					$respmess = str_replace("%from%", $res["result"][$i]["in_msg"]["source"], $respmess);						
									
					$tomessage .= $time." ".$respmess."		

";		
				}
			} // end FOR	
			if($tomessage != ""){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomessage,
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
			
			}
			
		}
		elseif( preg_match("/deletewallet/", $data['callback_query']['data']) ){									

			$walletid = str_replace("deletewallet", "", $data['callback_query']['data']);			
			$walletno = getwallet($walletid);

			$arInfo["inline_keyboard"][0][0]["callback_data"] = "confirmdelete".$walletid;
			$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][41];
			send($chat_id, $text[$langcode][40], $arInfo); 			

		}
		elseif( preg_match("/confirmdelete/", $data['callback_query']['data']) ){	
			
			$walletid = str_replace("confirmdelete", "", $data['callback_query']['data']);
			
			$str2del = "DELETE FROM `wallets` WHERE `rowid` = '$walletid'";
			mysqli_query($link, $str2del);

			walletslist($text[$langcode][42]);
		}
		elseif( preg_match("/checksubscr/", $data['callback_query']['data']) ){	
		
			$channel_id1 = "@tegrocatnft";
			$channel_id2 = "@gusevself";
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id1, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			$res = json_decode($res, true);
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/getChatMember');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('chat_id' => $channel_id2, 'user_id' => $chat_id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res2 = curl_exec($ch);
			curl_close($ch);
			$res2 = json_decode($res2, true);			
			
			if ($res['ok'] == true && $res['result']['status'] != "left" && $res2['ok'] == true && $res2['result']['status'] != "left") {
		
				$str2upd = "UPDATE `users` SET `verified`='1' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "👍 ".$text[$langcode][76],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');					
		
			}
			elseif($res['result']['status'] == "left"){
		
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => $text[$langcode][77], 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
		
			} else {
			
			$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/answerCallbackQuery');  
			curl_setopt($ch, CURLOPT_POST, 1);  
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('callback_query_id' => $data['callback_query']['id'], 'text' => $text[$langcode][77], 'show_alert' => 1, 'cache_time' => 0));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$res = curl_exec($ch);
			curl_close($ch);
			
			}			
							
		}
	
	}else{
	
		$str5select = "SELECT `action` FROM `temp_sess` WHERE `chatid`='$chat_id' ORDER BY `rowid` DESC LIMIT 1";
		$result5 = mysqli_query($link, $str5select);
		$row5 = @mysqli_fetch_object($result5);
		
		if(preg_match("/addwallet/", $row5->action)){	
		
			if(strlen(trim($data['message']['text'])) < 15){
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][43],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');		
			}else{
			
				clean_temp_sess();
				$verifcode = rand_string(25);
			
				$str2ins = "INSERT INTO `wallets` (`chatid`,`wallet`,`verified`,`verifcode`) VALUES ('$chat_id','".trim($data['message']['text'])."','0', '$verifcode')";
				mysqli_query($link, $str2ins);	
			
				#walletslist("Кошелек добавлен!");

				$walletid = mysqli_insert_id($link); 
				walletactions($walletid);
			
			}
		}
		elseif(preg_match("/wait4wallet/", $row5->action)){

			if(strlen(trim($data['message']['text'])) < 20){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][101],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');				
				
			}else{
			
			//Wallet verify
			$walletno = trim($data['message']['text']);
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);

			if($res['ok'] == false){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][102],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
							
			} else {
				
				$str2upd = "UPDATE `users` SET `wallet`='$walletno' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);	
				
				sendMeSumTON();				
			
			}
		
			}	
		}
		elseif(preg_match("/wait4tonsum/", $row5->action)){
			
			if(!is_numeric(trim($data['message']['text']))){

				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][103],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
			}
			elseif($data['message']['text'] < 100){

				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][104],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');		
				
			}
			elseif(!is_int($data['message']['text'] / 100)){

				$response = array(
					'chat_id' => $chat_id, 
					'text' => $text[$langcode][105],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');					

			}else{
				
				$tonsum = trim($data['message']['text']);				
				clean_temp_sess();
				choosePayMethod($tonsum);
				
				$suminton = $tonsum * $tegrotonrate;
				$tomeswal = str_replace("%suminton%", $suminton, $text[$langcode][107]);
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $tomeswal,
					'parse_mode' => 'HTML');	
				#sendit($response, 'sendMessage');				
			
			}
							
		}
		elseif(preg_match("/walletfor_nft|wal4cat_nft|wal4dog_nft|wal4cust_nft/", $row5->action)){
			
			$walletno = trim($data['message']['text']);

/*			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://api.ton.sh/getAddressInformation");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,
						"address=".$walletno);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));*/
			
			$dat = array(
				'address' => $walletno
			);
			
			//parsing
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://toncenter.com/api/v2/getAddressInformation?".http_build_query($dat));
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'X-API-Key: '.$toncenterAPIKey));			
			
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$res = json_decode($server_output, true);
			
			if($res['ok'] == false){
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "❌ ".$text[$langcode][57],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
							
			} else {
				
				$str15select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
				$result15 = mysqli_query($link, $str15select);
				if(mysqli_num_rows($result15) == 0){
					$nftbalance = 0;
				}else{
					$row15 = @mysqli_fetch_object($result15);
					$nftbalance = $row15->nft_balance;
					$catbalance = ($row15->cat) ? $row15->cat : 0;
					$dogbalance = ($row15->dog) ? $row15->dog : 0;
					$custbalance = ($row15->cust) ? $row15->cust : 0;
				}
				
				
				$tomesnft = str_replace("%NFTBalance%", $nftbalance, $text[$langcode][58]);
				#$tomesnft = str_replace("%NFTWallet%", $verifyrecepient, $tomesnft);
				
				$prefix = "";
				if($row5->action == 'wal4cat_nft'){
					$tomesnft = str_replace("%NFTBalance%", $catbalance, $text[$langcode][69]);
					$prefix = "cat;";
				}
				elseif($row5->action == 'wal4dog_nft'){
					$tomesnft = str_replace("%NFTBalance%", $dogbalance, $text[$langcode][70]);
					$prefix = "dog;";
				}
				elseif($row5->action == 'wal4cust_nft'){
					$tomesnft = str_replace("%NFTBalance%", $custbalance, $text[$langcode][79]);
					$prefix = "blogger;";
				}				

				$tomesnft = str_replace("%NFTWallet%", $verifyrecepient, $tomesnft);
				$tomesnft = str_replace("%1Cat%", $nftCatRate, $tomesnft);
				$tomesnft = str_replace("%2Cat%", $nftCatRate*2, $tomesnft);
				$tomesnft = str_replace("%3Cat%", $nftCatRate*3, $tomesnft);
				$tomesnft = str_replace("%10Cat%", $nftCatRate*10, $tomesnft);												
				$tomesnft = str_replace("%1Dog%", $nftDogRate, $tomesnft);
				$tomesnft = str_replace("%2Dog%", $nftDogRate*2, $tomesnft);
				$tomesnft = str_replace("%3Dog%", $nftDogRate*3, $tomesnft);
				$tomesnft = str_replace("%10Dog%", $nftDogRate*10, $tomesnft);
				$tomesnft = str_replace("%1Cust%", $nftCustRate, $tomesnft);
				$tomesnft = str_replace("%2Cust%", $nftCustRate*2, $tomesnft);
				$tomesnft = str_replace("%3Cust%", $nftCustRate*3, $tomesnft);
				$tomesnft = str_replace("%5Cust%", $nftCustRate*5, $tomesnft);
				$tomesnft = str_replace("%10Cust%", $nftCustRate*10, $tomesnft);																

				$nftcode = $prefix.rand_string(20);
			
				$str2upd = "UPDATE `users` SET `nftcode`='".$nftcode."' WHERE `chatid`='$chat_id'";
				mysqli_query($link, $str2upd);				
				$tomesnft = str_replace("%NFTCode2%", $nftcode, $tomesnft);

				$messageNFTparts = explode("%NFTCode%", $tomesnft);
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "💎 ".$messageNFTparts[0],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => "<code>".$nftcode."</code>",
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');
				
				$response = array(
					'chat_id' => $chat_id, 
					'text' => $messageNFTparts[1],
					'parse_mode' => 'HTML');	
				sendit($response, 'sendMessage');								
				
				$arInfo["inline_keyboard"][0][0]["callback_data"] = "checkpay4nft".$walletno;
				$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][60];
				send($chat_id, $text[$langcode][59], $arInfo); 	
				
				clean_temp_sess();								
				
			}	
			

		
	}
	
	}


} // if-else /start
 
exit('ok'); //Обязательно возвращаем "ok", чтобы телеграмм не подумал, что запрос не дошёл

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

function clean_temp_sess(){
	global $chat_id, $link;
	
	$str2del = "DELETE FROM `temp_sess` WHERE `chatid` = '$chat_id'";
	mysqli_query($link, $str2del);
}

function walletslist($message){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$message = ($message != '') ? $message : $text[$langcode][44];
	
	$str2select = "SELECT * FROM `wallets` WHERE `chatid`='$chat_id' ORDER BY `rowid`";
	$result = mysqli_query($link, $str2select);
	$r = 0;
	while($row = @mysqli_fetch_object($result)){
		$ql = ($row->verified == 1) ? " ✅" : "";
		$start = substr($row->wallet, 0, 5);
		$end = substr($row->wallet, -5);		
		$arInfo["inline_keyboard"][$r][0]["callback_data"] = "walletnum".$row->rowid;
		$arInfo["inline_keyboard"][$r][0]["text"] = " Изменить / Change ".$start."...".$end.$ql;
		$r++;
	}  // end WHILE MySQL
		$arInfo["inline_keyboard"][$r][0]["callback_data"] = 1;
		$arInfo["inline_keyboard"][$r][0]["text"] = $text[$langcode][14];	
	send($chat_id, $message, $arInfo); 		
	
}

function walletactions($walletid){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$str2select = "SELECT * FROM `wallets` WHERE `rowid`='$walletid'";
	$result = mysqli_query($link, $str2select);	
	$row = @mysqli_fetch_object($result);
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "goverify".$walletid;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][45];		
	$arInfo["inline_keyboard"][0][1]["callback_data"] = "showbalance".$walletid;
	$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][46];
	$arInfo["inline_keyboard"][1][0]["callback_data"] = "showtransactions".$walletid;
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][47];
	$arInfo["inline_keyboard"][1][1]["callback_data"] = "deletewallet".$walletid;
	$arInfo["inline_keyboard"][1][1]["text"] = $text[$langcode][48];		
	send($chat_id, "<code>".$row->wallet."</code>", $arInfo); 		
		
}

function getwallet($walletid){
	global $chat_id, $link;
	
	$str2select = "SELECT * FROM `wallets` WHERE `rowid`='$walletid'";
	$result = mysqli_query($link, $str2select);	
	$row = @mysqli_fetch_object($result);
	
	return $row->wallet;
}

function verify($walletid){
	global $chat_id, $link, $verifyrecepient, $lang, $text, $langcode;
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][49],
		'parse_mode' => 'HTML',
		'disable_web_page_preview' => True);	
	sendit($response, 'sendMessage');		
	
	$respmess = $text[$langcode][50];
	$respmess = str_replace("%verifyrecepient%", $verifyrecepient, $respmess);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $respmess,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');		
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][51],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');		

	$str2select = "SELECT * FROM `wallets` WHERE `rowid`='$walletid'";
	$result = mysqli_query($link, $str2select);	
	$row = @mysqli_fetch_object($result);
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $row->verifcode,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');					
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "chckver".$walletid;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][30];		
	$url = 'ton://transfer/'.$verifyrecepient.'?&text='.$row->verifcode;
	$arInfo["inline_keyboard"][0][1]["text"] = "Go to Tonkeeper";
	$arInfo["inline_keyboard"][0][1]["url"] = rawurldecode($url);	
	send($chat_id, $text[$langcode][52], $arInfo); 	
	
}
function rand_string( $length ) {

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);

}

function mainmenu($premessage){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$arInfo["keyboard"][0][0]["text"] = "️🪙 Pre-sale TGR";
	$arInfo["keyboard"][0][1]["text"] = "💎 ".$text[$langcode][2];
	#$arInfo["keyboard"][0][1]["text"] = "🖼 ".$text[$langcode][55];
	#$arInfo["keyboard"][1][0]["text"] = "🪙 Pre-sale Token";
	$arInfo["keyboard"][1][0]["text"] = "💹 ".$text[$langcode][5];
	#$arInfo["keyboard"][2][0]["text"] = "👥 ".$text[$langcode][6];
	$arInfo["keyboard"][1][1]["text"] = "💳 ".$text[$langcode][3];
	$arInfo["keyboard"][2][0]["text"] = "⚙️ ".$text[$langcode][54];		
	$arInfo["keyboard"][2][1]["text"] = "🛠 ".$text[$langcode][106];
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $premessage.$text[$langcode][10].':👇', $arInfo); 	
	
}

function instmenu(){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$arInfo["keyboard"][0][0]["text"] = "🔥 ".$text[$langcode][71];
	#$arInfo["keyboard"][0][1]["text"] = "⛏ ".$text[$langcode][8];	
	$arInfo["keyboard"][1][0]["text"] = "↩️ ".$text[$langcode][13];		
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $text[$langcode][10].':👇', $arInfo); 	
	
}

function settingsMenu($message){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$arInfo["keyboard"][0][0]["text"] = "💭 ".$text[$langcode][9];
	$arInfo["keyboard"][0][1]["text"] = "💸 ".$text[$langcode][7];
	$arInfo["keyboard"][1][0]["text"] = "📨 ".$text[$langcode][63];
	$arInfo["keyboard"][1][1]["text"] = "🎁 ".$text[$langcode][4];	
	$arInfo["keyboard"][2][0]["text"] = "↩️ ".$text[$langcode][13];	
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $text[$langcode][10].'👇', $arInfo); 	
	
}


function miningMenu($message){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$arInfo["keyboard"][0][0]["text"] = "💰 ".$text[$langcode][11];
	$arInfo["keyboard"][1][0]["text"] = "💹 ".$text[$langcode][12];
	$arInfo["keyboard"][2][0]["text"] = "↩️ ".$text[$langcode][13];
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $message.$text[$langcode][10].'👇', $arInfo); 	
	
}

function NFTMenu($message){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$arInfo["keyboard"][0][0]["text"] = "🐱 ".$text[$langcode][66];
	$arInfo["keyboard"][0][1]["text"] = "🐶 ".$text[$langcode][67];
	$arInfo["keyboard"][1][0]["text"] = "🔞 ".$text[$langcode][78];	
	$arInfo["keyboard"][1][1]["text"] = "↩️ ".$text[$langcode][13];
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $message.$text[$langcode][10].'👇', $arInfo); 	
	
}

function setLangiage(){
	global $chat_id, $lang, $text, $langcode;

	$arInfo["inline_keyboard"][0][0]["callback_data"] = 100;
	$arInfo["inline_keyboard"][0][0]["text"] = $lang[0];
	$arInfo["inline_keyboard"][1][0]["callback_data"] = 101;
	$arInfo["inline_keyboard"][1][0]["text"] = $lang[1]; 
	send($chat_id, hex2bin('F09F92AD')." ".$text[$langcode][0].":", $arInfo); 	
}

function langCode($langcode){
	if($langcode > 12) $langcode = 0;
	return $langcode;
}

function delMessage($mid, $cid){
	global $chat_id;
		if($mid != ''){
			$message_id = $mid-1;
		}
		elseif($cid != ''){
			$message_id = $cid;
		}

		$ch2 = curl_init('https://api.telegram.org/bot' . TOKEN . '/deleteMessage');  
		curl_setopt($ch2, CURLOPT_POST, 1);  
		curl_setopt($ch2, CURLOPT_POSTFIELDS, array('chat_id' => $chat_id, 'message_id' => $message_id));
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch2, CURLOPT_HEADER, false);
		$res2 = curl_exec($ch2);
		curl_close($ch2);		
}

function TONMenu(){
	global $chat_id, $link, $lang, $text, $langcode;
	
	$arInfo["keyboard"][0][0]["text"] = "Tegro (TON)";
	#$arInfo["keyboard"][0][1]["text"] = "Tegro (BEP20)";
	$arInfo["keyboard"][0][1]["text"] = "The Token";	
	$arInfo["keyboard"][1][0]["text"] = "↩️ Back";
	$arInfo["resize_keyboard"] = TRUE;
	send($chat_id, $text[$langcode][80].' 👇', $arInfo); 	
	
}

function processWallet(){
	global $chat_id, $link, $langcode, $text;
	
	$str2select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result = mysqli_query($link, $str2select);
	$row = @mysqli_fetch_object($result);
	
	if(strlen($row->wallet) > 10){
		$toButton = str_replace("%walletno%", $row->wallet, $text[$langcode][81]);	
		
		$arInfo["inline_keyboard"][0][0]["callback_data"] = 3;
		$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][82];
		$arInfo["inline_keyboard"][0][1]["callback_data"] = 4;
		$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][83];				
		send($chat_id, $toButton, $arInfo);
		 		
	}else{
		
		processWallet2();
		
	}
	
}

function processWallet2(){
	global $chat_id, $link, $langcode, $text;

	clean_temp_sess();
	save2temp("action", "wait4wallet");
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][84],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	

}

function save2temp($field, $val){
	global $link, $chat_id;
	$curtime = time();
	
	$str2ins = "INSERT INTO `temp_sess` (`chatid`,`$field`) VALUES ('$chat_id','$val')";
	mysqli_query($link, $str2ins);	

}

function sendMeSumTON(){
	global $chat_id, $link, $langcode, $text;
	
	clean_temp_sess();
	save2temp("action", "wait4tonsum");
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $text[$langcode][85],
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');	
	
}

function choosePayMethod($sum){
	global $chat_id, $link, $langcode, $text, $tegrotonrate,$CryptoPayAPIToken;
	
	$suminton = $sum * $tegrotonrate;
################# PREPARE FOR CRYPTO BOT #######################
	$ctime = time();
	$payload = $chat_id;
	$data = array("asset"=>"TON", "amount"=>$suminton, "payload"=>$payload, "paid_btn_name"=>"callback", "paid_btn_url"=>"https://t.me/TegroTonBot");
	
	$prop = http_build_query($data);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://pay.crypt.bot/api/createInvoice?".$prop);
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json', 'Crypto-Pay-API-Token: '.$CryptoPayAPIToken));
	
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$server_output = curl_exec ($ch);
	curl_close ($ch);
	$res = json_decode($server_output, true);		
################# PREPARE FOR CRYPTO BOT #######################
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "payonsite".$suminton;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][86];
	$arInfo["inline_keyboard"][0][1]["callback_data"] = "paybyton".$suminton;
	$arInfo["inline_keyboard"][0][1]["text"] = $text[$langcode][87]; 
	$url22 = $res['result']['pay_url'];
	$arInfo["inline_keyboard"][1][0]["url"] = rawurldecode($url22);	
	$arInfo["inline_keyboard"][1][0]["text"] = $text[$langcode][88]; 	
	send($chat_id, $text[$langcode][89], $arInfo); 	
}

function makelink($sum){
	global $link, $chat_id, $roskassa_publickey, $roskassa_secretkey;
	
	$curtime = time();
	$str2ins = "INSERT INTO `paylinks` (`chatid`,`times`,`status`,`sum`) VALUES ('$chat_id','$curtime','0','$sum')";
	mysqli_query($link, $str2ins);
	$last_id = mysqli_insert_id($link);
	
	$secret = $roskassa_secretkey;
	$data = array(
		'shop_id'=>$roskassa_publickey,
		'amount'=>$sum,
		'currency'=>'TON',
		'order_id'=>$chat_id
		#'test'=>1
	);
	ksort($data);
	$str = http_build_query($data);
	$sign = md5($str . $secret);
	
	return 'https://tegro.money/pay/?'.$str.'&sign='.$sign;
	
}

function messageIfPayByTON($sum){
	global $chat_id, $link, $langcode, $text, $NFTwalletTON, $tegrotonrate;
	
	$str20select = "SELECT `wallet` FROM `users` WHERE `chatid`='$chat_id'";
	$result20 = mysqli_query($link, $str20select);
	$row20 = @mysqli_fetch_object($result20);
	$walletno = $row20->wallet;
	
	$nftcode = "tegroton;".rand_string(20);
	$str2upd = "UPDATE `users` SET `nftcode`='$nftcode' WHERE `chatid`='$chat_id'";
	mysqli_query($link, $str2upd);				

	$suminnanoton = $sum * 1000000000;
	$suminton = $sum;

	$tomessage = str_replace("%NFTwallet%", $NFTwalletTON, $text[$langcode][90]);
	$tomessage = str_replace("%NFTcode%", $nftcode, $tomessage);
	$tomessage = str_replace("%casename%", $casename, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminton, $tomessage);	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');

	$tomessage = str_replace("%NFTwallet%", $NFTwalletTON, $text[$langcode][91]);
	$tomessage = str_replace("%nftcode%", $nftcode, $tomessage);	
	$tomessage = str_replace("%suminton%", $suminnanoton, $tomessage);				
	
	unset($arInfo);
	$arInfo["inline_keyboard"][0][0]["callback_data"] = "chkp".$walletno."|".$sum;
	$arInfo["inline_keyboard"][0][0]["text"] = $text[$langcode][92];
	#send($chat_id, $tomessage, $arInfo); 	
	
	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');									
				
}

function send2($method, $request)
{

	$ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/' . $method);
	curl_setopt_array($ch,
		[
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($request),
			CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			CURLOPT_SSL_VERIFYPEER => false,
		]
	);
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}
	
function uuid()
{
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),

		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
	);
}	

function checkInlineQuery()
{
	global $langcode, $text;	
	$request = json_decode(file_get_contents('php://input'));

	if (isset($request->inline_query))
	{
		
		$chatid = $request->inline_query->from->id;
		
		#file_put_contents('debug', print_r($request, true) . PHP_EOL . json_encode($request) . PHP_EOL . $result . PHP_EOL, FILE_APPEND);
		
		// https://core.telegram.org/bots/api#answerinlinequery
		send2('answerInlineQuery',
			[
				'inline_query_id' => $request->inline_query->id,

				// InlineQueryResult https://core.telegram.org/bots/api#inlinequeryresult
				'results' =>
				[
					[
						// InlineQueryResultArticle https://core.telegram.org/bots/api#inlinequeryresultarticle
						'type' => 'article',
						'id' => uuid(),
						// 'id' => 0,
						'title' => $text[$langcode][110],
						'description' => $text[$langcode][113],
						'thumb_url' => 'https://smoservice.vc/TGBot/avatar.JPG',

						// InputMessageContent https://core.telegram.org/bots/api#inputmessagecontent
						'input_message_content' =>
						[
							// InputTextMessageContent https://core.telegram.org/bots/api#inputtextmessagecontent
							'message_text' => $text[$langcode][111],
						],

						// InlineKeyboardMarkup https://core.telegram.org/bots/api#inlinekeyboardmarkup
						'reply_markup' =>
						[
							'inline_keyboard' =>
							[
								// InlineKeyboardButton https://core.telegram.org/bots/api#inlinekeyboardbutton
								[
									[
										'text' => $text[$langcode][112],
										'url' => 'https://t.me/tegrotonbot?start='.$chatid,
									],
								],
							],
						],
					],
				],
			]
		);
	}
}
