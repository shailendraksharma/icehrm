<?php
class SettingsManager{
	public function getSetting($name){
		
		if(class_exists("ProVersion")){
			$pro = new ProVersion();
			$val =	$pro->getSetting($name);
			if(!empty($val)){
				return $val;
			}
		}
		
		$setting = new Setting();
		$setting->Load("name = ?",array($name));
		if($setting->name == $name){
			return $setting->value;
		}	
		return null;
	}
	
	public function setSetting($name, $value){
		$setting = new Setting();
		$setting->Load("name = ?",array($name));
		if($setting->name == $name){
			$setting->value = $value;
			$setting->Save();
		}
	}
}