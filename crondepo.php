<?php 
include "global.php";
$link = mysqli_connect($hostName, $userName, $password, $databaseName) or die ("Error connect to database");
mysqli_set_charset($link, "utf8");

$curtime = time();
$str2select = "SELECT * FROM `deposits` WHERE `timeend` < '$curtime'";
$result = mysqli_query($link, $str2select);
while($row = @mysqli_fetch_object($result)){
	
	$income = $row->sum * $row->percent / 100;
	$return = $row->sum + $income;
	
	$str2upd = "UPDATE `users` SET `balance`=`balance`+$return WHERE `chatid`='".$row->chatid."'";
	mysqli_query($link, $str2upd);	
	
	$str2del = "DELETE FROM `deposits` WHERE `rowid` = '".$row->rowid."'";
	mysqli_query($link, $str2del);	
	
}  // end WHILE MySQL
?>