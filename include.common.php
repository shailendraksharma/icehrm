<?php
error_reporting(E_ERROR);
$jsVersion = '7.0.1';
$cssVersion = '7.0.1';
if (!function_exists('saveSessionObject')) {
function saveSessionObject($name,$obj){
	session_start();
	$_SESSION[$name.CLIENT_NAME] = json_encode($obj);
	session_write_close();
}
}

if (!function_exists('getSessionObject')) {
function getSessionObject($name){
	session_start();
	if(isset($_SESSION[$name.CLIENT_NAME])){
		$obj = $_SESSION[$name.CLIENT_NAME];
	}
	session_write_close();
	if(empty($obj)){
		return null;
	}
	return json_decode($obj);
}
}

//Find timezone diff with GMT
$dateTimeZoneColombo = new DateTimeZone("Asia/Colombo");
$dateTimeColombo = new DateTime("now", $dateTimeZoneColombo);
$dateTimeColomboStr = $dateTimeColombo->format("Y-m-d H:i:s");
$dateTimeNow = date("Y-m-d H:i:s");

$diffHoursBetweenServerTimezoneWithGMT = (strtotime($dateTimeNow) - (strtotime($dateTimeColomboStr) - 5.5*60*60))/(60*60);

if (!function_exists('fixJSON')) {
	function fixJSON($json){
		global $noJSONRequests;
		if($noJSONRequests."" == "1"){
			$json = str_replace("|",'"',$json);
		}
		return $json;
	}
}