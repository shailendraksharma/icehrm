<?php

//Reset modules if required
if($settingsManager->getSetting("System: Reset Modules and Permissions") == "1"){
	$permissionTemp = new Permission();
	$permissions = $permissionTemp->Find("1=1");
	foreach ($permissions as $permTemp){
		$permTemp->Delete();
	}
	
	$moduleTemp = new Module();
	$modulesTemp = $moduleTemp->Find("1=1");
	foreach ($modulesTemp as $moduleTemp){
		$moduleTemp->Delete();
	}
	
	$settingsManager->setSetting("System: Reset Modules and Permissions","0");
}

$addNewPermissions = false;
if($settingsManager->getSetting("System: Add New Permissions") == "1"){
	$addNewPermissions = true;
	$settingsManager->setSetting("System: Add New Permissions","0");
}

function includeModuleManager($type,$name){
	global $moduleManagers;
	$moduleCapsName = ucfirst($name);
	$moduleTypeCapsName = ucfirst($type); // Admin or Modules
	$incFile = CLIENT_PATH.'/'.$type.'/'.$name.'/api/'.$moduleCapsName.$moduleTypeCapsName."Manager.php";
	
	include ($incFile);
	$moduleManagerClass = $moduleCapsName.$moduleTypeCapsName."Manager";
	$moduleManagers[] = new $moduleManagerClass();
}


function createPermissions($meta, $moduleId){
	$permData = $meta->permissions;
	if(empty($permData)){
		return;
	}	
	
	foreach($permData as $key=>$val){
		if(!empty($val)){
			foreach($val as $permissionString => $defaultValue){
				$permissionObj = new Permission();
				$permissionObj->Load("user_level = ? and module_id = ? and permission = ?", array($key, $moduleId, $permissionString));
				
				if(empty($permissionObj->id) && $permissionObj->module_id == $moduleId){
					
				}else{
					$permissionObj = new Permission();
					$permissionObj->user_level = $key;
					$permissionObj->module_id = $moduleId;
					$permissionObj->permission = $permissionString;
					$permissionObj->value = $defaultValue;
					$permissionObj->meta = '["value", {"label":"Value","type":"select","source":[["Yes","Yes"],["No","No"]]}]';
					$permissionObj->Save();
				}
			}
		}
	}
}

$dbModule = new Module();
$adminDbModules = $dbModule->Find("mod_group = ?",array("admin"));
$userDbModules = $dbModule->Find("mod_group = ?",array("user"));

$adminDBModuleList = array();
foreach($adminDbModules as $dbm){
	$adminDBModuleList[$dbm->name] = $dbm;
}

$userDBModuleList = array();
foreach($userDbModules as $dbm){
	$userDBModuleList[$dbm->name] = $dbm;
}

$adminModulesTemp = array();
$ams = scandir(CLIENT_PATH.'/admin/');
$currentLocation = 0;
foreach($ams as $am){
	if(is_dir(CLIENT_PATH.'/admin/'.$am) && $am != '.' && $am != '..'){
		$meta = json_decode(file_get_contents(CLIENT_PATH.'/admin/'.$am.'/meta.json'));
		includeModuleManager('admin',$am);
		$arr = array();
		$arr['name'] = $am;	
		$arr['label'] = $meta->label;	
		$arr['menu'] = $meta->menu;	
		$arr['order'] = $meta->order;
		$arr['user_levels'] = $meta->user_levels;
		
		//Check in admin dbmodules
		if(isset($adminDBModuleList[$arr['label']])){
			$dbModule = $adminDBModuleList[$arr['label']];
			
			if($addNewPermissions && isset($meta->permissions)){
				createPermissions($meta, $dbModule->id);
			}
			
			if($dbModule->status == 'Disabled'){
				continue;	
			}
		}else{
			$dbModule = new Module();
			$dbModule->menu = $arr['menu'];
			$dbModule->name = $arr['label'];
			$dbModule->mod_group = "admin";
			$dbModule->mod_order = $arr['order'];
			$dbModule->status = "Enabled";
			$dbModule->version = isset($meta->version)?$meta->version:"";
			$dbModule->update_path = "admin>".$am;
			$dbModule->user_levels = isset($meta->user_levels)?json_encode($meta->user_levels):"";
			$dbModule->Save();
			
			if(isset($meta->permissions)){
				createPermissions($meta, $dbModule->id);
			}
			
		}
		
		if(!isset($adminModulesTemp[$arr['menu']])){
			$adminModulesTemp[$arr['menu']] = array();	
		}

		if($arr['order'] == '0' || $arr['order'] == ''){
			$adminModulesTemp[$arr['menu']]["Z".$currentLocation] = $arr; 	
			$currentLocation++;
		}else{
			$adminModulesTemp[$arr['menu']]["A".$arr['order']] = $arr;
		}
		
		$moduleCapsName = ucfirst($am);
		$initFile = CLIENT_PATH.'/admin/'.$am."/api/".$moduleCapsName."Initialize.php";
		if(file_exists($initFile)){
			include $initFile;	
			$class = $moduleCapsName."Initialize";
			if(class_exists($class)){
				$initClass = new $class();
				$initClass->setBaseService($baseService);
				$initClass->init();	
			}
			
		}
	}	
}


$userModulesTemp = array();
$ams = scandir(CLIENT_PATH.'/modules/');
foreach($ams as $am){
	if(is_dir(CLIENT_PATH.'/modules/'.$am) && $am != '.' && $am != '..'){
		$meta = json_decode(file_get_contents(CLIENT_PATH.'/modules/'.$am.'/meta.json'));
		includeModuleManager('modules',$am);
		$arr = array();
		$arr['name'] = $am;	
		$arr['label'] = $meta->label;	
		$arr['menu'] = $meta->menu;	
		$arr['order'] = $meta->order;
		$arr['user_levels'] = $meta->user_levels;
		
		//Check in admin dbmodules
		if(isset($userDBModuleList[$arr['label']])){
			$dbModule = $userDBModuleList[$arr['label']];
			
			if($addNewPermissions && isset($meta->permissions)){
				createPermissions($meta, $dbModule->id);
			}
			
			if($dbModule->status == 'Disabled'){
				continue;
			}
		}else{
			$dbModule = new Module();
			$dbModule->menu = $arr['menu'];
			$dbModule->name = $arr['label'];
			$dbModule->mod_group = "user";
			$dbModule->mod_order = $arr['order'];
			$dbModule->status = "Enabled";
			$dbModule->version = isset($meta->version)?$meta->version:"";
			$dbModule->update_path = "modules>".$am;
			$dbModule->user_levels = isset($meta->user_levels)?json_encode($meta->user_levels):"";
			$dbModule->Save();
			
			if(isset($meta->permissions)){
				createPermissions($meta, $dbModule->id);
			}
		}
		
		if(!isset($userModulesTemp[$arr['menu']])){
			$userModulesTemp[$arr['menu']] = array();	
		}

		if($arr['order'] == '0' || $arr['order'] == ''){
			$userModulesTemp[$arr['menu']]["Z".$currentLocation] = $arr; 
			$currentLocation++;	
		}else{
			$userModulesTemp[$arr['menu']]["A".$arr['order']] = $arr;
		}
		
		$moduleCapsName = ucfirst($am);
		$initFile = CLIENT_PATH.'/modules/'.$am."/api/".$moduleCapsName."Initialize.php";
		if(file_exists($initFile)){
			include $initFile;	
			$class = $moduleCapsName."Initialize";
			if(class_exists($class)){
				$initClass = new $class();
				$initClass->setBaseService($baseService);
				$initClass->init();	
			}
			
		}
	}	
}



foreach ($adminModulesTemp as $k=>$v){
	ksort($adminModulesTemp[$k]);	
}


foreach ($userModulesTemp as $k=>$v){
	ksort($userModulesTemp[$k]);	
}


$adminMenus = json_decode(file_get_contents(CLIENT_PATH.'/admin/meta.json'),true);
$adminModules = array();
$added = array();
foreach($adminMenus as $menu){
	if(isset($adminModulesTemp[$menu])){
		$arr = array("name"=>$menu,"menu"=>$adminModulesTemp[$menu]);
		$adminModules[] = $arr;	
		$added[] = $menu;
	}
}


foreach($adminModulesTemp as $k=>$v){
	if(!in_array($k, $added)){
		$arr = array("name"=>$k,"menu"=>$adminModulesTemp[$k]);
		$adminModules[] = $arr;	
	}
}

$userMenus = json_decode(file_get_contents(CLIENT_PATH.'/modules/meta.json'));

$userModules = array();
$added = array();
foreach($userMenus as $menu){
	if(isset($userModulesTemp[$menu])){
		$arr = array("name"=>$menu,"menu"=>$userModulesTemp[$menu]);
		$userModules[] = $arr;
		$added[] = $menu;
	}
}


foreach($userModulesTemp as $k=>$v){
	if(!in_array($k, $added)){
		$arr = array("name"=>$k,"menu"=>$userModulesTemp[$k]);
		$userModules[] = $arr;	
	}
}

//Remove modules having no permissions

foreach($adminModules as $fk => $menu){
	foreach ($menu['menu'] as $key => $item){
		if(!in_array($user->user_level,$item['user_levels'])){
			//unset($menu['menu'][$key]);
			unset($adminModules[$fk]['menu'][$key]);
		}
	}
}

foreach($userModules as $fk => $menu){
	foreach ($menu['menu'] as $key => $item){
		if(!in_array($user->user_level,$item['user_levels'])){
			//unset($menu['menu'][$key]);
			unset($userModules[$fk]['menu'][$key]);
		}	
	}
}

