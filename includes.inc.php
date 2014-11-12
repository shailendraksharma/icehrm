<?php
include ("include.common.php");
if(defined('MODULE_PATH')){
	saveSessionObject("modulePath", MODULE_PATH);
}
define('CLIENT_PATH',dirname(__FILE__));
include (CLIENT_PATH."/include.common.php");
include (CLIENT_PATH."/server.includes.inc.php");
$user = getSessionObject('user');

$employeeCurrent = null;
$employeeSwitched = null;
if(!empty($user->employee)){
	$employeeCurrent = $baseService->getElement('Employee',$user->employee, null, true);	
	if(!empty($employeeCurrent)){
		$employeeCurrent = $fileService->updateEmployeeImage($employeeCurrent);
	}
}
if($user->user_level == 'Admin' || $user->user_level == 'Manager'){
	$switchedEmpId = $baseService->getCurrentEmployeeId();
	if($switchedEmpId != $user->employee && !empty($switchedEmpId)){
		$employeeSwitched = $baseService->getElement('Employee',$switchedEmpId, null, true);
		if(!empty($employeeSwitched)){
			$employeeSwitched = $fileService->updateEmployeeImage($employeeSwitched);
		}	
	}
}

$activeEmployee = null;
if(!empty($employeeSwitched)){
	$activeEmployee = $employeeSwitched;
}else{
	$activeEmployee = $employeeCurrent;
}








//read field templates
$fieldTemplates = array();
$fieldTemplates['hidden'] = file_get_contents(CLIENT_PATH.'/templates/fields/hidden.html');
$fieldTemplates['text'] = file_get_contents(CLIENT_PATH.'/templates/fields/text.html');
$fieldTemplates['textarea'] = file_get_contents(CLIENT_PATH.'/templates/fields/textarea.html');
$fieldTemplates['select'] = file_get_contents(CLIENT_PATH.'/templates/fields/select.html');
$fieldTemplates['select2'] = file_get_contents(CLIENT_PATH.'/templates/fields/select2.html');
$fieldTemplates['date'] = file_get_contents(CLIENT_PATH.'/templates/fields/date.html');
$fieldTemplates['datetime'] = file_get_contents(CLIENT_PATH.'/templates/fields/datetime.html');
$fieldTemplates['time'] = file_get_contents(CLIENT_PATH.'/templates/fields/time.html');
$fieldTemplates['fileupload'] = file_get_contents(CLIENT_PATH.'/templates/fields/fileupload.html');
$fieldTemplates['label'] = file_get_contents(CLIENT_PATH.'/templates/fields/label.html');
$fieldTemplates['placeholder'] = file_get_contents(CLIENT_PATH.'/templates/fields/placeholder.html');

$templates = array();
$templates['formTemplate'] = file_get_contents(CLIENT_PATH.'/templates/form_template.html');
$templates['filterTemplate'] = file_get_contents(CLIENT_PATH.'/templates/filter_template.html');


//include module templates

if(file_exists(MODULE_PATH.'/templates/fields/hidden.html')){
	$fieldTemplates['hidden'] = file_get_contents(MODULE_PATH.'/templates/fields/hidden.html');	
}
if(file_exists(MODULE_PATH.'/templates/fields/text.html')){
	$fieldTemplates['text'] = file_get_contents(MODULE_PATH.'/templates/fields/text.html');	
}
if(file_exists(MODULE_PATH.'/templates/fields/textarea.html')){
	$fieldTemplates['textarea'] = file_get_contents(MODULE_PATH.'/templates/fields/textarea.html');	
}
if(file_exists(MODULE_PATH.'/templates/fields/select.html')){
	$fieldTemplates['select'] = file_get_contents(MODULE_PATH.'/templates/fields/select.html');	
}
if(file_exists(MODULE_PATH.'/templates/fields/date.html')){
	$fieldTemplates['date'] = file_get_contents(MODULE_PATH.'/templates/fields/date.html');	
}
if(file_exists(MODULE_PATH.'/templates/fields/time.html')){
	$fieldTemplates['time'] = file_get_contents(MODULE_PATH.'/templates/fields/time.html');	
}

if(file_exists(MODULE_PATH.'/templates/fields/fileupload.html')){
	$fieldTemplates['fileupload'] = file_get_contents(MODULE_PATH.'/templates/fields/fileupload.html');
}

if(file_exists(MODULE_PATH.'/templates/fields/label.html')){
	$fieldTemplates['label'] = file_get_contents(MODULE_PATH.'/templates/fields/label.html');
}

if(file_exists(MODULE_PATH.'/templates/fields/placeholder.html')){
	$fieldTemplates['placeholder'] = file_get_contents(MODULE_PATH.'/templates/fields/placeholder.html');
}

if(file_exists(MODULE_PATH.'/templates/form_template.html')){
	$templates['orig_formTemplate'] = $templates['formTemplate'];
	$templates['formTemplate'] = file_get_contents(MODULE_PATH.'/templates/form_template.html');	
}

//Read module custom templates
$customTemplates = array();
if(is_dir(MODULE_PATH.'/customTemplates/')){
	$ams = scandir(MODULE_PATH.'/customTemplates/');
	foreach($ams as $am){
		if(!is_dir(MODULE_PATH.'/customTemplates/'.$am) && $am != '.' && $am != '..'){
			$customTemplates[$am] = file_get_contents(MODULE_PATH.'/customTemplates/'.$am);	
		}	
	}
}


