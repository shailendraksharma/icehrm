<?php
define('CLIENT_PATH',dirname(__FILE__));
include ("include.common.php");
$modulePath = getSessionObject("modulePath");
if(!defined('MODULE_PATH')){
	define('MODULE_PATH',$modulePath);
}
include("server.includes.inc.php");
if(empty($user)){
	$ret['status'] = "ERROR";
	echo json_encode($ret);
	exit();
}

$_REQUEST['sm'] = fixJSON($_REQUEST['sm']);
$_REQUEST['cl'] = fixJSON($_REQUEST['cl']);
$_REQUEST['ft'] = fixJSON($_REQUEST['ft']);


$columns = json_decode($_REQUEST['cl'],true);
$columns[]="id";
$table = $_REQUEST['t'];
$obj = new $table();


$sLimit = "";
if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' ){
	$sLimit = " LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".intval( $_REQUEST['iDisplayLength'] );
}

$isSubOrdinates = false;
if(isset($_REQUEST['type']) && $_REQUEST['type']="sub"){
	$isSubOrdinates = true;
}

$skipEmployeeRestriction = false;
if(isset($_REQUEST['skip']) && $_REQUEST['type']="1"){
	$skipEmployeeRestriction = true;
}

$data = $baseService->getData($_REQUEST['t'],$_REQUEST['sm'],$_REQUEST['ft'],$_REQUEST['ob'],$sLimit, $_REQUEST['cl'], $_REQUEST['sSearch'],$isSubOrdinates,$skipEmployeeRestriction);

//Get Total row count
$totalRows = 0;

$countFilterQuery = "";
$countFilterQueryData = array();
if(!empty($_REQUEST['ft'])){
	$filter = json_decode($_REQUEST['ft']);
	if(!empty($filter)){
		foreach($filter as $k=>$v){
			$countFilterQuery.=" and ".$k."=?";
			$countFilterQueryData[] = $v;
		}
	}
}


if(in_array($table, $baseService->userTables) && !$skipEmployeeRestriction && !$isSubOrdinates){
	$cemp = $baseService->getCurrentEmployeeId();
	$sql = "Select count(id) as count from ".$obj->_table." where employee = ? ".$countFilterQuery;
	array_unshift($countFilterQueryData,$cemp);
	
	$rowCount = $obj->DB()->Execute($sql, $countFilterQueryData);
			
}else{
	if($isSubOrdinates){
		$cemp = $baseService->getCurrentEmployeeId();
		$subordinate = new Employee();
		$subordinates = $subordinate->Find("supervisor = ?",array($cemp));
		$subordinatesIds = "";
		foreach($subordinates as $sub){
			if($subordinatesIds != ""){
				$subordinatesIds.=",";
			}
			$subordinatesIds.=$sub->id;
		}
		$subordinatesIds.="";
		$sql = "Select count(id) as count from ".$obj->_table." where employee in (".$subordinatesIds.") ".$countFilterQuery;
		$rowCount = $obj->DB()->Execute($sql,$countFilterQueryData);
	}else{
		$sql = "Select count(id) as count from ".$obj->_table;
		if(!empty($countFilterQuery)){
			$sql.=" where 1=1 ".$countFilterQuery;
		}
		
		$rowCount = $obj->DB()->Execute($sql,$countFilterQueryData);
	}
	
}

foreach ($rowCount as $cnt) {
	$totalRows = $cnt['count'];
}	

/*
 * Output
 */

$output = array(
	"sEcho" => intval($_REQUEST['sEcho']),
	"iTotalRecords" => $totalRows,
	"iTotalDisplayRecords" => $totalRows,
	"aaData" => array()
);

/*
$output['debug_data'] = print_r($data,true);
$output['debug_col'] = print_r($columns,true);
$output['debug_col_plain'] = $_REQUEST['cl'];
$output['get_magic_quotes_gpc'] = get_magic_quotes_gpc();
*/

foreach($data as $item){
	$row = array();
	$colCount = count($columns);
	for ($i=0 ; $i<$colCount;$i++){
		$row[] = $item->$columns[$i];
	}
	$row["_org"] = $baseService->cleanUpAdoDB($item);
	$output['aaData'][] = $row;
}
echo json_encode($output);
