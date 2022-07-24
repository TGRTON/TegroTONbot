<?php 
function acceptton($gotTON, $senderid){
	global $chat_id, $link, $langcode, $text, $NFTRefPercent, $tegrotonrate;
	
	$sumInTegroTON = $gotTON / $tegrotonrate;
	
	$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result16 = mysqli_query($link, $str16select);
		if(mysqli_num_rows($result16) == 0 ){

			$str2ins = "INSERT INTO `nft` (`chatid`,`tegroton`) VALUES ('$chat_id','$sumInTegroTON')";				
			mysqli_query($link, $str2ins);

		}else{
			$row16 = @mysqli_fetch_object($result16);
			$oldsum = $row16->tegroton;

			$newsum = $oldsum + $sumInTegroTON;	
			$str11upd = "UPDATE `nft` SET `tegroton`='".$newsum."' WHERE `chatid`='$chat_id'";
			mysqli_query($link, $str11upd);
							
		}

	########## REF FEE ##########
	$str12select = "SELECT * FROM `users` WHERE `chatid`='$chat_id'";
	$result12 = mysqli_query($link, $str12select);
	$row12 = @mysqli_fetch_object($result12);	
	
	$earnRefNFT = $gotTON / 100 * $NFTRefPercent;
	
	if($row12->ref > 1){
		$str10upd = "UPDATE `users` SET `refbalance`=`refbalance`+$earnRefNFT WHERE `chatid`='".$row12->ref."'";
		mysqli_query($link, $str10upd);	
	}
	########## REF FEE ##########		
	
	$str16select = "SELECT * FROM `nft` WHERE `chatid`='$chat_id'";
	$result16 = mysqli_query($link, $str16select);
	$row16 = @mysqli_fetch_object($result16);
	
/*	$response = array(
		'chat_id' => $chat_id, 
		'text' => "�");	
	sendit($response, 'sendMessage');*/
	
	$tomessage = str_replace("%nft_balance%", $row16->tegroton, "Ваш платеж получен и зачислен на ваш баланс. Баланс: %nft_balance% Tegro TON");
	
	$arInfo["inline_keyboard"][0][0]["callback_data"] = 251;
	$arInfo["inline_keyboard"][0][0]["text"] = "<< Назад";
	send($chat_id, $tomessage, $arInfo); 
	
/*	$response = array(
		'chat_id' => $chat_id, 
		'text' => $tomessage,
		'parse_mode' => 'HTML');	
	sendit($response, 'sendMessage');*/
	
	######## SAVE TRANSACTION ###########
	$date_time = date("j-m-Y G:i");
	$str2ins = "INSERT INTO `transactions` (`chatid`,`sender`,`date_time`,`tegroton`) VALUES ('$chat_id','$senderid','$date_time','$sumInTegroTON')";
	mysqli_query($link, $str2ins);
	######## SAVE TRANSACTION ###########											
	
}
?>