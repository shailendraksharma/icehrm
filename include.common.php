<?php
error_reporting(E_ERROR);
$jsVersion = '8.1';
$cssVersion = '8.1';

if (!function_exists('cleanParameters')) {
	function cleanParameters($input){
		foreach($input as $key => $value) {
			$cleaned = cleanParameter($value);
			$input[$key] = $cleaned;
		}
		return $input;
	}
}

if (!function_exists('cleanParameter')) {
	function cleanParameter($val){
		$val = strip_tags($val, TAGS_TO_PRESERVE);
		/*
		 /              # Start Pattern
		<             # Match '<' at beginning of tags
		(             # Start Capture Group $1 - Tag Name
				[a-z]         # Match 'a' through 'z'
				[a-z0-9]*     # Match 'a' through 'z' or '0' through '9' zero or more times
		)             # End Capture Group
		[^>]*?        # Match anything other than '>', Zero or More times, not-greedy (wont eat the /)
		(\/?)         # Capture Group $2 - '/' if it is there
		>             # Match '>'
		/i            # End Pattern - Case Insensitive
		*/
		$val =  preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $val);
		return $val;
	}
}

$_REQUEST = cleanParameters($_REQUEST);
$_GET = cleanParameters($_GET);
$_POST = cleanParameters($_POST);


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